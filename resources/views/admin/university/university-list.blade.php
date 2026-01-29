@extends('layouts.app')
@section('title', '|University List')
@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/university/university-list.css') }}">
@endpush
@section('content')
    <!-- Begin page -->
    <div class="wrapper">
        <div class="page-container">
            <div class="row mt-2">
                <div class="col-xl-12">
                    <div class="card">
                       <div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                            <h4 class="header-title mb-0">University List</h4>
                            <div class="d-flex items-center gap-2">
                                @can('Create University')
                                <a href="{{ route('add_new_university') }}" class="btn btn-sm glodex-blue-btn">
                                    <i class="ti ti-plus" style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Add New
                                </a>
                                @endcan
                                <a href="{{ route('university_list') }}" class="btn btn-sm glodex-blue-btn" id="addNewCountryBtn">
                                    <i class="ti ti-rotate me-2"></i>
                                    Refresh
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                        {{-- show entries --}}
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="glodex-show-entries">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <!-- Show entries form -->
                                            <form method="GET" action="{{ route('search_university_name') }}" class="d-flex align-items-center gap-2">
                                                <label for="show-entries" class="form-label mb-0">Show</label>
                                                <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                                    <option value="8"  {{ request('per_page') == 8  ? 'selected' : '' }}>8</option>
                                                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                                    <option value="60" {{ request('per_page') == 60 ? 'selected' : '' }}>60</option>
                                                    <option value="120"{{ request('per_page') == 120 ? 'selected' : '' }}>120</option>
                                                </select>
                                                <span>entries</span>

                                                {{-- Keep search filters persistent when changing per_page --}}
                                                @if (request()->has('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                @if (request()->has('university_id'))
                                                    <input type="hidden" name="university_id" value="{{ request('university_id') }}">
                                                @endif
                                            </form>

                                            <!-- Search + Filter form -->
                                            <form action="{{ route('search_university_name') }}" method="GET" class="d-flex align-items-center university-filter-form">
                                                <div class="glodex-show-entries-select">
                                                    <select class="form-control form-select-sm" id="university_id" name="university_id" data-choices>
                                                        <option value="">Select Country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}" {{ request('university_id') == $country->id ? 'selected' : '' }}>
                                                                {{ $country->country_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="glodex-search-field d-flex align-items-center">
                                                    <input
                                                        type="search"
                                                        name="search"
                                                        id="glodex-country-search"
                                                        class="form-control form-control-sm mb-0"
                                                        placeholder="Search university..."
                                                        value="{{ request('search') }}"
                                                    >
                                                    <button type="submit" class="btn btn-sm glodex-blue-btn ms-2">
                                                        <i class="ti ti-search" style="font-size: 1.3rem;"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                @foreach($universities as  $university)
                                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                                        <div class="university-card border rounded-4 shadow-md py-3 px-2 position-relative h-100">
                                            <!-- 3-dot Dropdown -->
                                            <div class="btn-group glodex-custom-3dot-dropdown position-absolute top-0 end-0 m-2">
                                                <button type="button" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @can('View Course')
                                                    <li><a class="dropdown-item" href="{{ url('/admin/filter-course') }}?university_name={{ urlencode($university->university_name) }}">Courses</a></li>
                                                    @endcan
                                                    @can('View University')
                                                    <li><a class="dropdown-item" href="{{ route('university_details', $university->id) }}">University Details</a></li>
                                                    @endcan
                                                    @can('Edit University')
                                                    <li><a class="dropdown-item" href="{{ route('edit_university', $university->id) }}">Edit</a></li>
                                                    @endcan 
                                                    @can('Delete University')
                                                    <li>
                                                        <a class="dropdown-item"  href="#" onclick="confirmDelete({{ $university->id }})">Delete</a>
                                                        <form id="delete-university-form-{{ $university->id }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                            <div class="university-card-header d-flex align-items-center gap-2 pb-3">
                                                <div class="university-logo flex-shrink-0">
                                                    <img src="{{ $university->logo && file_exists(public_path($university->logo)) ? asset($university->logo) : asset('back-end/assets/images/dr-profile/image-upload.jpg') }}" alt="Logo" class="img-fluid">
                                                </div>
                                                <div class="flex-grow-1 text-left">
                                                    <h5 class="university-title mb-0 text-left">{{ $university->university_name }}</h5>
                                                    <p class="university-subtitle mb-0 text-left text-muted">{{ $university->country->country_name ?? 'Not added' }} • {{ $university->university_city ?? 'Not added' }}</p>
                                                </div>
                                            </div>
                                            <div class="card-body-custom">
                                                <div class="contact-item py-1 d-flex align-items-center">
                                                    <div class="contact-icon me-2">
                                                        <i class="ti ti-mail-filled"></i>
                                                    </div>
                                                    <p class="contact-text mb-0">{{ $university->admission_email ?? 'Not added' }}</p>
                                                </div>
                                                <div class="contact-item py-1 d-flex align-items-center">
                                                    <div class="contact-icon me-2">
                                                        <i class="ti ti-phone-filled"></i>
                                                    </div>
                                                    <p class="contact-text mb-0">{{ $university->admission_phone ?? 'Not added' }}</p>
                                                </div>
                                                <div class="contact-item py-1 d-flex align-items-center">
                                                    <div class="contact-icon me-2">
                                                        <i class="ti ti-world-www"></i>
                                                    </div>
                                                    <p class="contact-text mb-0">{{ $university->website_link ?? 'Not added'}}</p>
                                                </div>
                                                <div class="contact-item py-1 d-flex align-items-center">
                                                    <div class="contact-icon me-2">
                                                        <i class="ti ti-map-pin-filled"></i>
                                                    </div>
                                                    <p class="contact-text mb-0">{{ $university->address ?? 'Not added'}}</p>
                                                </div>
                                                <div class="contact-item py-1 d-flex align-items-center">
                                                    <div class="contact-icon me-2">
                                                        <i class="ti ti-currency-dollar"></i>
                                                    </div>
                                                    <span class="badge-commission">Commission {{ $university->commission_for_us ?? 'Not added'}}</span>
                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    @can('View Course')
                                                    <a href="{{ url('/admin/filter-course') }}?university_name={{ urlencode($university->university_name) }}" class="btn btn-sm py-2 btn-gradient mt-3 d-inline-flex align-items-center">
                                                        <i class="ti ti-graduation-cap-filled me-2"></i>
                                                        View Courses
                                                    </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-center">
                                    <div class="pagination-container">
                                        {{ $universities->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <p>
                                        Showing {{ $universities->count() }} records on page {{ $universities->currentPage() }} of {{ $universities->lastPage() }}
                                        (Total: {{ $universities->total() }} records)
                                    </p>
                                </div>
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
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(`delete-university-form-${id}`);
                    form.action = "{{ route('delete_university', '') }}/" + id;
                    form.submit();
                }
            });
        }
    </script>
@endpush
