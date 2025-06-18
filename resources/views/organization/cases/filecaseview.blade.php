@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <div class="container my-xl-5 my-lg-4 my-3">
        <div class="row">
            <div class="col-md-3 col-12">

                @include('front.includes.sidebar_inner')

            </div>

            <div class="col-md-9 col-12">
                <div class="card mb-3 card-inner form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Case File List</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="{{ route('organization.cases.filecase') }}"
                                        class="btn btn-outline-secondary py-1">
                                        <i class="fa fa-plus me-1"></i>
                                        File Multiple Cases
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <form id="filter-form" class="row my-2 gy-2 border-bottom border-1 pb-3">
                            <div class="col-md-3">
                                <select id="filter_case_type" class="form-control form-select py-1">
                                    <option value="">All Case Types</option>
                                    @foreach (config('constant.case_type') as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filter_product_type" class="form-control form-select py-1">
                                    <option value="">All Product Types</option>
                                    @foreach (config('constant.product_type') as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_case_number" class="form-control py-1"
                                    placeholder="Enter Case Number">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_loan_number" class="form-control py-1"
                                    placeholder="Enter Loan Number">
                            </div>
                            <div class="col-md-3">
                                <select id="filter_status" class="form-control form-select py-1">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_claimant_name" class="form-control py-1"
                                    placeholder="Enter Claimant Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_claimant_mobile" class="form-control py-1"
                                    placeholder="Enter Claimant Mobile">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_respondent_name" class="form-control py-1"
                                    placeholder="Enter Respondent Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="filter_respondent_mobile" class="form-control py-1"
                                    placeholder="Enter Respondent Mobile">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="filter_start_date" class="form-control py-1"
                                    placeholder="Start Date">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="filter_end_date" class="form-control py-1" placeholder="End Date">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" id="filter-search"
                                    class="btn btn-primary w-100 btn-sm me-2 py-1">Search</button>
                                <button type="button" id="filter-reset"
                                    class="btn btn-secondary w-100 btn-sm py-1">Reset</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body table-padding">
                        <div class="table-responsive scrollbar">
                            <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                                style="width:100%">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>Case Type</th>
                                        <th>Product Type</th>
                                        <th>Case Number</th>
                                        <th>Loan Number</th>
                                        <th>Claimant Name</th>
                                        <th>Claimant Mobile</th>
                                        <th>Respondent Name</th>
                                        <th>Respondent Mobile</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    @if (session('showProfilePopup') || isset($showProfilePopup))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Profile Incomplete!",
                    text: "Please complete your profile before proceeding.",
                    icon: "warning",
                    confirmButtonText: "Update Now",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showCancelButton: false,
                    showCloseButton: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('organization.profile') }}";
                    }
                });
            });
        </script>
    @endif
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('organization.cases.filecaseview') }}",
                    data: function(d) {
                        d.case_type = $('#filter_case_type').val();
                        d.product_type = $('#filter_product_type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.claimant_first_name = $('#filter_claimant_name').val();
                        d.claimant_mobile = $('#filter_claimant_mobile').val();
                        d.respondent_first_name = $('#filter_respondent_name').val();
                        d.respondent_mobile = $('#filter_respondent_mobile').val();
                        d.status = $('#filter_status').val();
                        d.start_date = $('#filter_start_date').val();
                        d.end_date = $('#filter_end_date').val();
                    }
                },
                order: [
                    [9, 'desc']
                ],
                columns: [{
                        data: 'case_type',
                        name: 'case_type'
                    },
                    {
                        data: 'product_type',
                        name: 'product_type'
                    },
                    {
                        data: 'case_number',
                        name: 'case_number'
                    },
                    {
                        data: 'loan_number',
                        name: 'loan_number'
                    },
                    {
                        data: 'claimant_first_name',
                        name: 'claimant_first_name'
                    },
                    {
                        data: 'claimant_mobile',
                        name: 'claimant_mobile'
                    },
                    {
                        data: 'respondent_first_name',
                        name: 'respondent_first_name'
                    },
                    {
                        data: 'respondent_mobile',
                        name: 'respondent_mobile'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ]
            });

            $('#filter-search').click(function() {
                table.draw();
            });

            $('#filter-reset').click(function() {
                $('#filter-form')[0].reset();
                table.draw();
            });



            $(document).on('click', ".delete", function() {
                var id = $(this).data('id')
                Swal.fire(deleteMessageSwalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('organization.cases.filecaseview.delete') }} ",
                            data: {
                                'id': id
                            },
                            type: 'DELETE',
                            success: function(data) {
                                if (data.status) {
                                    Swal.fire('', data?.message, "success")
                                    table.draw();
                                } else {
                                    toastr.error(data?.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
