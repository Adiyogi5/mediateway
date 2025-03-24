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
                            <th>Claimant Name</th>
                            <th>Respondent Name</th>
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
                        data: 'claimant_first_name',
                        name: 'claimant_first_name'
                    },
                    {
                        data: 'respondent_first_name',
                        name: 'respondent_first_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'assigned_status', // New Column
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
                    },
                ],
                columnDefs: [{
                    targets: 0, // First column (Serial Number)
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Generate serial numbers dynamically
                    }
                }]
            });

            // Apply filters
            $('#filter_button').click(function() {
                table.draw();
            });

            // Reset filters
            $('#reset_button').click(function() {
                $('#filter_user_type').val('');
                $('#filter_case_type').val('');
                $('#filter_created_at').val('');
                table.draw();
            });

            // Delete Case
            $(document).on('click', ".delete", function() {
                var id = $(this).data('id');
                swal(deleteSweetAlertConfig).then((willDelete) => {
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
