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
        $agentTotalCountries        = Country::count();
        $agentTotalUniversities     = University::count();
        $agentTotalCourses          = Course::count();
        $agentTotalStudents         = StudentInfo::where('created_by', Auth::id())->count();
        $agentTotalApplications     = Application::where('created_by', Auth::id())->count();
        $AgentInProgressApplication = Application::where('status', 1)->where('created_by', Auth::id())->count(); 
        $agentTotalVisaGranted      = Application::where('status', 10)->where('created_by', Auth::id())->count();
        $agentTotalVisaRejected     = Application::where('status', 12)->count();
        return view('dashboard.agent-dashboard',compact('agentTotalCountries','agentTotalUniversities','agentTotalCourses',
       'agentTotalStudents','agentTotalApplications','AgentInProgressApplication','agentTotalVisaGranted','agentTotalVisaRejected'));
    }
}
