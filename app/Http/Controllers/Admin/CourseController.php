<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Handler\ImageHandlerController;
use App\Http\Middleware\Student;
use App\Models\Admin\Country;
use App\Models\Admin\Course;
use App\Models\Admin\CourseProgram;
use App\Models\Admin\IntakeMonth;
use App\Models\Admin\StudentInfo;
use App\Models\Admin\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CourseController extends Controller
{
    protected $imageHandler;

    // Functio to call image handler
    public function __construct(ImageHandlerController $imageHandler)
    {
        $this->imageHandler = $imageHandler;
    }
    
    // function to show course list page
    public function courseList()
    {
        $courses = Course::with('university','country','courseProgram')
            ->orderBy('id', 'desc')
            ->paginate(8);
        $coursePrograms = CourseProgram::all();
        $countries      = Country::all();
        $students = StudentInfo::orderBy('id', 'desc')->get();
       return view('admin.course.course-list', compact('courses','coursePrograms','countries','students'));
    }

    // function to show course details
    public function courseDetails($id)
    {
        $courseDetails = Course::with('country', 'university','courseProgram')->findOrFail($id);
        return view('admin.course.course-details', compact('courseDetails'));   
    }           

    // function to search course
    public function searchCourse(Request $request)
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

        return view('admin.course.course-list', compact('courses','countries','coursePrograms'));
    }

    // function to filter course
    public function filterCourse(Request $request)
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
        return view('admin.course.course-list', compact('courses', 'countries', 'coursePrograms','students'));
    }



    // function to show add course page
    public function addCourse()
    {
        $countries      = Country::all();
        $universities   = University::all();
        $coursePrograms = CourseProgram::all();
        $intakeMonths   = IntakeMonth::all();
        return view('admin.course.add-new-course', compact('countries','universities','coursePrograms','intakeMonths'));
    }

    // function to save new course
    public function saveCourse(Request $request)
    {
        $request->validate([
            'university_id'       => 'required|exists:universities,id',
            'country_id'          => 'required|exists:countries,id',
            'course_program_id'   => 'required|exists:course_programs,id',
            'intake_month_id'     => 'required|array',
            'course_name'         => 'required|string|max:255',
            'application_fee'     => 'required|string|max:255',
            'tuition_fee_per_year'=> 'required|string|max:255',
            'program_length'      => 'required|string|max:255',
            'course_photo'        => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'course_details'      => 'required',
        ]);

        DB::beginTransaction();
        try {

            $course = new Course();
            $course->university_id        = $request->university_id;
            $course->country_id           = $request->country_id;
            $course->course_program_id    = $request->course_program_id;
            $course->intake_month_id      = json_encode($request->intake_month_id);
            $course->course_name          = $request->course_name;
            $course->application_fee      = $request->application_fee;
            $course->tuition_fee_per_year = $request->tuition_fee_per_year;
            $course->program_length       = $request->program_length;
            $course->course_details       = $request->course_details;

            if ($request->hasFile('course_photo')) {
                $file = $request->file('course_photo');
                $filePath = $this->imageHandler->coursePhoto($file);
                $course->course_photo = $filePath;
            }

            $course->save();
            DB::commit();
            Alert::success('Success', 'Course added successfuly');
            return redirect()->back();
        }catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Alert::error('Error', 'Failed to added course. Please try again.');
            return redirect()->back();
        }
    }

    // function to edit course
    public function editCourse($id)
    {
        $countries      = Country::all();
        $universities   = University::all();
        $coursePrograms = CourseProgram::all();
        $intakeMonths   = IntakeMonth::all();
        $course         = Course::findOrFail($id);
        $course->intake_month_ids = json_decode($course->intake_month_id, true) ?? [];
        return view('admin.course.edit-course', compact('countries','universities','coursePrograms','intakeMonths','course'));
    }

    // function to update course
    public function updateCourse(Request $request, $id)
    {
           $request->validate([
            'university_id'       => 'required|exists:universities,id',
            'country_id'          => 'required|exists:countries,id',
            'course_program_id'   => 'required|exists:course_programs,id',
            'intake_month_id'     => 'required|array',
            'course_name'         => 'required|string|max:255',
            'application_fee'     => 'required|string|max:255',
            'tuition_fee_per_year'=> 'required|string|max:255',
            'program_length'      => 'required|string|max:255',
            'course_details'      => 'required',
        ]);
        DB::beginTransaction();
        try {
            $course = Course::findOrFail($id);
            $course->university_id        = $request->university_id;
            $course->country_id           = $request->country_id;
            $course->course_program_id    = $request->course_program_id;
            $course->intake_month_id      = json_encode($request->intake_month_id);
            $course->course_name          = $request->course_name;
            $course->application_fee      = $request->application_fee;
            $course->tuition_fee_per_year = $request->tuition_fee_per_year;
            $course->program_length       = $request->program_length;
            $course->course_details       = $request->course_details;

            if ($request->hasFile('course_photo')) {
                $file = $request->file('course_photo');
                $filePath = $this->imageHandler->coursePhoto($file);
                $course->course_photo = $filePath;
            }

            $course->update();
            DB::commit();
            Alert::success('Success', 'Course updated successfuly');
            return redirect()->back();
        }catch (\Exception $e) {
            DB::rollback();
            Alert::error('Error', 'Failed to update course. Please try again.');
            return redirect()->back();
        }
    }

    // function to delete course
    public function deleteCourse($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            Alert::success('Success', 'Course deleted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to delete course. Please try again.');
            return redirect()->back();
        }
    }
}
