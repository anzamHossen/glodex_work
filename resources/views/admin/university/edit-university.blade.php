@extends('layouts.app')
@section('title', '|Edit University')
@section('content')
    <!-- Begin page -->
    <div class="wrapper">
        <div class="page-container">
            <div class="row mt-2">
                <div class="col-xl-12">
                    <div class="card">
                        <div
                            class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                            <h4 class="header-title mb-0">Edit University</h4>
                            <div class="d-flex">
                                <a href="{{ route('university_list') }}" class="btn btn-sm btn-secondary me-2">
                                    <i class="ti ti-arrow-back-up"
                                        style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Go Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="universityForm" action="{{ route('update_university', $university->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="university_name" class="form-label">University Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="university_name" name="university_name" value="{{ old('university_name', $university->university_name ?? '') }}" placeholder="Enter university name" required>
                                        @error('university_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="country_id" name="country_id" required>
                                            <option value="">--Select Country--</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" 
                                                    {{ old('country_id', $university->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->country_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="university_city" class="form-label">City<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="university_city" name="university_city" value="{{ old('university_city', $university->university_city ?? '') }}" placeholder="Enter university city" required>
                                        @error('university_city')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="admission_email" class="form-label">Email<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="admission_email" name="admission_email" value="{{ old('admission_email', $university->admission_email ?? '') }}" placeholder="Enter admission email " required>
                                        @error('admission_email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="admission_phone" class="form-label">Phone<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="admission_phone" name="admission_phone" value="{{ old('admission_phone', $university->admission_phone ?? '') }}" placeholder="Enter admission phone " required>
                                        @error('admission_phone')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="commission" class="form-label">Commision For Us<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="commission" name="commission_for_us" value="{{ old('commission_for_us', $university->commission_for_us ?? '') }}" placeholder="Enter commision" required>
                                        @error('commission_for_us')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="commission" class="form-label">Commision For Agent<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="commission" name="commission_for_agent" value="{{ old('commission_for_agent', $university->commission_for_agent ?? '') }}" placeholder="Enter commision" required>
                                        @error('commission_for_agent')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="website_link" class="form-label">Website link<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="website_link" name="website_link" value="{{ old('website_link', $university->website_link ?? '') }}" placeholder="Enter website link " required>
                                        @error('website_link')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="address" class="form-label">Location<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $university->address ?? '') }}" placeholder="Enter university address" required>
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label for="logo" class="form-label">Logo<span class="text-danger">*</span>:</label>
                                        <!-- Preview Box -->
                                        <div class="col-md-3">
                                            <div class="photo-preview overflow-hidden shadow"
                                                style="width: 160px; height: 160px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <img id="preview"
                                                    src="{{ $university->logo && file_exists(public_path($university->logo)) ? asset($university->logo) : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                    class="w-100 img-fluid"
                                                    style="object-fit: cover; width: 100%; height: 100%; border-radius: 10px; {{ $university->logo ? '' : 'display: none;' }}"
                                                    alt="Image Preview">
                                            </div>
                                        </div>

                                        <!-- File Input for university logo -->
                                        <img id="image_preview" src="#" alt="Image Preview"
                                            style="display: none; margin-top: 10px; max-width: 100%; height: auto;" />
                                        <div class="fallback mt-3">
                                            <input type="file" name="logo" class="form-control"
                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                id="country_image_input" onchange="previewImage(event)" required />
                                        </div>
                                        @error('logo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mt-2">
                                        <label for="cover_image" class="form-label">Cover Photo<span
                                                class="text-danger">*</span>:</label>

                                        <!-- Preview Box -->
                                        <div class="col-md-3">
                                            <div class="photo-preview overflow-hidden shadow"
                                                style="width: 160px; height: 160px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <img id="popular_preview"
                                                    src="{{ $university->cover_image && file_exists(public_path($university->cover_image)) ? asset($university->cover_image) : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                    class="w-100 img-fluid"
                                                    style="object-fit: cover; width: 100%; height: 100%; border-radius: 10px; {{ $university->cover_image ? '' : 'display: none;' }}"
                                                    alt="Image Preview">
                                            </div>
                                        </div>

                                        <!-- File Input for University Image -->
                                        <img id="popular_image_preview" src="#" alt="Image Preview"
                                            style="display: none; margin-top: 10px; max-width: 100%; height: auto;" />
                                        <div class="fallback mt-3">
                                            <input type="file" name="cover_image" class="form-control"
                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                id="popular_image_input" onchange="popularPreviewImage(event)" required />
                                        </div>
                                        @error('cover_image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label for="description" class="form-label">Description<span class="text-danger">*</span></label>
                                        <div id="snow-editor" style="height: 400px;">
                                            {!! old('description', $university->description) !!}
                                        </div>
                                        <input type="hidden" name="description" id="description">
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
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
            preview.src = '{{ $university->logo ? asset($university->logo) : '' }}';
            preview.style.display = '{{ $university->logo ? 'block' : 'none' }}';
        }
    }
</script>
<script>
    function popularPreviewImage(event) {
        const preview = document.getElementById('popular_preview');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '{{ $country->cover_image ? asset($country->cover_image) : '' }}';
            preview.style.display = '{{ $country->cover_image ? 'block' : 'none' }}';
        }
    }
</script>
<script>
    document.querySelector("#universityForm").addEventListener("submit", function() {
        document.querySelector("#description").value = quill.root.innerHTML;
    });

    @if(old('description'))
        quill.root.innerHTML = {!! json_encode(old('description')) !!};
    @endif 
</script>
@endpush
