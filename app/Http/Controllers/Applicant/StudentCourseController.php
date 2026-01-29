<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use App\Models\Admin\Course;
use App\Models\Admin\CourseProgram;
use App\Models\Admin\StudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
     // function to show agent course list page
    public function studentCourseList()
    {
        $courses = Course::with('university','country','courseProgram')
            ->orderBy('id', 'desc')
            ->paginate(8);
        $coursePrograms = CourseProgram::all();
        $countries      = Country::all();
        $students       = StudentInfo::where('created_by', Auth::id())->orderBy('id', 'desc')->get();
       return view('student.course.student-course-list', compact('courses','coursePrograms','countries','students'));
    }

    public function studentCourseDetails($id)
    {
        $courseDetails = Course::with('country', 'university','courseProgram')->findOrFail($id);
        return view('student.course.student-course-details', compact('courseDetails'));
    }

     // function to search student course
    public function studentSearchCourse(Request $request)
    {
        $query = Course::with(['country']);
        $searchCourse = $request->input('search_course');
        $searchCountry = $request->input('search_country');
        $perPage = $request->input('per_page', 8);

        if (!empty($searchCourse)) {
            $query->where('course_name', 'like', '%' . $searchCourse . '%');
        }

        if (!empty($searchCountry)) {
            $query->whereHas('country', function ($q) use ($searchCountry) {
                $q->where('country_name', 'like', '%' . $searchCountry . '%');
            });
        }

        $courses = $query->paginate($perPage)->appends($request->all());
        $countries = Country::all();
        $coursePrograms = CourseProgram::all();
        $students       = StudentInfo::where('created_by', Auth::id())->orderBy('id', 'desc')->get();

        return view('student.course.student-course-list', compact('courses','countries','coursePrograms','students'));
    }


    public function studentFilterCourse(Request $request)
    {
        $query = Course::with(['university', 'country', 'courseProgram']);

        // Filters
        if ($request->filled('course_name')) {
            $query->where('course_name', 'like', '%' . $request->course_name . '%');
        }

        if ($request->filled('university_name')) {
            $query->whereHas('university', function ($q) use ($request) {
                $q->where('university_name', 'like', '%' . $request->university_name . '%');
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('tuition_fees')) {
            $query->where('tuition_fee_per_year', '<=', $request->tuition_fees);
        }

        if ($request->filled('application_fees')) {
            $query->where('application_fee', '<=', $request->application_fees);
        }

        if ($request->filled('program_level')) {
            $query->where('course_program_id', $request->program_level);
        }

        if ($request->filled('program_length')) {
            $query->where('program_length', $request->program_length);
        }

        $courses = $query->orderByDesc('id')
            ->paginate(8)
            ->appends($request->all());

        $countries = Country::all();
        $coursePrograms = CourseProgram::all();
        $students       = StudentInfo::where('created_by', Auth::id())->orderBy('id', 'desc')->get();
        return view('student.course.student-course-list', compact('courses', 'countries', 'coursePrograms','students'));
    }
}
