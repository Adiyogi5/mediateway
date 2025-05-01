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
                <div class="card mb-3 card-inner">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">File Cases :: File Cases List </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row border-bottom border-1 pb-3">
                            <div class="col-md-3">
                                <label for="filter_user_type">User Type</label>
                                <select id="filter_user_type" class="form-select py-1">
                                    <option value="">All</option>
                                    <option value="1">Individual</option>
                                    <option value="2">Organization</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filter_case_type">Case Type</label>
                                <select id="filter_case_type" class="form-select py-1">
                                    <option value="">All</option>
                                    @foreach (config('constant.case_type') as $key => $case)
                                        <option value="{{ $key }}">{{ $case }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filter_created_at">Created Date</label>
                                <input type="date" id="filter_created_at" class="form-control py-1">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary me-2 py-1">Filter</button>
                                <button id="clearFilterBtn" class="btn btn-secondary py-1">Clear</button>
                            </div>
                        </div>
                        <div class="table-responsive scrollbar">
                            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>User Type</th>
                                        <th>Case Type</th>
                                        <th>Case No.</th>
                                        <th>Loan No.</th>
                                        <th>Status</th>
                                        <th>Receive To </br>SuperAdmin</th>
                                        <th>Send To </br>SuperAdmin</th>
                                        <th>Confirm To </br>Arbitrator</th>
                                        <th>Assigned Status</th>
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
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('drp.caseassign') }}",
                    data: function(d) {
                        d.user_type = $('#filter_user_type').val();
                        d.case_type = $('#filter_case_type').val();
                        d.created_at = $('#filter_created_at').val();
                    }
                },
                order: [
                    [7, 'desc']
                ],
                columns: [{
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_type',
                        name: 'user_type'
                    },
                    {
                        data: 'case_type',
                        name: 'case_type'
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
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'send_status',
                        name: 'send_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'receive_status',
                        name: 'receive_status',
                        orderable: false,
                        searchable: false
                    },
                    {   data: 'arbitrator_status', 
                        name: 'arbitrator_status', 
                        orderable: false, 
                        searchable: false 
                    },
                    {
                        data: 'assigned_status',
                        name: 'assigned_status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                }]
            });

            // Apply filters
            $('#filterBtn').click(function() {
                table.ajax.reload();
            });

            // Reset filters
            $('#clearFilterBtn').click(function() {
                $('#filter_user_type').val('');
                $('#filter_case_type').val('');
                $('#filter_created_at').val('');
                table.ajax.reload();
            });

            // Delete Case
            $(document).on('click', ".delete", function() {
                var id = $(this).data('id');
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "{{ route('drp.caseassign.delete') }}",
                            data: {
                                'id': id
                            },
                            type: 'DELETE',
                            success: function(data) {
                                if (data.status) {
                                    swal(data?.message, {
                                        icon: "success"
                                    });
                                    table.ajax.reload();
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
