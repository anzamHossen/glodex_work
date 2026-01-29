<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\Admin\University;
use Illuminate\Http\Request;

class AgentUniversityController extends Controller
{
    // function to show agent university list page
    public function agentUniversityList()
    {
        $universities = University::with('country')->orderBy('id','desc')->paginate(8);
        $countries    = Country::where('status', 1)->get();
        return view('agent.university.agent-university-list', compact('universities','countries'));
    }

    // Function to search university by name and country for agent
    public function searchAgentUniversity(Request $request)
    {
        $query = University::with('country')->orderBy('id', 'desc');

        // Search by university name
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('university_name', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($request->filled('university_id')) {
            $countryId = $request->input('university_id');
            $query->where('country_id', $countryId);
        }

        // Pagination
        $perPage = $request->input('per_page', 8);
        $universities = $query->paginate($perPage)->appends($request->all());

        $countries = Country::where('status', 1)->get();

        return view('agent.university.agent-university-list', compact('universities', 'countries'));
    }

    // function to show agent university details page
    public function agentUniversityDetails($id)
    {
        $universityDetails = University::with('country')->findOrFail($id);
        $randomCourses = $universityDetails->courses()
                                       ->inRandomOrder()
                                       ->take(10)
                                       ->get();
        return view('agent.university.agent-university-details', compact('universityDetails', 'randomCourses'));
    }
}
