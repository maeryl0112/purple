<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Employee;
use Illuminate\Http\Request;

class DisplayAbout extends Controller
{
   public function about()
   {
        $employees = Employee::all();
        $categories = Category::all();
        return view('web.about', compact('employees','categories'));
   }
}
