<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Applicant;
use App\Models\Admin\ApplicantFile;
use App\Models\Admin\Application;
use App\Models\Admin\ApplicationStatus;
use App\Models\Admin\CompanyJob;
use App\Models\Admin\Course;
use App\Models\Admin\StudentFile;
use App\Models\Admin\StudentInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ApplicationController extends Controller
{
    public function myApplicationList()
    {
        $applications = Application::with([
                'applicant',
                'job.country',
                'job.company',
                'applicationStatus'
            ])
            ->whereHas('createdBy', function ($query) {
                $query->whereIn('user_type', [1, 3]);
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.application.my-application-list', compact('applications'));
    }


    // function to show application list for agent
    public function applicationListAgent()
    {
        $applications = Application::with(['student', 'course.country', 'course.university', 'applicationStatus'])
            ->whereHas('createdBy', function ($query) {
                $query->where('user_type', 2);
            })->orderBy('id', 'desc')->get();
        return view('admin.application.application-list-agent', compact('applications'));
    }

    // function to show all application list
    public function allApplication()
    {
        $authUser = auth()->user();

        $applications = Application::with(['student', 'course.country', 'course.university', 'applicationStatus', 'createdBy'
            ])->when($authUser->hasRole('BDM'), function ($query) use ($authUser) {
                // BDM only applications submitted by agents created by user
                $query->whereHas('createdBy', function ($q) use ($authUser) {
                    $q->where('user_type', 2)  // Only agents
                    ->where('created_by', $authUser->id); // Agent created by this BDM
                });
            })
            // SuperAdmin no filter (sees all)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.application.all-application', compact('applications'));
    }

    // function to add application for new student
    public function addApplicationNewApplicant($job_id)
    {
        $job                = CompanyJob::find($job_id);
        $applicationStatus = ApplicationStatus::all();
        return view('admin.application.add-application-new-applicant', compact('job','applicationStatus'));
    }

    // function to save application for new applicant
    public function saveApplicationNewApplicant(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'phone'       => 'required',
            'email'       => 'required|email',
            'dob'         => 'required|date',
            'passport_no' => 'required|string',
            'permanent_address' => 'required|string',
            'gender'      => 'required',
            'going_year'  => 'required',
            'days'    => 'required|integer|min:0',
            'hours'   => 'required|integer|min:0|max:23',
            'seconds' => 'required|integer|min:0|max:59',
            'job_id'  => 'required|exists:company_jobs,id',
        ]);

        $existingApplicant = Applicant::where('phone', $request->phone)
            ->orWhere('email', $request->email)
            ->orWhere('passport_no', $request->passport_no)
            ->exists();

        if ($existingApplicant) {
            Alert::error('Error', 'Student Already Exists');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();

        try {

            // 🔒 LOCK job row
            $job = CompanyJob::lockForUpdate()->find($request->job_id);

            if (!$job || $job->avilable_positions <= 0) {
                throw new \Exception('No available positions left for this job.');
            }

            // ➖ Decrease available position
            $job->decrement('avilable_positions');

            // Create User
            $user = User::create([
                'name'       => $request->name,
                'phone'      => $request->phone,
                'email'      => $request->email,
                'password'   => Hash::make('applicant1234'),
                'created_by' => Auth::id(),
                'user_type'  => 3,
            ]);

            // Create Applicant
            $applicantInfo = new Applicant();
            $applicantInfo->user_id = $user->id;
            $applicantInfo->sent_by = 'Glodex';
            $applicantInfo->name = $request->name;
            $applicantInfo->applicant_code = 'GLD' . rand(1000000, 9999999);
            $applicantInfo->phone = $request->phone;
            $applicantInfo->email = $request->email;
            $applicantInfo->permanent_address = $request->permanent_address;
            $applicantInfo->gender = $request->gender;
            $applicantInfo->fathers_name = $request->fathers_name;
            $applicantInfo->mothers_name = $request->mothers_name;
            $applicantInfo->dob = $request->dob;
            $applicantInfo->passport_no = $request->passport_no;
            $applicantInfo->moi = $request->moi;
            $applicantInfo->notes = $request->notes;
            $applicantInfo->created_by = Auth::id();

            // English Tests
            if ($request->has('english_tests')) {
                $tests = [];
                foreach ($request->english_tests as $test) {
                    $tests[] = [
                        'type' => $test['type'] ?? null,
                        'listening' => $test['listening'] ?? null,
                        'reading' => $test['reading'] ?? null,
                        'writing' => $test['writing'] ?? null,
                        'speaking' => $test['speaking'] ?? null,
                        'overall' => $test['overall'] ?? null,
                    ];
                }
                $applicantInfo->english_proficiency = json_encode($tests);
            }

            // Academic
            if ($request->has('academic_qualifications')) {
                $academic = [];
                foreach ($request->academic_qualifications as $q) {
                    $academic[] = [
                        'group_name' => $q['group_name'] ?? null,
                        'institute_name' => $q['institute_name'] ?? null,
                        'gpa' => $q['gpa'] ?? null,
                        'passing_year' => $q['passing_year'] ?? null,
                    ];
                }
                $applicantInfo->academic_qualifications = json_encode($academic);
            }

            $applicantInfo->save();

            // Files
            if ($request->hasFile('applicantfiles')) {
                foreach ($request->file('applicantfiles') as $key => $file) {
                    $newName = time().'_'.$file->getClientOriginalName();
                    $path = $file->storeAs('applicants', $newName, 'public');

                    ApplicantFile::create([
                        'applicant_id' => $applicantInfo->id,
                        'filename' => 'applicants',
                        'filepath' => $path,
                    ]);
                }
            }

            // Expiry time
            $expiresAt = now()
                ->addDays((int)$request->days)
                ->addHours((int)$request->hours)
                ->addSeconds((int)$request->seconds);

            // Save Application
            Application::create([
                'user_id' => Auth::id(),
                'job_id' => $job->id,
                'applicant_id' => $applicantInfo->id,
                'sent_by' => 'Glodex',
                'application_code' => rand(100000, 999999),
                'status' => $request->status,
                'created_by' => Auth::id(),
                'going_year' => $request->going_year,
                'expires_at' => $expiresAt,
            ]);

            DB::commit();
            Alert::success('Success', 'Application added & job position updated!');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // function to add application for existing student
    public function addApplicationEixStudent($course_id, $student_id)
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
        $applicationStatus = ApplicationStatus::all();
        $englishTests = json_decode($student->english_proficiency, true) ?? [];
        $academicQualifications = json_decode($student->academic_qualifications, true) ?? [];
        return view('admin.application.add-application-existing-student', compact('course','student','applicationStatus','englishTests','academicQualifications'));
    }

    // function to save application for existing student
    public function saveApplicationEixStudent(Request $request)
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
                'sent_by'     => 'Glodex',
                'application_code' => rand(100000, 999999),
                'status'      => $request->status ?? 'pending',
                'created_by'  => Auth::id(),
                'intake_year' => $request->intake_year,
            ]);

            DB::commit();

            Alert::success('Success', 'Application added successfully for existing student.');
            return redirect()->route('my_application_list');

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            Alert::error('Error', 'Failed to add application, Try Again.');
            return redirect()->back()->withInput();
        }
    }

    // function to edit application
    public function editApplication($id, $course_id, $student_id)
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
        $applicationStatus = ApplicationStatus::all();
        $englishTests = json_decode($student->english_proficiency, true) ?? [];
        $academicQualifications = json_decode($student->academic_qualifications, true) ?? [];
        return view('admin.application.edit-application', compact('application', 'course', 'student', 'applicationStatus', 'englishTests', 'academicQualifications'));
    }

    // function to update application
    public function updateApplication(Request $request, $id)
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
            $updateApplication->status      = $request->input('status') ?? 'In Progress';
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
