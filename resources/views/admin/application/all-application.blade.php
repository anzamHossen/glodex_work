@extends('layouts.app')
@section('title', '| All Application List')
@push('page-css')
    <link rel="stylesheet" href="{{ asset('css/custom-status.css') }}">
@endpush
@section('content')
    <!-- Begin page -->
    <div class="wrapper">
        <div class="page-container">
            <div class="row mt-2">
                <div class="col-xl-12">
                    <div class="card">
                       <div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                            <h4 class="header-title mb-0">All Application</h4>
                            <div class="d-flex">
                                <a href="{{ route('admin_dashboard') }}" class="btn btn-sm btn-secondary me-2">
                                    <i class="ti ti-arrow-back-up" style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Go Back 
                                </a>
                                @can('Create Application')
                                <a href="{{ route('course_list') }}" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#centermodal">
                                    <i class="ti ti-plus" style="margin-right:3px; font-size: 1.3rem; margin-bottom: 1px"></i>
                                    Add New
                                </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table id="dataTable" class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Status</th>
                                            <th>Application ID</th>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Intake</th>
                                            <th>University</th>
                                            <th>Application Date</th>
                                            <th>Sent By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applications as $application)
                                            <tr>
                                                <td>
                                                    <div class="dropdown position-relative">
                                                        <button
                                                            type="button"
                                                            class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill"
                                                            data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="ti ti-dots-vertical ti-md"></i>
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            @can('Edit All Application')
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-1" href="{{ $application->student
                                                                    ? route('edit_application', [
                                                                        'student_id' => $application->student->id,
                                                                        'course_id' => $application->course->id,
                                                                        'id' => $application->id,
                                                                    ])
                                                                    : '#' }}">
                                                                    <i class="ti ti-edit"></i> Edit
                                                                </a>
                                                            </li>
                                                            @endcan
                                                            {{-- <a href="javascript:void(0);" 
                                                                onclick="confirmDelete()" 
                                                                class="dropdown-item d-flex align-items-center gap-1" 
                                                                title="Delete">
                                                                    <i class="ti ti-trash ti-md"></i> <span>Delete</span>
                                                            </a> --}}
                                                        </div>
                                                    </div>
                                                    {{-- <form id="delete-user-form" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form> --}}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'In Progress' => 'badge-in-progress',
                                                            'On Hold' => 'badge-on-hold',
                                                            'Applied' => 'badge-applied',
                                                            'Unconditional Offer Letter' => 'badge-unconditional',
                                                            'Conditional Offer Letter' => 'badge-conditional',
                                                            'Payment' => 'badge-payment',
                                                            'CAS/I20/LOA/COE Confirmation' => 'badge-confirmation',
                                                            'Visa Documentation' => 'badge-documentation',
                                                            'Visa Applied' => 'badge-visa-applied',
                                                            'Visa Granted' => 'badge-visa-granted',
                                                            'Enrolled' => 'badge-enrolled',
                                                            'Visa Rejected' => 'badge-rejected',
                                                            'Canceled' => 'badge-canceled',
                                                        ];

                                                        $statusName = $application->applicationStatus->status_name ?? 'N/A';
                                                        $badgeClass = $statusColors[$statusName] ?? 'badge-canceled';
                                                    @endphp

                                                    <span class="badge {{ $badgeClass }}">{{ $statusName }}</span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-primary">
                                                        {{ $application->application_code ?? 'Not Added' }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-dark">
                                                        {{ $application->student->student_code ?? 'Not Added' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $application->student->name ?? 'Not Added' }}
                                                </td>
                                                <td>
                                                    {{ $application->course->course_name ?? 'Not Added' }}
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-success">
                                                        @if ($application->intake_year && \Carbon\Carbon::hasFormat($application->intake_year, 'Y-m'))
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m', $application->intake_year)->format('F Y') }}
                                                        @else
                                                            Not Added
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $application->course->university->university_name ?? '--' }}
                                                </td>
                                                <td>
                                                    {{ $application->created_at->format('Y-m-d') }}
                                                </td>
                                                <td>
                                                    {{ $application->sent_by ?? 'Not Added' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->
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
                    const form = document.getElementById('delete-user-form');
                    form.action = `/delete-user/${id}`;
                    form.submit();
                }
            });
        }
    </script>
@endpush