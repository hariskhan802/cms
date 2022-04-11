<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $name = 'dashboard';
        return view('Admin.Dashboard.index', ['name' => $name]);
    }
}
