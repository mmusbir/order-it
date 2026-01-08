<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LdapService
{
    /**
     * Check if LDAP integration is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) AppSetting::getValue('ldap_enabled', false);
    }

    /**
     * Authenticate user via LDAP
     * 
     * @param string $email User email or username
     * @param string $password User password
     * @return array ['success' => bool, 'message' => string, 'fallback_to_local' => bool, 'user' => ?User]
     */
    public function authenticate(string $email, string $password): array
    {
        // Check if LDAP extension is loaded
        if (!extension_loaded('ldap')) {
            Log::warning('LDAP authentication skipped: PHP LDAP extension not installed');
            return [
                'success' => false,
                'message' => 'LDAP extension not available',
                'fallback_to_local' => true,
                'user' => null,
            ];
        }

        try {
            $server = AppSetting::getValue('ldap_server');
            $port = (int) AppSetting::getValue('ldap_port', 389);
            $baseDn = AppSetting::getValue('ldap_base_dn');
            $isAd = (bool) AppSetting::getValue('ldap_is_ad', false);
            $adDomain = AppSetting::getValue('ldap_ad_domain', '');
            $useTls = (bool) AppSetting::getValue('ldap_use_tls', false);
            $skipSslVerify = (bool) AppSetting::getValue('ldap_ssl_skip_verify', false);
            $authFilter = AppSetting::getValue('ldap_auth_filter', 'sAMAccountName=');
            $passwordSync = (bool) AppSetting::getValue('ldap_password_sync', false);

            if (!$server) {
                Log::warning('LDAP authentication skipped: No LDAP server configured');
                return [
                    'success' => false,
                    'message' => 'LDAP server not configured',
                    'fallback_to_local' => true,
                    'user' => null,
                ];
            }

            Log::info('LDAP authentication attempt', ['email' => $email, 'server' => $server]);

            // Set connection timeout
            putenv('LDAPTLS_REQCERT=never');

            // Connect to LDAP with timeout
            $ldapConn = @ldap_connect($server, $port);
            if (!$ldapConn) {
                Log::error('LDAP connection failed', ['server' => $server, 'port' => $port]);
                return [
                    'success' => false,
                    'message' => 'Could not connect to LDAP server',
                    'fallback_to_local' => true,
                    'user' => null,
                ];
            }

            // Set LDAP options
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 5); // 5 second timeout

            if ($skipSslVerify) {
                ldap_set_option($ldapConn, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
            }

            // Start TLS if enabled
            if ($useTls) {
                if (!@ldap_start_tls($ldapConn)) {
                    $error = ldap_error($ldapConn);
                    Log::error('LDAP STARTTLS failed', ['error' => $error]);
                    @ldap_close($ldapConn);
                    return [
                        'success' => false,
                        'message' => 'LDAP TLS connection failed',
                        'fallback_to_local' => true,
                        'user' => null,
                    ];
                }
            }

            // Extract username from email if needed
            $username = $email;
            if (str_contains($email, '@')) {
                $username = explode('@', $email)[0];
            }

            // Construct user DN for authentication
            if ($isAd && $adDomain) {
                // Active Directory uses UserPrincipalName
                $userDn = str_contains($email, '@') ? $email : $username . '@' . $adDomain;

                // Try direct bind with UPN
                $userBind = @ldap_bind($ldapConn, $userDn, $password);
            } else {
                // Standard LDAP - search for user first using admin bind
                $bindUser = AppSetting::getValue('ldap_username');
                $bindPass = AppSetting::getValue('ldap_password');

                $adminBind = @ldap_bind($ldapConn, $bindUser, $bindPass);
                if (!$adminBind) {
                    $error = ldap_error($ldapConn);
                    Log::error('LDAP admin bind failed', ['error' => $error]);
                    @ldap_close($ldapConn);
                    return [
                        'success' => false,
                        'message' => 'LDAP service unavailable',
                        'fallback_to_local' => true,
                        'user' => null,
                    ];
                }

                // Search for the user
                $searchFilter = "({$authFilter}{$username})";
                $search = @ldap_search($ldapConn, $baseDn, $searchFilter, ['dn', 'mail']);

                if (!$search) {
                    $error = ldap_error($ldapConn);
                    Log::error('LDAP user search failed', ['error' => $error, 'filter' => $searchFilter]);
                    @ldap_close($ldapConn);
                    return [
                        'success' => false,
                        'message' => 'LDAP search failed',
                        'fallback_to_local' => true,
                        'user' => null,
                    ];
                }

                $entries = ldap_get_entries($ldapConn, $search);
                if ($entries['count'] == 0) {
                    Log::info('LDAP user not found', ['username' => $username]);
                    @ldap_close($ldapConn);
                    return [
                        'success' => false,
                        'message' => 'User not found in directory',
                        'fallback_to_local' => true, // User might exist locally but not in LDAP
                        'user' => null,
                    ];
                }

                $userDn = $entries[0]['dn'];

                // Bind as the user to verify password
                $userBind = @ldap_bind($ldapConn, $userDn, $password);
            }

            @ldap_close($ldapConn);

            if (!$userBind) {
                Log::info('LDAP authentication failed: Invalid credentials', ['email' => $email]);
                return [
                    'success' => false,
                    'message' => trans('auth.failed'),
                    'fallback_to_local' => false, // Invalid password - don't fallback to prevent bypass
                    'user' => null,
                ];
            }

            // LDAP auth successful - find or create local user
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Try finding by username part of email
                $user = User::where('email', 'like', $username . '@%')->first();
            }

            if (!$user) {
                Log::warning('LDAP auth successful but user not found in local database', ['email' => $email]);
                return [
                    'success' => false,
                    'message' => 'User not found in system. Please contact administrator.',
                    'fallback_to_local' => false,
                    'user' => null,
                ];
            }

            // Optionally sync password to local database
            if ($passwordSync) {
                $user->password = Hash::make($password);
                $user->save();
                Log::info('LDAP password synced to local database', ['user_id' => $user->id]);
            }

            Log::info('LDAP authentication successful', ['email' => $email, 'user_id' => $user->id]);

            return [
                'success' => true,
                'message' => 'Authentication successful',
                'fallback_to_local' => false,
                'user' => $user,
            ];

        } catch (\Exception $e) {
            Log::error('LDAP authentication exception', [
                'error' => $e->getMessage(),
                'email' => $email,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Authentication service temporarily unavailable',
                'fallback_to_local' => true,
                'user' => null,
            ];
        }
    }

    /**
     * Test LDAP connection without authentication
     */
    public function testConnection(): array
    {
        if (!extension_loaded('ldap')) {
            return [
                'success' => false,
                'message' => 'PHP LDAP extension is not installed',
            ];
        }

        try {
            $server = AppSetting::getValue('ldap_server');
            $port = (int) AppSetting::getValue('ldap_port', 389);

            if (!$server) {
                return ['success' => false, 'message' => 'LDAP server not configured'];
            }

            $ldapConn = @ldap_connect($server, $port);
            if (!$ldapConn) {
                return ['success' => false, 'message' => 'Could not connect to LDAP server'];
            }

            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 5);

            $username = AppSetting::getValue('ldap_username');
            $password = AppSetting::getValue('ldap_password');

            $bind = @ldap_bind($ldapConn, $username, $password);
            @ldap_close($ldapConn);

            if ($bind) {
                return ['success' => true, 'message' => 'LDAP connection successful'];
            } else {
                return ['success' => false, 'message' => 'LDAP bind failed: ' . ldap_error($ldapConn)];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
