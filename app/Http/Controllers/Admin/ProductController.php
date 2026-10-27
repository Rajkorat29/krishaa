<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Metal;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request): View
    {
        $query = Product::withTrashed()->with(['category', 'metal', 'images']);

        if ($request->input('scope') === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('stone', 'like', "%{$q}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        if ($metalId = $request->input('metal_id')) {
            $query->where('metal_id', $metalId);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        if ($request->input('stock_status') === 'low') {
            $query->where('stock', '<=', 5);
        } elseif ($request->input('stock_status') === 'out') {
            $query->where('stock', 0);
        }

        $products = $query->latest('id')->paginate(15);
        $categories = Category::all();
        $metals = Metal::all();

        return view('admin.products.index', compact('products', 'categories', 'metals'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $metals = Metal::all();
        $symbols = ['ring', 'ring-round', 'ring-band', 'ring-twin', 'ring-marquise', 'earrings', 'bangle', 'necklace', 'pearls', 'locket'];
        $colorClasses = ['c-pink', 'c-emerald', 'c-sapphire', 'c-gold', 'c-lilac'];
        $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];

        return view('admin.products.create', compact('categories', 'metals', 'symbols', 'colorClasses', 'ringSizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|unique:products,slug',
            'sku' => 'nullable|string|max:40|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'metal_id' => 'required|exists:metals,id',
            'stone' => 'nullable|string|max:80',
            'weight_grams' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:1',
            'mrp' => 'nullable|numeric|gte:price',
            'stock' => 'required|integer|min:0',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'art_symbol' => 'required|string',
            'color_class' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images.*' => 'nullable|image|max:4096',
            'sizes' => 'nullable|array',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? $validated['slug'] : Str::slug($validated['name']);
        $validated['sku'] = !empty($validated['sku']) ? $validated['sku'] : ('DSY-' . strtoupper(Str::random(6)));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['published_at'] = now();

        $product = Product::create($validated);

        // Save sizes
        if ($request->has('sizes') && is_array($request->sizes)) {
            foreach ($request->sizes as $label => $qty) {
                if ($qty !== null && $qty !== '') {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_label' => (string) $label,
                        'stock' => (int) $qty,
                    ]);
                }
            }
        }

        // Upload images
        if ($request->hasFile('images')) {
            $isCover = true;
            foreach ($request->file('images') as $idx => $file) {
                $paths = $this->imageService->uploadProductImage($file);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $paths['original'],
                    'is_cover' => $isCover,
                    'sort_order' => $idx,
                ]);
                $isCover = false;
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created successfully!");
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'metal', 'images', 'sizes']);
        $categories = Category::all();
        $metals = Metal::all();
        $symbols = ['ring', 'ring-round', 'ring-band', 'ring-twin', 'ring-marquise', 'earrings', 'bangle', 'necklace', 'pearls', 'locket'];
        $colorClasses = ['c-pink', 'c-emerald', 'c-sapphire', 'c-gold', 'c-lilac'];
        $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];

        return view('admin.products.edit', compact('product', 'categories', 'metals', 'symbols', 'colorClasses', 'ringSizes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => "nullable|string|max:140|unique:products,slug,{$product->id}",
            'sku' => "nullable|string|max:40|unique:products,sku,{$product->id}",
            'category_id' => 'required|exists:categories,id',
            'metal_id' => 'required|exists:metals,id',
            'stone' => 'nullable|string|max:80',
            'weight_grams' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:1',
            'mrp' => 'nullable|numeric|gte:price',
            'stock' => 'required|integer|min:0',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'art_symbol' => 'required|string',
            'color_class' => 'required|string',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images.*' => 'nullable|image|max:4096',
            'cover_image_id' => 'nullable|integer',
            'delete_images' => 'nullable|array',
            'sizes' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        // Update sizes
        if ($request->has('sizes') && is_array($request->sizes)) {
            foreach ($request->sizes as $label => $qty) {
                if ($qty !== null && $qty !== '') {
                    ProductSize::updateOrCreate(
                        ['product_id' => $product->id, 'size_label' => (string) $label],
                        ['stock' => (int) $qty]
                    );
                }
            }
        }

        // Delete requested images
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            foreach ($request->delete_images as $imgId) {
                $img = ProductImage::where('id', $imgId)->where('product_id', $product->id)->first();
                if ($img) {
                    $this->imageService->deleteImage($img->path);
                    $img->delete();
                }
            }
        }

        // Set Cover Image
        if ($coverId = $request->input('cover_image_id')) {
            ProductImage::where('product_id', $product->id)->update(['is_cover' => false]);
            ProductImage::where('product_id', $product->id)->where('id', $coverId)->update(['is_cover' => true]);
        }

        // Upload new images
        if ($request->hasFile('images')) {
            $hasCover = $product->images()->where('is_cover', true)->exists();
            foreach ($request->file('images') as $file) {
                $paths = $this->imageService->uploadProductImage($file);
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $paths['original'],
                    'is_cover' => ! $hasCover,
                    'sort_order' => $product->images()->count(),
                ]);
                $hasCover = true;
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', "Product '{$product->name}' moved to trash.");
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return back()->with('success', "Product '{$product->name}' restored.");
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        foreach ($product->images as $img) {
            $this->imageService->deleteImage($img->path);
        }
        $product->forceDelete();
        return back()->with('success', 'Product permanently deleted.');
    }
}
