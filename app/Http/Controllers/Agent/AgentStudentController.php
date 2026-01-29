<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Admin\StudentFile;
use App\Models\Admin\StudentInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;


class AgentStudentController extends Controller
{
     // function to show my student list
    public function agentStudentList()
    {
        $students = StudentInfo::where('created_by', Auth::id())->get();
        return view('agent.student.agent-student-list', compact('students'));
    }
    
    // function to show add new student page
    public function agentAddNewStudent()
    {
        return view('agent.student.agent-add-new-student');
    }
    
    // Function to save student
    public function agentSaveNewStudent(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'phone'       => 'required',
            'email'       => 'required',
            'dob'         => 'required',
            'passport_no'  => 'required',
            'permanent_address'  => 'required',
            'gender'  => 'required',
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

        $user = User::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'password'  => Hash::make('student1234'),
            'created_by'=> Auth::id(),
            'user_type' => 3, //Student
        ]);

        $studentInfo                              = new StudentInfo();
        $studentInfo->user_id                     = $user->id;
        $studentInfo->sent_by                     = Auth::user()->organization_name ?? 'N/A';
        $studentInfo->name                        = $request->name;
        $studentInfo->student_code                =  'GLD' . rand(1000000, 9999999);
        $studentInfo->phone                       = $request->phone;
        $studentInfo->email                       = $request->email;
        $studentInfo->permanent_address           = $request->permanent_address;
        $studentInfo->gender                      = $request->gender;
        $studentInfo->fathers_name                = $request->fathers_name;
        $studentInfo->mothers_name                = $request->mothers_name;
        $studentInfo->dob                         = $request->dob;
        $studentInfo->passport_no                 = $request->passport_no;
        $studentInfo->moi                         = $request->moi;
        $studentInfo->notes                       = $request->notes;
        $studentInfo->created_by                  = Auth::id();

        // Save English Proficiency
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


        // Save Academic Qualifications
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

        $studentFiles  = $request->file('studentfiles');
        $filenames     = $request->input('filename');           
        $studenId      = $studentInfo->id;
        foreach ($studentFiles as $key => $single) {
        $originalFileName = $single->getClientOriginalName();
        $originalFileName2 = Carbon::now()->timestamp . $studentInfo->name . $originalFileName;
        $filePath = $single->storeAs($filenames[$key], $originalFileName2, 'public');

        $studentFile                 = new StudentFile();
        $studentFile->student_id     = $studenId;
        $studentFile->filename       = $filenames[$key];
        $studentFile->filepath       = $filePath;
        $studentFile->save();
        }
        DB::commit();
        Alert::success('Success', 'Student added successfully');
        return redirect()->back();
        }catch (\Exception $e) {
            DB::rollBack();
            //   dd($e);
            Alert::error('Error', 'Failed to save student, Try Again');
            return redirect()->back();
        }
    }

    // Function to show edit student page
    public function agentEditStudent($id)
    {
        $student = StudentInfo::findOrFail($id);
        $englishTests = json_decode($student->english_proficiency, true) ?? [];
        $academicQualifications = json_decode($student->academic_qualifications, true) ?? []; // ← Add this line
        return view('agent.student.agent-edit-student', compact('student', 'englishTests', 'academicQualifications'));
    }

    // function to update student
    public function agentUpdateStudent(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'phone'       => 'required',
            'email'       => 'required',
            'dob'         => 'required',
            'passport_no'  => 'required',
            'permanent_address'  => 'required',
            'gender'  => 'required',
        ]);

        DB::beginTransaction();
        try {

        $studentInfo                             = StudentInfo::findOrFail($id);
        $studentInfo->name                        = $request->name;
        $studentInfo->phone                       = $request->phone;
        $studentInfo->email                       = $request->email;
        $studentInfo->permanent_address           = $request->permanent_address;
        $studentInfo->gender                      = $request->gender;
        $studentInfo->fathers_name                = $request->fathers_name;
        $studentInfo->mothers_name                = $request->mothers_name;
        $studentInfo->dob                         = $request->dob;
        $studentInfo->passport_no                 = $request->passport_no;
        $studentInfo->moi                         = $request->moi;
        $studentInfo->notes                       = $request->notes;

        // Save English Proficiency
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

        // Save Academic Qualifications
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

        
        if ($request->hasFile('studentfiles')) {
          $studentFiles = $request->file('studentfiles');
          $filenames = $request->input('filename');

          foreach ($studentFiles as $key => $single) {
            $filename = $filenames[$key];
            $originalFileName = $single->getClientOriginalName();
            $originalFileName2 = Carbon::now()->timestamp . $studentInfo->name . $originalFileName;
            $filePath = $single->storeAs($filename, $originalFileName2, 'public');

            $studentFile = StudentFile::where('student_id', $studentInfo->id)
              ->where('filename', $filename)
              ->first();
            if ($studentFile) {
              $studentFile->filepath = $filePath;
              $studentFile->save();
            } else {
              $studentFile                 = new StudentFile();
              $studentFile->student_id     = $studentInfo->id;
              $studentFile->filepath       = $filePath;
              $studentFile->filename       = $filename;
              $studentFile->save();
            }
          }
        }
        DB::commit();
        Alert::success('Success', 'Student added successfully');
        return redirect()->back();
        }catch (\Exception $e) {
            DB::rollBack();
            //   dd($e);
            Alert::error('Error', 'Student Already Exists');
            return redirect()->back();
        }
    }
}
