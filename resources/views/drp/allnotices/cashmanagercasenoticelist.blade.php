@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet"
        type="text/css" />
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
                                <h5 class="mb-0" data-anchor="data-anchor">All Case Notice List</h5>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row my-2 gy-2 border-bottom border-1 pb-2">
                            <div class="col-md-3">
                                <select id="filter-case-type" class="form-control form-select py-1">
                                    <option value="">All Case Types</option>
                                      @foreach (config('constant.case_type') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                    <!-- add more options based on your `case_type` config -->
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
                                <select class="form-control form-select py-1" id="filter_status">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="filter-from-date" class="form-control py-1" placeholder="From Date">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="filter-to-date" class="form-control py-1" placeholder="To Date">
                            </div>
                            <div class="col-md-2">
                                <button id="btn-search" class="btn btn-primary w-100 py-1">Search</button>
                            </div>
                            <div class="col-md-2">
                                <button id="btn-clear" class="btn btn-secondary w-100 py-1">Clear</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-padding">
                        <div class="table-responsive scrollbar">
                            <table id="fileCasesNoticeTable" class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                                style="width:100%">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>Case Type</th>
                                        <th>Case Number</th>
                                        <th>Loan Number</th>
                                        <th>Status</th>
                                        <th>Case File Date</th>
                                        <th>Notice 1</th>
                                        <th>Notice 1A</th>
                                        <th>Notice 1B</th>
                                        <th>Notice 2B</th>
                                        <th>Notice 3A</th>
                                        <th>Notice 3B</th>
                                        <th>Notice 3C</th>
                                        <th>Notice 3D</th>
                                        <th>Notice 4A</th>
                                        <th>Notice 5A</th>
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
    <script type="text/javascript">
          $(function() {
            let table = $('#fileCasesNoticeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('drp.allnotices.cashmanagercasenoticelist') }}",
                    data: function(d) {
                        d.case_type = $('#filter-case-type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.status = $('#filter_status').val();
                        d.from_date = $('#filter-from-date').val();
                        d.to_date = $('#filter-to-date').val();
                    }
                },
                order: [
                    [4, 'desc']
                ],
                columns: [{
                        data: 'case_type',
                        name: 'file_cases.case_type'
                    },
                    {
                        data: 'case_number',
                        name: 'file_cases.case_number'
                    },
                    {
                        data: 'loan_number',
                        name: 'file_cases.loan_number'
                    },
                    {
                        data: 'status',
                        name: 'file_cases.status'
                    },
                    {
                        data: 'created_at',
                        name: 'file_cases.created_at'
                    },
                    {
                        data: 'notice_1',
                        name: 'notice_1',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_1a',
                        name: 'notice_1a',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_1b',
                        name: 'notice_1b',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_2b',
                        name: 'notice_2b',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_3a',
                        name: 'notice_3a',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_3b',
                        name: 'notice_3b',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_3c',
                        name: 'notice_3c',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_3d',
                        name: 'notice_3d',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_4a',
                        name: 'notice_4a',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_5a',
                        name: 'notice_5a',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Filter button click
           $('#btn-search').on('click', function () {
                table.draw();
            });

            // Reset filters
            $('#btn-clear').on('click', function () {
                $('#filter-case-type').val('');
                $('#filter_case_number').val('');
                $('#filter_loan_number').val('');
                $('#filter_status').val('');
                $('#filter-from-date').val('');
                $('#filter-to-date').val('');
                table.draw();
            });
        });
    </script>
@endsection
