<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    /**
     * Show the public landing page
     */
    public function landing()
    {
        return view('public.landing');
    }
}
