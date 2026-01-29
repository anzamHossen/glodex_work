<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\Admin\University;
use Illuminate\Http\Request;

class StudentUniversityController extends Controller
{
    // function to show student university list page
    public function studentUniversityList()
    {
        $universities = University::with('country')->orderBy('id','desc')->paginate(8);
        $countries    = Country::where('status', 1)->get();
        return view('student.university.student-university-list', compact('universities','countries'));
    }

    // Function to search university by name and country for student
    public function searchStudentUniversity(Request $request)
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

        return view('student.university.student-university-list', compact('universities', 'countries'));
    }

    // function to show student university details page
    public function studentUniversityDetails($id)
    {
        $universityDetails = University::with('country')->findOrFail($id);
        $randomCourses = $universityDetails->courses()
                                       ->inRandomOrder()
                                       ->take(10)
                                       ->get();
        return view('student.university.student-university-details', compact('universityDetails', 'randomCourses'));
    }
}
