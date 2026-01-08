<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class BackupController extends Controller
{
    /**
     * Display a listing of the backups.
     */
    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => $this->formatSize($disk->size($file)),
                    'date' => Carbon::createFromTimestamp($disk->lastModified($file))->toDateTimeString(),
                    'timestamp' => $disk->lastModified($file)
                ];
            }
        }

        // Sort by date desc
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return view('superadmin.settings.backup.index', compact('backups'));
    }

    /**
     * Create a new backup.
     */
    public function store()
    {
        try {
            $filename = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Ensure directory exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $database = env('DB_DATABASE');
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');

            // Construct mysqldump command
            // Note: This assumes mysqldump is in the system path. 
            // In Laragon, it usually is. If not, users might need to configure the path.
            $command = "mysqldump --user=\"{$username}\" --password=\"{$password}\" --host=\"{$host}\" --port=\"{$port}\" \"{$database}\" > \"{$path}\"";

            // For security, password inside command might be visible in process list, but this is a local/controlled env usually.
            // Using --defaults-extra-file is safer but more complex to setup on the fly.

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new Exception("Backup failed with exit code $returnVar. Output: " . implode("\n", $output));
            }

            return redirect()->route('superadmin.settings.backup')->with('success', 'Backup created successfully: ' . $filename);

        } catch (Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (!file_exists($path)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($path);
    }

    /**
     * Delete a backup file.
     */
    public function destroy($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->with('error', 'File not found.');
    }

    /**
     * Restore database from backup.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'nullable|file|mimes:sql,txt', // Uploaded file
            'filename' => 'nullable|string' // Selected from list
        ]);

        try {
            $path = null;

            if ($request->hasFile('backup_file')) {
                // Handle uploaded file
                $file = $request->file('backup_file');
                $filename = 'restore-' . time() . '.sql';
                $path = $file->storeAs('temp-restores', $filename, 'local');
                $fullPath = storage_path('app/' . $path);
            } elseif ($request->filled('filename')) {
                // Handle existing file
                $filename = $request->input('filename');
                $fullPath = storage_path('app/backups/' . $filename);
                if (!file_exists($fullPath)) {
                    throw new Exception("Backup file not found.");
                }
            } else {
                return back()->with('error', 'Please select a file to restore.');
            }

            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $database = env('DB_DATABASE');
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');

            // Construct mysql restore command
            $command = "mysql --user=\"{$username}\" --password=\"{$password}\" --host=\"{$host}\" --port=\"{$port}\" \"{$database}\" < \"{$fullPath}\"";

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            // Clean up temp file if uploaded
            if ($request->hasFile('backup_file') && file_exists($fullPath)) {
                unlink($fullPath);
            }

            if ($returnVar !== 0) {
                throw new Exception("Restore failed with exit code $returnVar. Output: " . implode("\n", $output));
            }

            return redirect()->route('superadmin.settings.backup')->with('success', 'Database restored successfully.');

        } catch (Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Format file size.
     */
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
