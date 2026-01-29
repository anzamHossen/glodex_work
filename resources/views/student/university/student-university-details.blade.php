@extends('layouts.app')
@section('title', '| University Details')
@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/country/country-details.css') }}">
    <link rel="stylesheet" href="{{ asset('css/university/university-details.css') }}">
@endpush
@section('content')
    <!-- Begin page -->
    <div class="wrapper">
        <div class="page-container">
            <div class="row mt-2">
                <div class="col-xl-12">
                    <div class="card">
                        <div
                            class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                            <h4 class="header-title mb-0">University Details</h4>
                            <div class="d-flex">
                                <a href="{{ route('student_university_list') }}" class="btn btn-sm btn-gradient">
                                    <i class="ti ti-arrow-back-up"
                                        style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Go Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="country-details-container">
                                <section class="py-5">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12">
                                                <div
                                                    class="country-details-cover-photo border-0 shadow-sm position-relative">
                                                    <img class="img-fluid"
                                                        src="{{ $universityDetails->cover_image && file_exists(public_path($universityDetails->cover_image)) ? asset($universityDetails->cover_image) : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                        alt="cover photo">
                                                    <div class="country-details-top-info">
                                                        <div class="text-white country-details-top-info-inner">
                                                            <h1 class="display-5 fw-bold mb-2">
                                                                {{ $universityDetails->country->country_name ?? 'Not Added' }}
                                                            </h1>
                                                            <p class="lead">
                                                                Discover world-class education opportunities in one of the
                                                                most prestigious academic destinations.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="country-details-cover-bottom-flag">
                                                        <img src="{{ $universityDetails->logo && file_exists(public_path($universityDetails->logo)) ? asset($universityDetails->logo) : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                            alt="United Kingdom flag" class="img-fluid rounded-circle">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="py-4">
                                    <div class="container">
                                        <div class="info-section row">
                                            <div class="col-md-12">
                                                <h2 class="section-title">
                                                    <i class="bi bi-building"></i>University Information
                                                </h2>
                                            </div>
                                        </div>

                                        <div class="row pb-4">
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">University Name</div>
                                                    <div class="info-value">{{ $universityDetails->university_name }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">Country</div>
                                                    <div class="info-value">
                                                        {{ $universityDetails->country->country_name ?? 'Not Added' }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">City</div>
                                                    <div class="info-value">
                                                        {{ $universityDetails->university_city ?? 'Not Added' }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">Email</div>
                                                    <div class="info-value">
                                                        <a href="#" class="text-decoration-none text-white">
                                                            <i
                                                                class="bi bi-envelope"></i>{{ $universityDetails->admission_email ?? 'Not Added' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">Phone</div>
                                                    <div class="info-value">
                                                        <i
                                                            class="bi bi-telephone"></i>{{ $universityDetails->admission_phone ?? 'Not Added' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-label">Website</div>
                                                    <div class="info-value">
                                                        <a href="#" target="_blank"
                                                            class="text-decoration-none text-white">
                                                            <i
                                                                class="bi bi-globe"></i>{{ $universityDetails->website_link ?? 'Not Added' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="description-box">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h3 class="section-title">
                                                        <i class="ti ti-file-text"></i>About the University
                                                    </h3>
                                                    <h4 class="text-white"> {!! $universityDetails->description !!}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="py-4 country-details-universities-list">
                                    <div class="container">
                                        <div class="text-center pb-3">
                                            <h2 class="h2 fw-bold text-white mb-3">Top Courses</h2>
                                            <p class="text-white opacity-75">
                                                Explore the leading courses offering world-class education
                                            </p>
                                        </div>

                                        {{-- Owl Carousel Wrapper --}}
                                        <div class="owl-carousel owl-theme glodex-details-carousel" id="university-details-universities-carousel">
                                            @forelse ($randomCourses as $course)
                                                <div class="glodex-details-carousel-item">
                                                    <div class="glodex-details-carousel-image position-relative rounded-4 overflow-hidden">
                                                        <img 
                                                            src="{{ $course->course_photo && file_exists(public_path($course->course_photo)) 
                                                            ? asset($course->course_photo) 
                                                            : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}"
                                                            alt="{{ $course->course_name }}"
                                                            class="w-100 object-fit-cover img-fluid">

                                                        <!-- Overlay -->
                                                        <div class="glodex-details-carousel-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                                            <h4 class="text-white fw-bold mb-2">
                                                                {{ $course->course_name }}
                                                            </h4>
                                                            <a href="{{ route('course_details', $course->id) }}" class="btn btn-sm btn-gradient">View Details</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <h1 class="text-white h-3 text-center">
                                                    No courses available for this university.
                                                </h1>
                                            @endforelse

                                        </div>
                                    </div>
                                </section>
                                <section class="pb-4 text-center">
                                    <div class="container">
                                        <a href="#" class="btn btn-gradient btn-lg text-white border-0 d-inline-flex align-items-center gap-2"
                                            >
                                            <i class="ti ti-arrow-left"></i>
                                            Back to all courses
                                        </a>
                                    </div>
                                </section>
                            </div>
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
        $("#university-details-universities-carousel").owlCarousel({
            loop: true,
            margin: 15,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                767: {
                    items: 2
                },
                991: {
                    items: 3
                },
                1199: {
                    items: 3
                },
                1499: {
                    items: 4
                }
            }
        });
    </script>
@endpush
