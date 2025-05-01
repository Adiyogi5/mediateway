@extends('layouts.app')

@section('content')

                <div class="card mb-3 form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">All Case Notice List</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-padding">
                        <div class="table-responsive scrollbar">
                            <table id="cases-table" class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
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
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
             // Set up global AJAX CSRF token
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
            });

            var table = $('#cases-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('allcasenotices.casenoticelist') }}",
                order: [[4, 'desc']],
                columns: [
                    { data: 'case_type', name: 'file_cases.case_type' },
                    { data: 'case_number', name: 'file_cases.case_number' },
                    { data: 'loan_number', name: 'file_cases.loan_number' },
                    { data: 'status', name: 'file_cases.status' },
                    { data: 'created_at', name: 'file_cases.created_at' },
                    { data: 'notice_1', name: 'notice_1', orderable: false, searchable: false },
                    { data: 'notice_1a', name: 'notice_1a', orderable: false, searchable: false },
                    { data: 'notice_1b', name: 'notice_1b', orderable: false, searchable: false },
                    { data: 'notice_2b', name: 'notice_2b', orderable: false, searchable: false },
                    { data: 'notice_3a', name: 'notice_3a', orderable: false, searchable: false },
                    { data: 'notice_3b', name: 'notice_3b', orderable: false, searchable: false },
                    { data: 'notice_3c', name: 'notice_3c', orderable: false, searchable: false },
                    { data: 'notice_3d', name: 'notice_3d', orderable: false, searchable: false },
                    { data: 'notice_4a', name: 'notice_4a', orderable: false, searchable: false },
                    { data: 'notice_5a', name: 'notice_5a', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endsection
