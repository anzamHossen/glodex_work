@extends('layouts.app')
@section('title', '| Add New Course')
@section('content')
    <!-- Begin page -->
    <div class="wrapper">
        <div class="page-container">
            <div class="row mt-2">
                <div class="col-xl-12">
                    <div class="card">
                        <div
                            class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                            <h4 class="header-title mb-0">Add Course</h4>
                            <div class="d-flex">
                                <a href="{{ route('course_list') }}" class="btn btn-sm btn-secondary me-2">
                                    <i class="ti ti-arrow-back-up"
                                        style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Go Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="courseForm" action="{{ route('save_new_course') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="course_name" name="course_name" value="{{ old('course_name') }}" placeholder="Enter course name" required>
                                        @error('course_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="university_id" class="form-label">University Name <span class="text-danger">*</span></label>
                                        <select class="form-control"  id="university_id" name="university_id" data-choices id="choices-single-default" required>
                                                <option value="">--Select University--</option>
                                                @foreach ($universities as $university)
                                                    <option value="{{ $university->id }}" {{ old('university_id') == $university->id ? 'selected' : '' }}>
                                                       {{ $university->university_name }}
                                                    </option>
                                                @endforeach
                                        </select>
                                        @error('university_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="conuntry_id" class="form-label">Country<span class="text-danger">*</span></label>
                                        <select class="form-control"  id="country_id" name="country_id" data-choices id="choices-country-default" required>
                                                <option value="">--Select Country--</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                       {{ $country->country_name }}
                                                    </option>
                                                @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="course_program_id" class="form-label">Program Level<span class="text-danger">*</span></label>
                                        <select class="form-control"  id="course_program_id" name="course_program_id" required>
                                            <option value="">--Select Program--</option>
                                            @foreach ($coursePrograms as $program)
                                                <option value="{{ $program->id }}" {{ old('course_program_id') == $program->id ? 'selected' : '' }}>
                                                    {{ $program->course_program }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_program_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="intake_month_id" class="form-label">Intake Month <span class="text-danger">*</span></label>
                                        <select class="form-control js-choices" name="intake_month_id[]" id="intake_month_id" multiple required>
                                            @foreach ($intakeMonths as $month)
                                                <option value="{{ $month->id }}"
                                                    @if(is_array(old('intake_month_id')) && in_array($month->id, old('intake_month_id'))) selected @endif>
                                                    {{ $month->month_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('intake_month_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="application_fee" class="form-label">Application Fees<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="application_fee" name="application_fee" value="{{ old('application_fee') }}" placeholder="Enter application fees" required>
                                        @error('application_fee')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="tuition_fee_per_year" class="form-label">Tuition Fees/Year<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tuition_fee_per_year" name="tuition_fee_per_year" value="{{ old('tuition_fee_per_year') }}" placeholder="Enter tuition fees" required>
                                        @error('tuition_fee_per_year')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="program_length" class="form-label">Program Length<span class="text-danger">*</span></label>
                                        <select class="form-control"  id="program_length" name="program_length" required>
                                            <option value="">--Select Program Length--</option>
                                            <option value="1">1 Year</option>
                                            <option value="2">2 Year</option>
                                            <option value="3">3 Year</option>
                                            <option value="4">4 Year</option>               
                                            <option value="5">5 Year</option>
                                        </select>
                                        @error('program_length')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label for="countryFlag" class="form-label">Course Photo<span
                                                class="text-danger">*</span>:</label>

                                        <!-- Preview Box -->
                                        <div class="col-md-3">
                                            <div class="photo-preview overflow-hidden shadow"
                                                style="width: 160px; height: 160px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <img id="preview"
                                                    src="{{ asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                    class="w-100 img-fluid"
                                                    style="object-fit: cover; width: 100%; height: 100%; border-radius: 10px;"
                                                    alt="Image Preview">
                                            </div>
                                        </div>

                                        <!-- File Input for Country Image -->
                                        <img id="image_preview" src="#" alt="Image Preview"
                                            style="display: none; margin-top: 10px; max-width: 100%; height: auto;" />
                                        <div class="fallback mt-3">
                                            <input type="file" name="course_photo" class="form-control"
                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                id="country_image_input" onchange="previewImage(event)" required />
                                        </div>
                                        @error('course_photo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label for="course_details" class="form-label">Course Details<span class="text-danger">*</span></label>
                                        <div id="snow-editor" style="height: 400px;">
                                            {!! old('course_details', '<h3><span class="ql-size-large">Add Your Content Here....</span></h3>') !!}
                                        </div>
                                        <input type="hidden" name="course_details" id="course_details">
                                        @error('course_details')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END wrapper -->
@endsection
@push('page-js')
   <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('image_preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.js-choices').forEach(function (el) {
                if (el.dataset.choicesInitialized) return;

                new Choices(el, {
                removeItemButton: true,
                shouldSort: false,        
                shouldSortItems: false, 
                searchEnabled: true,
                });

                el.dataset.choicesInitialized = '1';
            });
        });
    </script>

    <script>
        document.querySelector("#courseForm").addEventListener("submit", function() {
            document.querySelector("#course_details").value = quill.root.innerHTML;
        });

        @if(old('course_details'))
            quill.root.innerHTML = {!! json_encode(old('course_details')) !!};
        @endif 
    </script>
@endpush
