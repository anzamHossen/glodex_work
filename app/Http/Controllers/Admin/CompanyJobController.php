<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Company;
use App\Models\Admin\CompanyJob;
use App\Models\Admin\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CompanyJobController extends Controller
{
    public function addNewJob()
    {
        $countries = Country::where('status', 1)->get();
        $companies = Company::where('status', 1)->get();    
        return view('admin.job.add-new-job', compact('countries', 'companies'));
    }

public function saveNewJob(Request $request)
{
    $request->validate([
        'company_id'         => 'required|exists:companies,id',
        'country_id'         => 'required|exists:countries,id',
        'job_name'           => 'required',
        'avilable_positions' => 'required',
        'job_type'           => 'required',
        'experience_level'   => 'required',
        'intial_fees'        => 'required',
        'job_location'       => 'required',
        'job_details'        => 'required',
    ]);

    DB::beginTransaction();
    try {
        $job = new CompanyJob(); 
        $job->company_id         = $request->company_id;
        $job->country_id         = $request->country_id;
        $job->job_name           = $request->job_name;
        $job->avilable_positions = $request->avilable_positions;
        $job->job_type           = $request->job_type;
        $job->experience_level   = $request->experience_level;
        $job->intial_fees        = $request->intial_fees;
        $job->job_location       = $request->job_location;
        $job->job_details        = $request->job_details;
        $job->save();

        DB::commit();
        Alert::success('Success', 'Job added successfully');
        return redirect()->back();
    } catch (\Exception $e) {
        DB::rollback();
        // dd($e);
        Alert::error('Error', 'Failed to save job. Please try again.');
        return redirect()->back();
    }
}

           
}
