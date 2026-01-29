<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Handler\ImageHandlerController;
use App\Models\Admin\Country;
use App\Models\Admin\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class UniversityController extends Controller
{
    protected $imageHandler;

    // Functio to call image handler
    public function __construct(ImageHandlerController $imageHandler)
    {
        $this->imageHandler = $imageHandler;
    }

   
    public function universityList()
    {
        $universities = University::with('country')->orderBy('id','desc')->paginate(8);
        $countries    = Country::where('status', 1)->get();
        return view('admin.university.university-list', compact('universities','countries'));
    }

    
    // Function to search university by name and country
    public function searchUniversityName(Request $request)
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

        return view('admin.university.university-list', compact('universities', 'countries'));
    }


    // function to add university
    public function addUniversity()
    {
        $countries = Country::where('status', 1)->get();
        return view('admin.university.add-new-univeristy', compact('countries'));
    }

    // function to save university
    public function saveUniversity(Request $request)
    {
       
        $request->validate([
            'university_name'      => 'required',
            'university_city'      => 'required',
            'admission_email'      => 'required',
            'admission_phone'      => 'required',
            'website_link'         => 'required',
            'address'              => 'required',
            'commission_for_us'    => 'required',
            'commission_for_agent' => 'required',
            'description' => 'required',
            'country_id' => 'required|exists:countries,id',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'  => 'required',
            ], [
               'country_id.required' => 'The country field is required.',
        ]);

        DB::beginTransaction();
        try {
        $university = new University();
        $university->university_name      = $request->university_name;
        $university->country_id           = $request->country_id;
        $university->university_city      = $request->university_city;
        $university->admission_email      = $request->admission_email;
        $university->admission_phone      = $request->admission_phone;
        $university->website_link         = $request->website_link;
        $university->commission_for_us    = $request->commission_for_us;
        $university->commission_for_agent = $request->commission_for_agent;
        $university->address              = $request->address;
        $university->description          = $request->description;

        if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $filePath = $this->imageHandler->universityLogo($file);
        $university->logo = $filePath;
      }

      if ($request->hasFile('cover_image')) {
          $file = $request->file('cover_image');
          $filePath = $this->imageHandler->universityCoverImage($file);
          $university->cover_image = $filePath;
        }

      $university->save();
      DB::commit();
      Alert::success('Success', 'University added successfuly');
      return redirect()->back();
    }catch (\Exception $e) {
            DB::rollback();
            Alert::error('Error', 'Failed to save university. Please try again.');
            return redirect()->back();
        }
    }

    // function to edit university
    public function editUniversity($id)
    {
        $countries = Country::all();
        $university = University::findOrFail($id);
        return view('admin.university.edit-university', compact('countries','university'));
    }

    // function to university details
    public function universityDetails($id)
    {
        $universityDetails = University::with('country')->findOrFail($id);
        $randomCourses = $universityDetails->courses()
                                       ->inRandomOrder()
                                       ->take(10)
                                       ->get();
        return view('admin.university.university-details', compact('universityDetails', 'randomCourses'));
    }

    // function to update university
    public function updateUniversity(Request $request, $id )
    {
       
        $request->validate([
            'university_name'      => 'required',
            'university_city'      => 'required',
            'admission_email'      => 'required',
            'admission_phone'      => 'required',
            'website_link'         => 'required',
            'address'              => 'required',
            'commission_for_us'    => 'required',
            'commission_for_agent' => 'required',
            'description'          => 'required',
            'country_id'           => 'required|exists:countries,id',
            'logo'                 => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cover_image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'          => 'required',
            ], [
               'country_id.required' => 'The country field is required.',
        ]);

        DB::beginTransaction();
        try {
        $updateUniversity                       = University::findOrFail($id);
        $updateUniversity->university_name      = $request->university_name;
        $updateUniversity->country_id           = $request->country_id;
        $updateUniversity->university_city      = $request->university_city;
        $updateUniversity->admission_email      = $request->admission_email;
        $updateUniversity->admission_phone      = $request->admission_phone;
        $updateUniversity->website_link         = $request->website_link;
        $updateUniversity->commission_for_us    = $request->commission_for_us;
        $updateUniversity->commission_for_agent = $request->commission_for_agent;
        $updateUniversity->address              = $request->address;
        $updateUniversity->description          = $request->description;

        if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $filePath = $this->imageHandler->countryFlag($file);
        $updateUniversity->logo = $filePath;
      }

      if ($request->hasFile('cover_image')) {
          $file = $request->file('cover_image');
          $filePath = $this->imageHandler->countryCoverPhoto($file);
          $updateUniversity->cover_image = $filePath;
        }

      $updateUniversity->save();
      DB::commit();
      Alert::success('Success', 'University update successfuly');
      return redirect()->back();
    }catch (\Exception $e) {
            DB::rollback();
            Alert::error('Error', 'Failed to update university. Please try again.');
            return redirect()->back();
        }
    }

    // function to delete university
    public function deleteUniversity($id)
    {
        try {
            $university = University::findOrFail($id);
            $university->delete();
            Alert::success('Success', 'University deleted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Error', 'Failed to delete university');
            return redirect()->back();
        }
    }
}
