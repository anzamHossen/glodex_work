@extends('layouts.app')
@section('title', '| Course List')
@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/course/course-list.css') }}">
@endpush
@section('content')
<div class="wrapper">
    <div class="page-container">
        <div class="row mt-2">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                        <h4 class="header-title mb-0">Courses</h4>
                        <div class="d-flex items-center gap-1">
                            <a href="{{ route('agent_course_list') }}" class="btn btn-sm glodex-blue-btn" id="addNewCountryBtn">
                                <i class="ti ti-rotate me-2"></i>
                                Refresh
                            </a>
                            <button class="btn btn-sm glodex-blue-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#course_filterBtn" aria-controls="offcanvasRight">
                                <i class="ti ti-filter me-2"></i>
                                Filter
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="glodex-show-entries d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <!-- Show entries dropdown -->
                                    <form method="GET" action="{{ route('agent_search_course') }}" class="d-flex align-items-center gap-2">
                                        <label for="glodex-show-entries" class="form-label mb-0">Show</label>
                                        <select name="per_page" id="glodex-show-entries" class="form-select form-select-sm w-auto"
                                            onchange="this.form.submit()">
                                            <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                        <span>entries</span>
                                        <input type="hidden" name="search_course" value="{{ request('search_course') }}">
                                        <input type="hidden" name="search_country" value="{{ request('search_country') }}">
                                    </form>

                                    <!-- Search form -->
                                    <form action="{{ route('agent_search_course') }}" method="GET" class="d-flex align-items-center align-items-end">
                                        <div class="glodex-search-field glodex-search-field-doble">
                                            <!-- What to Study -->
                                            <input type="search" name="search_course" id="glodex-course-search"
                                                class="form-control form-control-sm mb-0"
                                                value="{{ request('search_course') }}" placeholder="What to Study?">
                                        </div>

                                        <div class="glodex-search-field glodex-search-field-right d-flex align-items-center">
                                            <!-- Where to Study -->
                                            <input type="search" name="search_country" id="glodex-country-search"
                                                class="form-control form-control-sm mb-0"
                                                value="{{ request('search_country') }}" placeholder="Where to Study?">
                                            <button type="submit" class="btn btn-sm glodex-blue-btn">
                                                <i class="ti ti-search" style="font-size: 1.3rem;"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-4">
                            @foreach ($courses as $course)
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-3">
                                    <div class="course-list-card border position-relative">

                                        <!-- 3 Dots Dropdown -->
                                        <div class="btn-group glodex-custom-3dot-dropdown">
                                            <button type="button" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('agent_course_details', $course->id) }}"><i class="ti ti-map-pin me-2"></i> CourseDetails</a></li>
                                            </ul>
                                        </div>

                                        <!-- Card Header Section -->
                                        <div class="card-header-section">
                                            <h1 class="course-title">{{ $course->course_name ?? 'Not Added' }}</h1>
                                            <div class="university-info">
                                                <div class="university-details">
                                                    <h3>{{ $course->university->university_name ?? 'Not Added' }}</h3>
                                                    <p><i class="ti ti-flag"></i>{{ $course->country->country_name ?? 'Not Added' }}</p>
                                                </div>
                                                <div class="university-logo">
                                                   <img src="{{ $course->university && $course->university->logo && file_exists(public_path($course->university->logo))
                                                            ? asset($course->university->logo)
                                                            : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                    alt="{{ $course->university->university_name ?? 'University Logo' }}"
                                                    class="img-fluid rounded-4">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Body Section -->
                                        <div class="card-body-section">
                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-school" style="font-size: 18px;"></i> Program Level
                                                    </h4>
                                                    <div class="info-value"> {{ $course->CourseProgram->course_program ?? 'N/A' }}</div>
                                                </div>

                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-calendar-check" style="font-size: 18px;"></i> Intake Month
                                                    </h4>
                                                    <div class="info-value">{{ $course->intake_month_names ?? 'Not Added' }}/Year</div>
                                                </div>
                                            </div>

                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-brand-framer" style="font-size: 18px;"></i> Application Fees
                                                    </h4>
                                                    <div class="info-value price">{{ $course->application_fee ?? 'Not Added' }}</div>
                                                </div>

                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-currency-dollar" style="font-size: 18px;"></i> Tuition Fees/Year
                                                    </h4>
                                                    <div class="info-value price">{{ $course->tuition_fee_per_year ?? 'Not Added' }}</div>
                                                </div>
                                            </div>

                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-brand-codepen" style="font-size: 18px;"></i> Program Length
                                                    </h4>
                                                    <div class="info-value">
                                                        @if($course->program_length == 1)
                                                            1 Year
                                                        @elseif($course->program_length == 2)
                                                            2 Years
                                                        @elseif($course->program_length == 3)
                                                            3 Years
                                                        @elseif($course->program_length == 4)
                                                            4 Years
                                                        @elseif($course->program_length == 5)
                                                            5 Years
                                                        @else
                                                            Not Specified
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <h4 class="d-flex align-items-center gap-1">
                                                        <i class="ti ti-award-off" style="font-size: 18px;"></i>Program Type
                                                    </h4>
                                                    <div class="info-value">Full-Time</div>
                                                </div>
                                            </div>

                                            <div class="pt-2 d-flex justify-content-center">
                                                <a href="#" class="btn btn-sm btn-gradient" target="_blank" data-bs-toggle="modal"
                                                data-bs-target="#modalCenter{{ $course->id }}">Apply Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="modalCenter{{ $course->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalCenterTitle">Start Application For
                                                    {{ $course->course_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 text-center">
                                                        <a href="javascript:void(0);" 
                                                            class="btn btn-dark w-100 d-flex align-items-center justify-content-center" 
                                                            style="height: 60px;  font-size: 18px;"
                                                            onclick="toggleSelect('existingStudentSelect{{ $course->id }}')">
                                                                <i class="ti ti-search me-1"></i> Existing Student
                                                        </a>

                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <a href="{{ route('agent_application_new_student', ['course_id' => $course->id]) }}" class="btn btn-success w-100 d-flex align-items-center justify-content-center"
                                                            style="height: 60px; font-size: 18px;">
                                                            <i class="ti ti-pencil me-1"></i> New Student
                                                        </a>
                                                    </div>
                                                    <div id="existingStudentSelect{{ $course->id }}" style="display: none; margin-top: 10px;">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <select id="studentSelect{{ $course->id }}" data-choices id="choices-single-default"
                                                                        class="form-control"
                                                                        onchange="updateStartButton({{ $course->id }})">
                                                                    <option value="">-- Select Student --</option>
                                                                    @foreach($students as $student)
                                                                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <a href="#" id="startApplicationBtn{{ $course->id }}" 
                                                                class="btn btn-primary w-100"
                                                                onclick="startApplication({{ $course->id }})">
                                                                    Start
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center">
                                <div class="pagination-container">
                                    {{ $courses->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <p>
                                    Showing {{ $courses->count() }} records on page {{ $courses->currentPage() }} of {{ $courses->lastPage() }}
                                    (Total: {{ $courses->total() }} records)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="course_filterBtn" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title" id="offcanvasRightLabel">Course Filter</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form action="{{ route('agent_filter_course') }}" method="GET">
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name:</label>
            <input type="text" class="form-control" name="course_name" id="course_name"
                value="{{ request('course_name') }}" placeholder="Course Name">
        </div>

        <div class="mb-3">
            <label for="university_name" class="form-label">University Name:</label>
            <input type="text" class="form-control" name="university_name" id="university_name"
                value="{{ request('university_name') }}" placeholder="University Name">
        </div>

        <div class="mb-3">
            <label for="country_id" class="form-label">Country Name:</label>
            <select class="form-control" name="country_id" id="country_id" data-choices>
                <option value="">--Select Country--</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->country_name }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label for="tuition_fees" class="form-label">Tuition Fees:</label>
            <input type="text" class="form-control" name="tuition_fees" id="tuition_fees"
                value="{{ request('tuition_fees') }}" placeholder="Tuition Fees">
        </div>

        <div class="mb-3">
            <label for="application_fees" class="form-label">Application Fees</label>
            <input type="text" class="form-control" name="application_fees" id="application_fees"
                value="{{ request('application_fees') }}" placeholder="Application Fees">
        </div>

        <div class="mb-3">
            <label for="program_level" class="form-label">Program Level:</label>
            <select name="program_level" id="program_level" class="form-control">
                <option value="">-- Select Program Level --</option>
                @foreach ($coursePrograms as $program)
                    <option value="{{ $program->id }}" {{ request('program_level') == $program->id ? 'selected' : '' }}>
                        {{ $program->course_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="program_length" class="form-label">Program Length:</label>
            <select class="form-control" id="program_length" name="program_length">
                <option value="">--Select Program Length--</option>
                <option value="1" {{ request('program_length') == 1 ? 'selected' : '' }}>1 Year</option>
                <option value="2" {{ request('program_length') == 2 ? 'selected' : '' }}>2 Years</option>
                <option value="3" {{ request('program_length') == 3 ? 'selected' : '' }}>3 Years</option>
                <option value="4" {{ request('program_length') == 4 ? 'selected' : '' }}>4 Years</option>
                <option value="5" {{ request('program_length') == 5 ? 'selected' : '' }}>5 Years</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm glodex-blue-btn">Apply Filter</button>
    </form>
  </div>
</div>
<!-- END wrapper -->
@endsection
@push('page-js')
  <script>
      document.addEventListener('DOMContentLoaded', function() {
        const canvas = new bootstrap.Offcanvas('#course-rbnss-canvaas');
        document.querySelector('#course-customFilterBtn').addEventListener('click', function() {
            canvas.show();
        });
      });
    </script>
    <script>
        function toggleSelect(selectId) {
            const selectDiv = document.getElementById(selectId);
            selectDiv.style.display = (selectDiv.style.display === "none" || selectDiv.style.display === "") 
                ? "block" 
                : "none";
        }

        function updateStartButton(courseId) {
            const select = document.getElementById('studentSelect' + courseId);
            const startButton = document.getElementById('startApplicationBtn' + courseId);
            const selectedStudentId = select.value;

            // Store selected student in a data attribute
            startButton.setAttribute('data-student-id', selectedStudentId);
        }

        function startApplication(courseId) {
            const startButton = document.getElementById('startApplicationBtn' + courseId);
            const selectedStudentId = startButton.getAttribute('data-student-id');

            if (!selectedStudentId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select a student first!',
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }

            const url = "{{ route('agent_application_existing_student', ['course_id' => '__course_id__', 'student_id' => '__student_id__']) }}"
                .replace('__course_id__', courseId)
                .replace('__student_id__', selectedStudentId);

            // Redirect to that page
            window.location.href = url;
        }
    </script>
@endpush
