<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request): Response
    {
        return response()->noContent();
    }
}
