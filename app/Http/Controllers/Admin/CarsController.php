<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    public function index(Request $request)
    {
        // Empty page placeholder for now
        return inertia('Admin/Cars/Index');
    }
}
