<?php

namespace App\Http\Controllers;

use App\Models\Drop;
use App\Models\HeroSlide;
use App\Models\MarqueeWord;
use App\Models\SocialPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $heroSlides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();
        $drops = Drop::orderBy('sort_order')->get();
        $socialPosts = SocialPost::where('is_active', true)->orderBy('sort_order')->get();
        $marqueeWords = MarqueeWord::orderBy('sort_order')->get();

        return view('pages.home', compact('heroSlides', 'drops', 'socialPosts', 'marqueeWords'));
    }
}
