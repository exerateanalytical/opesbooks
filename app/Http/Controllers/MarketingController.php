<?php

namespace App\Http\Controllers;

use App\Models\PlanConfig;

class MarketingController extends Controller
{
    public function home()
    {
        $plans = PlanConfig::where('is_active', true)->orderBy('sort_order')->get();
        return view('marketing.home', compact('plans'));
    }

    public function features()
    {
        return view('marketing.features');
    }

    public function pricing()
    {
        $plans = PlanConfig::where('is_active', true)->orderBy('sort_order')->get();
        return view('marketing.pricing', compact('plans'));
    }

    public function contact()
    {
        return view('marketing.contact');
    }

    public function about()
    {
        return view('marketing.about');
    }
}
