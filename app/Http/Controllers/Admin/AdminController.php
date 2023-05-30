<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        return response()->noContent();
    }
}
