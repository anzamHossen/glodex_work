<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Admin\Application;
use App\Models\Admin\Course;
use App\Models\Admin\StudentFile;
use App\Models\Admin\StudentInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AgentApplicationController extends Controller
{
    // function to show agent application list
    public function agentApplicationList()
    {
        $applications = Application::with(['student', 'course.country', 'course.university', 'applicationStatus'])
                        ->where('created_by', Auth::id())
                        ->orderBy('id', 'desc')
                        ->get();
        return view('agent.application.agent-application-list', compact('applications'));
    }

    // function to show new student application form
    public function agentApplicationNewStudent($course_id)
    {
        $course   = Course::find($course_id);
        return view('agent.application.agent-application-new-student', compact('course'));
    }

    // function to save agent new student application
    public function saveAgentApplicationNewStudent(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'phone'       => 'required',
            'email'       => 'required|email',
            'dob'         => 'required|date',
            'passport_no' => 'required|string',
            'permanent_address' => 'required|string',
            'gender'      => 'required',
            'intake_year' => 'required',
        ]);

        $existingStudent = StudentInfo::where('phone', $request->phone)
            ->orWhere('email', $request->email)
            ->orWhere('passport_no', $request->passport_no)
            ->exists();

        if ($existingStudent) {
            Alert::error('Error', 'Student Already Exists');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();

        try {
            // Create User
            $user = User::create([
                'name'       => $request->name,
                'phone'      => $request->phone,
                'email'      => $request->email,
                'password'   => Hash::make('student1234'),
                'created_by' => Auth::id(),
                'user_type'  => 3, // Student
            ]);

            // Create Student Info
            $studentInfo = new StudentInfo();
            $studentInfo->user_id = $user->id;
            $studentInfo->sent_by = Auth::user()->organization_name ?? 'N/A';
            $studentInfo->name = $request->name;
            $studentInfo->student_code = 'GLD' . rand(1000000, 9999999);
            $studentInfo->phone = $request->phone;
            $studentInfo->email = $request->email;
            $studentInfo->permanent_address = $request->permanent_address;
            $studentInfo->gender = $request->gender;
            $studentInfo->fathers_name = $request->fathers_name;
            $studentInfo->mothers_name = $request->mothers_name;
            $studentInfo->dob = $request->dob;
            $studentInfo->passport_no = $request->passport_no;
            $studentInfo->moi = $request->moi;
            $studentInfo->notes = $request->notes;
            $studentInfo->created_by = Auth::id();

            // English Proficiency
            if ($request->has('english_tests')) {
                $englishTests = [];
                foreach ($request->english_tests as $test) {
                    $englishTests[] = [
                        'type'      => $test['type'] ?? null,
                        'listening' => $test['listening'] ?? null,
                        'reading'   => $test['reading'] ?? null,
                        'writing'   => $test['writing'] ?? null,
                        'speaking'  => $test['speaking'] ?? null,
                        'overall'   => $test['overall'] ?? null,
                    ];
                }
                $studentInfo->english_proficiency = json_encode($englishTests);
            }

            // Academic Qualifications
            if ($request->has('academic_qualifications')) {
                $academicQualifications = [];
                foreach ($request->academic_qualifications as $qualification) {
                    $academicQualifications[] = [
                        'group_name'     => $qualification['group_name'] ?? null,
                        'institute_name' => $qualification['institute_name'] ?? null,
                        'gpa'            => $qualification['gpa'] ?? null,
                        'passing_year'   => $qualification['passing_year'] ?? null,
                    ];
                }
                $studentInfo->academic_qualifications = json_encode($academicQualifications);
            }

            $studentInfo->save();

            // Save Student Files
            if ($request->hasFile('studentfiles')) {
                $studentFiles = $request->file('studentfiles');
                $filenames = $request->input('filename');
                foreach ($studentFiles as $key => $single) {
                    $originalFileName = $single->getClientOriginalName();
                    $newFileName = Carbon::now()->timestamp . '_' . $studentInfo->name . '_' . $originalFileName;
                    $filePath = $single->storeAs($filenames[$key], $newFileName, 'public');

                    StudentFile::create([
                        'student_id' => $studentInfo->id,
                        'filename'   => $filenames[$key],
                        'filepath'   => $filePath,
                    ]);
                }
            }

            // Create Application Record
            Application::create([
                'user_id'     => Auth::id(), // current admin/agent
                'course_id'   => $request->course_id,
                'student_id'  => $studentInfo->id,
                'sent_by'     => Auth::user()->organization_name ?? 'N/A',
                'application_code' => rand(100000, 999999),
                'created_by'  => Auth::id(),
                'intake_year' => $request->intake_year,
            ]);

            DB::commit();

            Alert::success('Success', 'Application added successfully');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            Alert::error('Error', 'Failed to save application, Try Again');
            return redirect()->back();
        }
    }

    // function to add application for existing student
    public function agentApplicationEixStudent($course_id, $student_id)
    {
        $applicationExists = Application::where('course_id', $course_id)
        ->where('student_id', $student_id)
        ->exists();

        if ($applicationExists) {
            Alert::error('Error', 'In this course the student application in progress');
            return redirect()->back();
        }

        $course   = Course::find($course_id);
        $student  = StudentInfo::find($student_id);
        $englishTests = json_decode($student->english_proficiency, true) ?? [];
        $academicQualifications = json_decode($student->academic_qualifications, true) ?? [];
        return view('agent.application.agent-application-existing-student',compact('course','student','englishTests','academicQualifications'));
    }

    // function to save agent existing student application
    public function saveAgentApplicationEixStudent(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|exists:student_infos,id',
            'course_id'         => 'required|exists:courses,id',
            'name'              => 'required|string|max:50',
            'phone'             => 'required',
            'email'             => 'required|email',
            'dob'               => 'required|date',
            'passport_no'       => 'required|string',
            'permanent_address' => 'required|string',
            'gender'            => 'required',
            'intake_year'       => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Find existing student
            $studentInfo = StudentInfo::find($request->student_id);

            if (!$studentInfo) {
                Alert::error('Error', 'Student not found.');
                return redirect()->back();
            }

            //Update Student Info
            $studentInfo->update([
                'name'              => $request->name,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'dob'               => $request->dob,
                'passport_no'       => $request->passport_no,
                'permanent_address' => $request->permanent_address,
                'gender'            => $request->gender,
                'fathers_name'      => $request->fathers_name,
                'mothers_name'      => $request->mothers_name,
                'moi'               => $request->moi,
                'notes'             => $request->notes,
            ]);

            // 🔹 Update English Proficiency
            if ($request->has('english_tests')) {
                $englishTests = [];
                foreach ($request->english_tests as $test) {
                    $englishTests[] = [
                        'type'      => $test['type'] ?? null,
                        'listening' => $test['listening'] ?? null,
                        'reading'   => $test['reading'] ?? null,
                        'writing'   => $test['writing'] ?? null,
                        'speaking'  => $test['speaking'] ?? null,
                        'overall'   => $test['overall'] ?? null,
                    ];
                }
                $studentInfo->english_proficiency = json_encode($englishTests);
                $studentInfo->save();
            }

            //Update Academic Qualifications
            if ($request->has('academic_qualifications')) {
                $academicQualifications = [];
                foreach ($request->academic_qualifications as $qualification) {
                    $academicQualifications[] = [
                        'group_name'     => $qualification['group_name'] ?? null,
                        'institute_name' => $qualification['institute_name'] ?? null,
                        'gpa'            => $qualification['gpa'] ?? null,
                        'passing_year'   => $qualification['passing_year'] ?? null,
                    ];
                }
                $studentInfo->academic_qualifications = json_encode($academicQualifications);
                $studentInfo->save();
            }

            //Upload Student Files (if any new files)
            if ($request->hasFile('studentfiles')) {
                $studentFiles = $request->file('studentfiles');
                $filenames = $request->input('filename');

                foreach ($studentFiles as $key => $single) {
                    $originalFileName = $single->getClientOriginalName();
                    $newFileName = Carbon::now()->timestamp . '_' . $studentInfo->name . '_' . $originalFileName;
                    $filePath = $single->storeAs($filenames[$key], $newFileName, 'public');

                    StudentFile::create([
                        'student_id' => $studentInfo->id,
                        'filename'   => $filenames[$key],
                        'filepath'   => $filePath,
                    ]);
                }
            }

            //Create Application for this student
            Application::create([
                'user_id'     => Auth::id(),
                'course_id'   => $request->course_id,
                'student_id'  => $studentInfo->id,
                'sent_by'     => Auth::user()->organization_name ?? 'N/A',
                'application_code' => rand(100000, 999999),
                'created_by'  => Auth::id(),
                'intake_year' => $request->intake_year,
            ]);

            DB::commit();

            Alert::success('Success', 'Application added successfully for existing student.');
            return redirect()->route('agent_application_list');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            Alert::error('Error', 'Failed to add application, Try Again.');
            return redirect()->back()->withInput();
        }
    }

    // function to edit application
    public function agentEditApplication($id, $course_id, $student_id)
    {
        $application = Application::find($id);

        if (!$application) {
        Alert::error('Error', 'Application not found.');
        return redirect()->back();
        }

        $course = Course::find($course_id);

        if (!$course) {
        Alert::error('Error', 'Course not found..');
        return redirect()->back();
        }

        if (!$student_id || !StudentInfo::find($student_id)) {
        Alert::error('Error', 'Student record has been deleted or is invalid.');
        return redirect()->back();
        }

        $student = StudentInfo::find($student_id);
        $englishTests = json_decode($student->english_proficiency, true) ?? [];
        $academicQualifications = json_decode($student->academic_qualifications, true) ?? [];
        return view('agent.application.agent-edit-application', compact('application', 'course', 'student', 'englishTests', 'academicQualifications'));
    }

    // function to agent update application
    public function agentUpdateApplication(Request $request, $id)
    {
        $request->validate([
            'student_id'        => 'required|exists:student_infos,id',
            'course_id'         => 'required|exists:courses,id',
            'name'              => 'required|string|max:50',
            'phone'             => 'required',
            'email'             => 'required|email',
            'dob'               => 'required|date',
            'passport_no'       => 'required|string',
            'permanent_address' => 'required|string',
            'gender'            => 'required',
            'intake_year'       => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Find existing student
            $studentInfo = StudentInfo::find($request->student_id);

            if (!$studentInfo) {
                Alert::error('Error', 'Student not found.');
                return redirect()->back();
            }

            //Update Student Info
            $studentInfo->update([
                'name'              => $request->name,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'dob'               => $request->dob,
                'passport_no'       => $request->passport_no,
                'permanent_address' => $request->permanent_address,
                'gender'            => $request->gender,
                'fathers_name'      => $request->fathers_name,
                'mothers_name'      => $request->mothers_name,
                'moi'               => $request->moi,
                'notes'             => $request->notes,
            ]);

            //Update English Proficiency
            if ($request->has('english_tests')) {
                $englishTests = [];
                foreach ($request->english_tests as $test) {
                    $englishTests[] = [
                        'type'      => $test['type'] ?? null,
                        'listening' => $test['listening'] ?? null,
                        'reading'   => $test['reading'] ?? null,
                        'writing'   => $test['writing'] ?? null,
                        'speaking'  => $test['speaking'] ?? null,
                        'overall'   => $test['overall'] ?? null,
                    ];
                }
                $studentInfo->english_proficiency = json_encode($englishTests);
                $studentInfo->save();
            }

            //Update Academic Qualifications
            if ($request->has('academic_qualifications')) {
                $academicQualifications = [];
                foreach ($request->academic_qualifications as $qualification) {
                    $academicQualifications[] = [
                        'group_name'     => $qualification['group_name'] ?? null,
                        'institute_name' => $qualification['institute_name'] ?? null,
                        'gpa'            => $qualification['gpa'] ?? null,
                        'passing_year'   => $qualification['passing_year'] ?? null,
                    ];
                }
                $studentInfo->academic_qualifications = json_encode($academicQualifications);
                $studentInfo->save();
            }

            //Upload Student Files (if any new files)
            if ($request->hasFile('studentfiles')) {
                $studentFiles = $request->file('studentfiles');
                $filenames = $request->input('filename');

                foreach ($studentFiles as $key => $single) {
                    $originalFileName = $single->getClientOriginalName();
                    $newFileName = Carbon::now()->timestamp . '_' . $studentInfo->name . '_' . $originalFileName;
                    $filePath = $single->storeAs($filenames[$key], $newFileName, 'public');

                    StudentFile::create([
                        'student_id' => $studentInfo->id,
                        'filename'   => $filenames[$key],
                        'filepath'   => $filePath,
                    ]);
                }
            }

            $updateApplication = Application::findOrFail($id);
            $updateApplication->student_id  = $studentInfo->id;
            $updateApplication->course_id   = $request->input('course_id');
            $updateApplication->intake_year = $request->input('intake_year');

            $updateApplication->save();
            DB::commit();
            Alert::success('Success', 'Application update successfully for existing student.');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            Alert::error('Error', 'Failed to update application, Try Again.');
            return redirect()->back()->withInput();
        }
    }
}
