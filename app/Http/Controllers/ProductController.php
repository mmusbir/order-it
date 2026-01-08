<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Get view prefix based on user role
     */
    private function getViewPrefix(): string
    {
        return Auth::user()->role === 'superadmin' ? 'superadmin' : 'admin';
    }

    /**
     * Get route prefix based on user role
     */
    private function getRoutePrefix(): string
    {
        return Auth::user()->role === 'superadmin' ? 'superadmin' : 'admin';
    }

    /**
     * Fetch categories from Snipe-IT API
     */
    private function getSnipeitCategories(): array
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return [];
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/categories', ['limit' => 100]);

            if ($response->successful()) {
                return $response->json()['rows'] ?? [];
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return [];
    }

    /**
     * Fetch models from Snipe-IT API
     */
    private function getSnipeitModels(): array
    {
        $enabled = AppSetting::getValue('snipeit_enabled', '0');
        if ($enabled !== '1' && $enabled !== true && $enabled !== 1) {
            return [];
        }

        $url = AppSetting::getValue('snipeit_url');
        $token = AppSetting::getValue('snipeit_token');

        if (!$url || !$token) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get(rtrim($url, '/') . '/api/v1/models', ['limit' => 100]);

            if ($response->successful()) {
                return $response->json()['rows'] ?? [];
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return [];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('specs', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(12);

        $role = Auth::user()->role;

        // Admin/Superadmin sees management view
        if (in_array($role, ['admin', 'superadmin'])) {
            return view($this->getViewPrefix() . '.products.index', compact('products'));
        }

        // Requester sees catalog view
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        $models = \App\Models\AssetModel::active()->orderBy('name')->get();
        $requestTypes = \App\Models\RequestType::active()->orderBy('name')->get();

        return view($this->getViewPrefix() . '.products.create', compact('categories', 'models', 'requestTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specs' => 'required|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'category' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
            'snipeit_model_id' => 'nullable|integer',
            'snipeit_category_id' => 'nullable|integer',
            'request_types' => 'nullable|array',
            'request_types.*' => 'exists:request_types,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if ($file->isValid()) {
                try {
                    $filename = $file->hashName();
                    if (empty($filename)) {
                        $filename = \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
                    }

                    // Use Storage facade explicitly
                    $path = Storage::disk('public')->putFileAs('products', $file, $filename);
                    if (!$path) {
                        throw new \Exception('Storage::putFileAs returned false/empty path.');
                    }
                    $validated['image'] = $path;
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Image upload failed (store): ' . $e->getMessage());
                    // Continue without image or handle error as needed
                }
            }
        }

        // Remove request_types from validated data before creating product
        $requestTypeIds = $validated['request_types'] ?? [];
        unset($validated['request_types']);

        $product = Product::create($validated);

        // Sync request types
        $product->requestTypes()->sync($requestTypeIds);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_CREATED',
            'description' => "Created product {$product->name}",
            'ip_address' => request()->ip(),
            'subject_type' => get_class($product),
            'subject_id' => $product->id,
        ]);

        return redirect()->route($this->getRoutePrefix() . '.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        $models = \App\Models\AssetModel::active()->orderBy('name')->get();
        $requestTypes = \App\Models\RequestType::active()->orderBy('name')->get();
        $product->load('requestTypes');

        return view($this->getViewPrefix() . '.products.edit', compact('product', 'categories', 'models', 'requestTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Manual file validation before Laravel's validator to catch path errors
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            \Illuminate\Support\Facades\Log::info('File Upload Step 1 - File Received', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'is_valid' => $file->isValid()
            ]);

            // Check if file is valid
            if (!$file || !$file->isValid()) {
                \Illuminate\Support\Facades\Log::error('File validation failed - invalid file');
                return back()->withErrors(['image' => 'File yang diupload tidak valid.'])->withInput();
            }

            // Check file size (2MB = 2048 KB)
            if ($file->getSize() > 2048 * 1024) {
                return back()->withErrors(['image' => 'Ukuran file maksimal 2MB.'])->withInput();
            }

            // Check mime type
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return back()->withErrors(['image' => 'File harus berformat JPG, JPEG, PNG, atau PDF.'])->withInput();
            }
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'specs' => 'required|string',
                // Remove file validation from Laravel validator since we did it manually
                'image' => 'nullable',
                'category' => 'nullable|string|max:255',
                'model_name' => 'nullable|string|max:255',
                'snipeit_model_id' => 'nullable|integer',
                'snipeit_category_id' => 'nullable|integer',
                'request_types' => 'nullable|array',
                'request_types.*' => 'exists:request_types,id',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Validasi gagal: ' . $e->getMessage()])->withInput();
        }


        // Debug logging
        \Illuminate\Support\Facades\Log::info('Product Update Debug', [
            'product_id' => $product->id,
            'has_file' => $request->hasFile('image'),
            'files' => array_keys($request->allFiles())
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (!$file->isValid()) {
                return back()->with('error', 'Uploaded file is not valid.');
            }

            // Safe delete old image
            if (!empty($product->image) && trim($product->image) !== '') {
                try {
                    Storage::disk('public')->delete($product->image);
                } catch (\Exception $e) {
                    // Iterate and ignore deletion errors
                    \Illuminate\Support\Facades\Log::warning('Failed to delete old image: ' . $e->getMessage());
                }
            }

            try {
                \Illuminate\Support\Facades\Log::info('File Upload Step 2 - Starting upload process');

                // Ensure the products directory exists
                $productsPath = storage_path('app/public/products');
                if (!file_exists($productsPath)) {
                    mkdir($productsPath, 0755, true);
                    \Illuminate\Support\Facades\Log::info('Created products directory: ' . $productsPath);
                }

                // Manually generate name to ensure it's not empty, prepend timestamp for uniqueness
                $filename = time() . '_' . $file->hashName();
                \Illuminate\Support\Facades\Log::info('File Upload Step 3 - Generated filename: ' . $filename);

                if (empty($filename)) {
                    $filename = time() . '_' . \Illuminate\Support\Str::random(40) . '.' . $file->guessExtension();
                    \Illuminate\Support\Facades\Log::warning('Filename was empty, generated random: ' . $filename);
                }

                \Illuminate\Support\Facades\Log::info('File Upload Step 4 - Using direct file move instead of putFileAs');

                // Use direct file move to bypass Storage facade issues
                $destinationPath = $productsPath . DIRECTORY_SEPARATOR . $filename;
                $moved = $file->move($productsPath, $filename);

                \Illuminate\Support\Facades\Log::info('File Upload Step 5 - File moved successfully');

                if (!$moved) {
                    throw new \Exception('Failed to move uploaded file.');
                }

                // Store relative path for database (products/filename.jpg)
                $path = 'products/' . $filename;
                $validated['image'] = $path;

                \Illuminate\Support\Facades\Log::info("Product Image Updated SUCCESS: ID {$product->id}, Path: {$path}");

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Image upload failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                return back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
            }
        }

        // Remove request_types from validated data before updating product
        $requestTypeIds = $validated['request_types'] ?? [];
        unset($validated['request_types']);

        $product->update($validated);

        // Sync request types
        $product->requestTypes()->sync($requestTypeIds);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_UPDATED',
            'description' => "Updated product {$product->name}",
            'ip_address' => request()->ip(),
            'subject_type' => get_class($product),
            'subject_id' => $product->id,
        ]);

        return redirect()->route($this->getRoutePrefix() . '.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!empty($product->image) && trim($product->image) !== '') {
            Storage::disk('public')->delete($product->image);
        }
        $productName = $product->name;
        $product->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_DELETED',
            'description' => "Deleted product {$productName}",
            'ip_address' => request()->ip(),
            'subject_type' => get_class($product),
            'subject_id' => $product->id,
        ]);

        return redirect()->route($this->getRoutePrefix() . '.products.index')->with('success', 'Product deleted successfully.');
    }
}
