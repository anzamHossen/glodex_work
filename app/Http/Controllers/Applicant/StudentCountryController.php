<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\Admin\University;
use Illuminate\Http\Request;

class StudentCountryController extends Controller
{
    public function studentCountryList()
    {
        $countries = Country::with('countryContinent')
        ->where('status', 1)
        ->withCount(['universities', 'courses'])
        ->orderBy('country_name', 'asc')
        ->paginate(8);

        return view('student.country.student-country-list', compact('countries'));
    }

    public function searchStudentCountries(Request $request)
    {
        $query = Country::with('countryContinent')
            ->withCount(['universities', 'courses'])
            ->where('status', 1);

        // Search logic
        if ($request->filled('search_country')) {
            $query->where('country_name', 'LIKE', '%' . $request->search_country . '%');
        }

        // Pagination: default to 10 entries
        $perPage = $request->get('per_page', 10);
        $countries = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());

        return view('student.country.student-country-list', compact('countries'));
    }

    // function to country details
    public function studentCountryDetails($id)
    {
        $country = Country::findOrFail($id);
        $totalUniversities = $country->universities()->count();
        $totalCourses = $country->courses()->count();
        $randomUniversities = University::where('country_id', $country->id)
                                        ->inRandomOrder()
                                        ->take(10)
                                        ->get();

        return view('student.country.student-country-details', compact(
            'country',
            'totalUniversities',
            'totalCourses',
            'randomUniversities'
        ));
    }
}
