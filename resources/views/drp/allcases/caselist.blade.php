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
                                <h5 class="mb-0" data-anchor="data-anchor">All Case List</h5>
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
                                        <th>Case Number</th>
                                        <th>Loan Number</th>
                                        <th>Confirmation</th>
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
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                ajax: "{{ route('drp.allcases.caselist') }}",
                order: [
                    [5, 'desc']
                ],
                columns: [
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
                        data: 'arbitrator_status',
                        name: 'arbitrator_status',
                        orderable: false,
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
                        orderable: false,
                    },
                ]
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
