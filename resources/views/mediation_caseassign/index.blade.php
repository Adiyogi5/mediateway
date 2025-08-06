@extends('layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">File Cases :: Mediation File Cases List </h5>
                </div>
                <div class="col-auto align-self-center ms-auto">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#assignCaseModal">
                        <i class="fa fa-user-check me-1"></i> Bulk Cases Assign
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row gy-2 border-bottom border-1 pb-3">
                <div class="col-md-2">
                    <select id="filter_user_type" class="form-select py-1">
                        <option value="">All User Type</option>
                        <option value="1">Individual</option>
                        <option value="2">Organization</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select py-1" id="filter_status">
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

    <div class="modal fade" id="assignCaseModal" tabindex="-1" aria-labelledby="assignCaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form method="POST" action="{{ route('mediation_caseassign.bulkassign') }}" class="modal-content">
                @csrf
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white">Assign All Unassigned Mediation Cases</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3 px-3">
                    <div class="col-md-6">
                        <label class="form-label">Case Manager</label>
                        <select name="case_manager_id" class="form-select" required>
                            <option value="">Select Case Manager</option>
                            @foreach ($casemanagers as $cm)
                                <option value="{{ $cm->id }}">{{ $cm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mediator</label>
                        <select name="mediator_id" class="form-select" required>
                            <option value="">Select Mediator</option>
                            @foreach ($mediators as $mediator)
                                <option value="{{ $mediator->id }}">{{ $mediator->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-bordered" id="unassignedCasesTable" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Case No</th>
                                    <th>Loan No</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Assign All</button>
                </div>
            </form>
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
                    url: "{{ route('mediation_caseassign') }}",
                    data: function(d) {
                        d.user_type = $('#filter_user_type').val();
                        d.status = $('#filter_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
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
                $('#filter_status').val('');
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
                            url: "{{ route('mediation_caseassign.delete') }}",
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
    <script>
        let unassignedTable;

        $('#assignCaseModal').on('shown.bs.modal', function() {
            if (!$.fn.DataTable.isDataTable('#unassignedCasesTable')) {
                unassignedTable = $('#unassignedCasesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('mediation_caseassign.unassigneddata') }}",
                    columns: [{
                            data: 'case_number',
                            name: 'case_number'
                        },
                        {
                            data: 'loan_number',
                            name: 'loan_number'
                        },
                        {
                            data: 'user_type',
                            name: 'user_type'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    responsive: true
                });
            } else {
                unassignedTable.ajax.reload(); // Reload fresh data every time
            }
        });
    </script>
@endsection
