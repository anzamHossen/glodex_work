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
        // Condition for Super Admin Role   
        $isSuperAdmin      = Auth::user()->hasRole('SuperAdmin');
        $totalCountries    = Country::count();
        $totalUniversities = University::count();
        $totalCourses      = Course::count();
        $totalStudents     = StudentInfo::count();
        $totalActiveAgent  = User::where('user_type', 2)->where('user_status', 2)->count();
        $totalApplications = Application::count();
        $totalVisaGranted  = Application::where('status', 10)->count();
        $totalVisaRejected = Application::where('status', 12)->count();

        // Condition for BDM Admin Role   
        $isBDM                = Auth::user()->hasRole('BDM');
        $totalPartnerStudents = StudentInfo::whereHas('createdBy', function ($query) {
                            $query->where('created_by', Auth::id());
                            })->count();

        $totalPartnerCreated = User::where('created_by',Auth::id())->where('user_type',2)->count();

        $authUser = Auth::user();
        $totalPartnerApplications = Application::whereHas('createdBy', function ($query) use ($authUser) {
            $query->where('user_type', 2)          
                ->where('created_by', $authUser->id);
        })->count();

        $totalPartnerVisaGranted = Application::where('status', 10)
        ->when($authUser->hasRole('BDM'), function ($query) use ($authUser) {
            $query->whereHas('createdBy', function ($q) use ($authUser) {
                $q->where('user_type', 2)
                ->where('created_by', $authUser->id);
            });
        })->count();

        $totalPartnerVisaRejected = Application::where('status', 12)
        ->when($authUser->hasRole('BDM'), function ($query) use ($authUser) {
            $query->whereHas('createdBy', function ($q) use ($authUser) {
                $q->where('user_type', 2)
                ->where('created_by', $authUser->id);
            });
        })->count();

        return view('dashboard.admin-dashboard', compact('totalCountries','totalUniversities','totalCourses','totalStudents','totalActiveAgent',
        'totalApplications','totalVisaGranted','totalVisaRejected','isSuperAdmin',
       
         // condition for BDM Admin Role
        'isBDM','totalPartnerCreated','totalPartnerStudents','totalPartnerApplications','totalPartnerVisaGranted','totalPartnerVisaRejected'
        ));
    }
}
