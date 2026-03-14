<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing home page.
     */
    public function index()
    {
        return view('landing.home');
    }

    /**
     * Display the features page.
     */
    public function features()
    {
        return view('landing.features');
    }

    /**
     * Display the pricing page.
     */
    public function pricing()
    {
        return view('landing.pricing');
    }

    /**
     * Display the demo request page.
     */
    public function demo()
    {
        return view('landing.demo');
    }

    /**
     * Display the documentation page.
     */
    public function docs()
    {
        return view('landing.docs');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('landing.privacy');
    }

    /**
     * Handle demo request form submission.
     */
    public function submitDemoRequest(Request $request)
    {
        // Simple validation and response for now. 
        // In a real app, this would send an email or store in DB.
        $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'business_size' => 'required|string',
            'industry' => 'required|string',
        ]);

        return back()->with('success', 'Thank you for your interest! Our team will contact you shortly to schedule a demo.');
    }
}
