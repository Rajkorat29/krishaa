<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetalController extends Controller
{
    public function index(): View
    {
        $metals = Metal::withCount('products')->get();
        return view('admin.metals.index', compact('metals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60|unique:metals,name',
        ]);

        Metal::create($validated);
        return back()->with('success', 'Metal added.');
    }

    public function update(Request $request, Metal $metal)
    {
        $validated = $request->validate([
            'name' => "required|string|max:60|unique:metals,name,{$metal->id}",
        ]);

        $metal->update($validated);
        return back()->with('success', 'Metal updated.');
    }

    public function destroy(Metal $metal)
    {
        if ($metal->products()->count() > 0) {
            return back()->with('error', 'Cannot delete metal currently linked to active products.');
        }

        $metal->delete();
        return back()->with('success', 'Metal deleted.');
    }
}
