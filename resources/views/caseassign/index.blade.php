@extends('layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">File Cases :: File Cases List </h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row gy-2 border-bottom border-1 pb-3">
                <div class="col-md-3">
                    <select id="filter_user_type" class="form-select py-1">
                        <option value="">All User Type</option>
                        <option value="1">Individual</option>
                        <option value="2">Organization</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filter_case_type" class="form-select py-1">
                        <option value="">All Case Type</option>
                        @foreach (config('constant.case_type') as $key => $case)
                            <option value="{{ $key }}">{{ $case }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select py-1" id="filter_status">
                        <option value="">Case Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_send_status" class="form-select py-1">
                        <option value="">Send Status</option>
                        <option value="0">Not Sent</option>
                        <option value="1">Sent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_receive_status" class="form-select py-1">
                        <option value="">Receive Status</option>
                        <option value="0">Not Received</option>
                        <option value="1">Received</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_arbitrator_status" class="form-select py-1">
                        <option value="">Arbitrator Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Confirmed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_assigned_status" class="form-select py-1">
                        <option value="">Assigned Status</option>
                        <option value="0">Not Assigned</option>
                        <option value="1">Assigned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="filter_date_from" class="form-control py-1" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" id="filter_date_to" class="form-control py-1" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button id="filterBtn" class="btn btn-primary me-2 py-1 w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <button id="clearFilterBtn" class="btn btn-secondary py-1 w-100">Clear</button>
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
                            <th>Send To </br>CaseManager</th>
                            <th>Receive To </br>CaseManager</th>
                            <th>Confirm To </br>Arbitrator</th>
                            <th>Assigned </br>Status</th>
                            <th>Created Date</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('caseassign') }}",
                    data: function(d) {
                        d.user_type = $('#filter_user_type').val();
                        d.case_type = $('#filter_case_type').val();
                        d.status = $('#filter_status').val();
                        d.send_status = $('#filter_send_status').val();
                        d.receive_status = $('#filter_receive_status').val();
                        d.arbitrator_status = $('#filter_arbitrator_status').val();
                        d.assigned_status = $('#filter_assigned_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                order: [
                    [10, 'desc']
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
                    {
                        data: 'arbitrator_status',
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
                $('#filter_status').val('');
                $('#filter_send_status').val('');
                $('#filter_receive_status').val('');
                $('#filter_arbitrator_status').val('');
                $('#filter_assigned_status').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
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
                            url: "{{ route('caseassign.delete') }}",
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
