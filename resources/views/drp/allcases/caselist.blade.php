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
                                <h5 class="mb-0" data-anchor="data-anchor">All Case List</h5>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row my-2 gy-2 border-bottom border-1 pb-3">
                            <div class="col-md-3">
                                <select class="form-control form-select py-1" id="filter_case_type">
                                    <option value="">All Case Types</option>
                                    @foreach (config('constant.case_type') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-select py-1" id="filter_product_type">
                                    <option value="">All Product Types</option>
                                    @foreach (config('constant.product_type') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
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
                                <select class="form-control form-select py-1" id="filter_arbitrator_status">
                                    <option value="">All Confirmations</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control form-select py-1" id="filter_status">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" id="filter_date_from" class="form-control py-1" placeholder="From Date">
                            </div>
                            <div class="col-md-2">
                                <input type="date" id="filter_date_to" class="form-control py-1" placeholder="To Date">
                            </div>
                            <div class="col-md-3 d-flex justify-content-between gap-1">
                                <button class="btn btn-primary w-100 py-1" id="btn-filter">Search</button>
                                <button class="btn btn-secondary w-100 py-1" id="btn-reset">Reset</button>
                            </div>
                        </div>
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
                                        <th>Confirmation</th>
                                        <th>Status</th>
                                        <th>Case File Date</th>
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
                        window.location.href = "{{ route('drp.profile') }}";
                    }
                });
            });
        </script>
    @endif
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                ajax: {
                    url: "{{ route('drp.allcases.caselist') }}",
                    data: function(d) {
                        d.case_type = $('#filter_case_type').val();
                        d.product_type = $('#filter_product_type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.arbitrator_status = $('#filter_arbitrator_status').val();
                        d.status = $('#filter_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                order: [
                    [6, 'desc']
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
                        data: 'arbitrator_status',
                        name: 'arbitrator_status',
                        orderable: false
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
                        name: 'case_type',
                        orderable: false
                    },
                ]
            });

            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            $('#btn-reset').on('click', function() {
                $('#filter_case_type').val('');
                $('#filter_product_type').val('');
                $('#filter_case_number').val('');
                $('#filter_loan_number').val('');
                $('#filter_arbitrator_status').val('');
                $('#filter_status').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                table.ajax.reload();
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-approve-case', function() {
            let caseId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to approve this case!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('drp.allcases.approve') }}", // See route setup below
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            case_id: caseId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Approved!', response.message, 'success');
                                $('.table-datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection