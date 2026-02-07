<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Application;
use App\Models\Admin\Country;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Course;
use App\Models\Admin\StudentInfo;
use App\Models\Admin\University;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AgentDashboardController extends Controller
{
    public function agentDashboard()
    {
        return view('dashboard.agent-dashboard');
    }
}
