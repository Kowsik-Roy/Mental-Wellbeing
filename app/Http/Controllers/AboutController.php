<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Show the About Us page.
     */
    public function index()
    {
        return view('about.index');
    }
}
