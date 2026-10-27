<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Drop;
use App\Models\HeroSlide;
use App\Models\MarqueeWord;
use App\Models\Product;
use App\Models\SocialPost;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(): View
    {
        $heroSlides = HeroSlide::orderBy('sort_order')->get();
        $drops = Drop::orderBy('sort_order')->get();
        $socialPosts = SocialPost::orderBy('sort_order')->get();
        $marqueeWords = MarqueeWord::orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $symbols = ['ring', 'ring-round', 'ring-band', 'ring-twin', 'ring-marquise', 'earrings', 'bangle', 'necklace', 'pearls', 'locket'];
        $colorClasses = ['c-pink', 'c-emerald', 'c-sapphire', 'c-gold', 'c-lilac'];

        return view('admin.cms.index', compact('heroSlides', 'drops', 'socialPosts', 'marqueeWords', 'products', 'symbols', 'colorClasses'));
    }

    public function saveHero(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'tag' => 'nullable|string|max:50',
            'title' => 'required|string|max:100',
            'meta_text' => 'nullable|string|max:120',
            'link_url' => 'nullable|string|max:200',
            'color_class' => 'required|string',
            'art_symbol' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:4096',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $paths = $this->imageService->uploadProductImage($request->file('image'), 'cms');
            $validated['image'] = $paths['original'];
        }

        if (! empty($validated['id'])) {
            $slide = HeroSlide::findOrFail($validated['id']);
            $slide->update($validated);
        } else {
            HeroSlide::create($validated);
        }

        return back()->with('success', 'Hero slide saved.');
    }

    public function deleteHero(HeroSlide $slide)
    {
        if ($slide->image) {
            $this->imageService->deleteImage($slide->image);
        }
        $slide->delete();
        return back()->with('success', 'Hero slide deleted.');
    }

    public function saveDrop(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'title' => 'required|string|max:100',
            'subtitle' => 'nullable|string|max:150',
            'drop_date' => 'required|string|max:50',
            'drop_time' => 'required|string|max:20',
            'metal_label' => 'nullable|string|max:50',
            'weight_label' => 'nullable|string|max:30',
            'price_label' => 'nullable|string|max:30',
            'status' => 'required|in:available,sold_out',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if (! empty($validated['id'])) {
            $drop = Drop::findOrFail($validated['id']);
            $drop->update($validated);
        } else {
            Drop::create($validated);
        }

        return back()->with('success', 'Drop saved.');
    }

    public function deleteDrop(Drop $drop)
    {
        $drop->delete();
        return back()->with('success', 'Drop deleted.');
    }

    public function saveSocial(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'platform_label' => 'required|string|max:50',
            'caption' => 'required|string|max:200',
            'color_class' => 'required|string',
            'art_symbol' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:4096',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $paths = $this->imageService->uploadProductImage($request->file('image'), 'cms');
            $validated['image'] = $paths['original'];
        }

        if (! empty($validated['id'])) {
            $post = SocialPost::findOrFail($validated['id']);
            $post->update($validated);
        } else {
            SocialPost::create($validated);
        }

        return back()->with('success', 'Social post saved.');
    }

    public function deleteSocial(SocialPost $post)
    {
        if ($post->image) {
            $this->imageService->deleteImage($post->image);
        }
        $post->delete();
        return back()->with('success', 'Social post deleted.');
    }

    public function saveMarquee(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:40',
            'sort_order' => 'nullable|integer',
        ]);

        MarqueeWord::create([
            'word' => trim($validated['word']),
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        return back()->with('success', 'Marquee word added.');
    }

    public function deleteMarquee(MarqueeWord $word)
    {
        $word->delete();
        return back()->with('success', 'Marquee word deleted.');
    }
}
