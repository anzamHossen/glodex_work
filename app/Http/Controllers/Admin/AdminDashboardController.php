<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Application;
use App\Models\Admin\Country;
use App\Models\Admin\Course;
use App\Models\Admin\StudentInfo;
use App\Models\Admin\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.admin-dashboard');
    }
}
