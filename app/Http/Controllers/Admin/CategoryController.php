<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        Category::create($validated);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'slug' => "nullable|string|max:100|unique:categories,slug,{$category->id}",
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        $category->update($validated);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category that still has products assigned.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
