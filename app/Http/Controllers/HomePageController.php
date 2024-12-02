<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomePageController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        // get the top 3 popular services using most bookings in the last 30 days
        $popularServices = \App\Models\Service::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(4)
            ->where('is_hidden', false)
            ->get();


        return view('web.home', compact( 'popularServices','categories'));
    }
}
