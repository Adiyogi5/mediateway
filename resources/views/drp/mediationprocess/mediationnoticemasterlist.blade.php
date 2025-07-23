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
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between align-self-center gap-2">
                                <h5 class="mb-0" data-anchor="data-anchor">Mediation Notices :: Send Notice List </h5>
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)"
                                        class="btn btn-outline-secondary send-pre-mediation-notice-btn py-1 my-1">
                                        <i class="fa fa-paper-plane me-1"></i>
                                        Send Pre Mediation Notice
                                    </a>
                                </div>
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)"
                                        class="btn btn-outline-primary send-mediation-notice-btn py-1 my-1">
                                        <i class="fa fa-paper-plane me-1"></i>
                                        Send Mediation Notice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row my-2 g-2">
                        <div class="col-md-3">
                            <select class="form-control form-select py-1" id="filter_mediation_notice_type">
                                <option value="">All Notice Types</option>
                                <option value="1">Pre Mediation</option>
                                <option value="2">Mediation</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control py-1" id="filter_date_from" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control py-1" id="filter_date_to" placeholder="To Date">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary w-100 py-1" id="btn-maintable-filter">Search</button>
                            <button class="btn btn-secondary w-100 py-1" id="btn-maintable-reset">Reset</button>
                        </div>
                    </div>

                    <div class="card-body table-padding">
                        <div class="table-responsive scrollbar">
                            <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                                style="width:100%">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>Organization Name</th>
                                        <th>Notice Type</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Email Count</th>
                                        <th>Whatsapp Count</th>
                                        <th>Sms Count</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Modal for Send Pre-Mediation Notices -->
            <div class="modal fade" id="premediationProcessModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <form id="sendmediationNoticeForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Send Pre-Mediation Notices</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="px-2">
                                <div class="row my-2 gy-2 border-bottom border-1 pb-3">
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
                                    <div class="col-md-2">
                                        <select class="form-control form-select py-1" id="filter_status">
                                            <option value="">All Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="filter_date_from" class="form-control py-1"
                                            placeholder="From Date">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="filter_date_to" class="form-control py-1"
                                            placeholder="To Date">
                                    </div>
                                    <div class="col-md-3 d-flex justify-content-between gap-1">
                                        <button type="button" class="btn btn-primary w-100 py-1"
                                            id="btn-filter">Search</button>
                                        <button type="button" class="btn btn-secondary w-100 py-1"
                                            id="btn-reset">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table
                                        class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-mediation-process"
                                        style="width:100%">
                                        <thead class="bg-200 text-900">
                                            <tr>
                                                <th>Case Type</th>
                                                <th>Product Type</th>
                                                <th>Case Number</th>
                                                <th>Loan Number</th>
                                                <th>Status</th>
                                                <th>Case File Date</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="mt-2 row">
                                    <div class="col-md-6 col-12">
                                        <label>Pre-Concilation Notice Date</label>
                                        <input type="date" name="send_notice_date" id="send_notice_date"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <span class="text-danger" id="error-msg"></span>
                                <button type="submit" class="btn btn-sm px-3 btn-primary">Send Pre Mediation
                                    Notice</button>
                                <button type="button" class="btn btn-sm px-3 btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Modal for Send Mediation Notices -->
            <div class="modal fade" id="mediationNoticeModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <form id="sendMediationNoticeForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Send Mediation Notices</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="px-3">
                                <div class="row my-2 gy-2 border-bottom border-1 pb-3">
                                    <div class="col-md-3">
                                        <select class="form-control form-select py-1" id="filter2_product_type">
                                            <option value="">All Product Types</option>
                                            @foreach (config('constant.product_type') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="filter2_case_number" class="form-control py-1"
                                            placeholder="Enter Case Number">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="filter2_loan_number" class="form-control py-1"
                                            placeholder="Enter Loan Number">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control form-select py-1" id="filter2_status">
                                            <option value="">All Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="filter2_date_from" class="form-control py-1">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="filter2_date_to" class="form-control py-1">
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="button" class="btn btn-primary w-100 py-1"
                                            id="btn2-filter">Search</button>
                                        <button type="button" class="btn btn-secondary w-100 py-1"
                                            id="btn2-reset">Reset</button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table
                                        class="table table-mediation-list custom-table table-striped dt-table-hover fs--1 mb-0"
                                        style="width:100%">
                                        <thead class="bg-200 text-900">
                                            <tr>
                                                <th>Case Type</th>
                                                <th>Product Type</th>
                                                <th>Case Number</th>
                                                <th>Loan Number</th>
                                                <th>Status</th>
                                                <th>Case File Date</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="mt-2 row">
                                    <div class="col-md-6 col-12">
                                        <label>Date</label>
                                        <input type="date" name="date" id="meeting_date" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="mt-md-0 mt-2">Time</label>
                                        <input type="time" name="time" id="meeting_time" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <span class="text-danger" id="error-msg2"></span>
                                <!-- Send button -->
                                <button type="submit" class="btn btn-sm btn-success px-3"
                                    name="action" value="send">Send Mediation Notice</button>

                                <!-- Decline button -->
                                <button type="submit" class="btn btn-sm btn-danger px-3"
                                    name="action" value="decline">Decline Mediation Notice</button>
                                <button type="button" class="btn btn-sm btn-secondary px-3"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <!-- Loader for Processing Records -->
            <div id="loader-overlay" style="
                display: none;
                position: fixed;
                z-index: 1055;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.6); /* Dark semi-transparent */
                text-align: center;
                color: #fff; /* White text */
                ">
                <div class="spinner-border text-light mt-5" role="status">
                    <span class="visually-hidden">Processing...</span>
                </div>
                <p class="mt-2 fw-bold">Processing... Please wait</p>
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
    {{-- ######## Date Time Rules ######## --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const hh = String(today.getHours()).padStart(2, '0');
            const min = String(today.getMinutes()).padStart(2, '0');

            const currentDate = `${yyyy}-${mm}-${dd}`;
            const currentTime = `${hh}:${min}`;

            const dateInput = document.getElementById('meeting_date');
            const timeInput = document.getElementById('meeting_time');

            // Set min date to today
            dateInput.min = currentDate;

            // Adjust time if the date is today
            dateInput.addEventListener('change', function() {
                if (this.value === currentDate) {
                    timeInput.min = currentTime;
                } else {
                    timeInput.removeAttribute('min');
                }
            });
        });
    </script>

    {{-- ############# Main Datatable ############### --}}
    <script>
        $(document).ready(function() {
            let table = $('.table-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('drp.mediationprocess.mediationnoticemasterlist') }}",
                    data: function(d) {
                        d.mediation_notice_type = $('#filter_mediation_notice_type').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                columns: [
                    { data: 'organization_name' },
                    { data: 'mediation_notice_type', orderable: false, searchable: false },
                    { data: 'file_name' },
                    { data: 'date' },
                    { data: 'email_count', orderable: false, searchable: false },
                    { data: 'whatsapp_count', orderable: false, searchable: false },
                    { data: 'sms_count', orderable: false, searchable: false },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [
                    [3, 'desc'] // ordering by the "date" column (index 4)
                ],
            });

            // Filter button
            $('#btn-maintable-filter').click(function() {
                table.ajax.reload();
            });

            // Reset button
            $('#btn-maintable-reset').click(function() {
                $('#filter_mediation_notice_type').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                table.ajax.reload();
            });
        });
    </script>


    {{-- #################################################################### --}}
    {{-- ############## Data Table for Pre Mediation Modal ############### --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('.send-pre-mediation-notice-btn').click(function() {
                $('#premediationProcessModal').modal('show');
            });

            let table = $('.table-mediation-process').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('drp.mediationprocess.caseList') }}",
                    data: function(d) {
                        d.product_type = $('#filter_product_type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.status = $('#filter_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                order: [
                    [5, 'desc']
                ],
                columns: [{
                        data: 'case_type'
                    },
                    {
                        data: 'product_type'
                    },
                    {
                        data: 'case_number'
                    },
                    {
                        data: 'loan_number'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at'
                    }
                ]
            });

            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            $('#btn-reset').on('click', function() {
                $('#filter_product_type').val('');
                $('#filter_case_number').val('');
                $('#filter_loan_number').val('');
                $('#filter_status').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                table.ajax.reload();
            });
        });
    </script>


    {{-- ######################################################################## --}}
    {{-- #################### Send Pre Mediation Notice ###################### --}}
    <script>
        $('#sendmediationNoticeForm').on('submit', function(e) {
            e.preventDefault();

            let allCaseIds = [];
            let data = $('.table-mediation-process').DataTable().rows({ search: 'applied' }).data();

            for (let i = 0; i < data.length; i++) {
                allCaseIds.push(data[i].id); // Ensure `id` matches your backend
            }

            if (allCaseIds.length === 0) {
                $('#error-msg').text('No cases found to send notice.');
                return;
            }

            let noticeDate = $('#send_notice_date').val();
            if (!noticeDate) {
                $('#error-msg').text('Please select a notice date.');
                return;
            }

            // ✅ Show loader
            $('#loader-overlay').fadeIn();

            $.ajax({
                url: "{{ route('drp.mediationprocess.sendpremediationNotices') }}",
                method: "POST",
                contentType: "application/json",  // Important!
                data: JSON.stringify({
                    file_case_ids: allCaseIds,
                    send_notice_date: noticeDate
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#error-msg').text('');
                    toastr.success('Pre-Mediation Notices sent successfully!');
                    $('#premediationProcessModal').modal('hide');
                    $('.table-mediation-process').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    let msg = 'Failed to send notices.';
                    if (xhr.responseJSON?.error) msg = xhr.responseJSON.error;
                    $('#error-msg').text(msg);
                },
                complete: function() {
                    // ✅ Hide loader always (whether success or error)
                    $('#loader-overlay').fadeOut();
                }
            });
        });
    </script>


    {{-- ############################################################################ --}}
    {{-- ############# Send Mediation Notice and Create Meeting Room ############# --}}
    <script>
        $(document).ready(function() {
            // Open modal
            $('.send-mediation-notice-btn').click(function() {
                $('#mediationNoticeModal').modal('show');
                mediationListTable.ajax.reload();
            });

            // Datatable
            let mediationListTable = $('.table-mediation-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('drp.mediationprocess.mediationcaselist') }}",
                    data: function(d) {
                        d.case_type = ''; // if needed
                        d.product_type = $('#filter2_product_type').val();
                        d.case_number = $('#filter2_case_number').val();
                        d.loan_number = $('#filter2_loan_number').val();
                        d.status = $('#filter2_status').val();
                        d.date_from = $('#filter2_date_from').val();
                        d.date_to = $('#filter2_date_to').val();
                        d.is_mediation = true;
                    }
                },
                order: [
                    [5, 'desc']
                ],
                columns: [{
                        data: 'case_type'
                    },
                    {
                        data: 'product_type'
                    },
                    {
                        data: 'case_number'
                    },
                    {
                        data: 'loan_number'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'file_case_id',
                        visible: false,
                        searchable: false
                    }
                ]
            });

            // Filters
            $('#btn2-filter').on('click', function() {
                mediationListTable.ajax.reload();
            });
            $('#btn2-reset').on('click', function() {
                $('#filter2_product_type, #filter2_case_number, #filter2_loan_number, #filter2_status, #filter2_date_from, #filter2_date_to')
                    .val('');
                mediationListTable.ajax.reload();
            });

            // Submit form
            $('#sendMediationNoticeForm').on('submit', function(e) {
                e.preventDefault();

                let clickedButton = $(document.activeElement); // find the button that triggered submit
                let action = clickedButton.val(); // 'send' or 'decline'

                let filters = {
                    product_type: $('#filter2_product_type').val(),
                    case_number: $('#filter2_case_number').val(),
                    loan_number: $('#filter2_loan_number').val(),
                    status: $('#filter2_status').val(),
                    date_from: $('#filter2_date_from').val(),
                    date_to: $('#filter2_date_to').val()
                };

                $.ajax({
                    url: "{{ route('drp.mediationprocess.allcaseids') }}",
                    method: "GET",
                    data: filters,
                    success: function(response) {
                        let allCaseIds = response.case_ids;

                        if (allCaseIds.length === 0) {
                            $('#error-msg2').text('No eligible cases to send Mediation notice.');
                            return;
                        }
                        
                        // ✅ Show loader
                        $('#loader-overlay').fadeIn();

                        // Send final request with 'action'
                        $.ajax({
                            url: "{{ route('drp.mediationprocess.sendmediationNotices') }}",
                            method: "POST",
                            contentType: "application/json",
                            data: JSON.stringify({
                                file_case_ids: allCaseIds,
                                date: $('#meeting_date').val(),
                                time: $('#meeting_time').val(),
                                action: action // either 'send' or 'decline'
                            }),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#error-msg2').text('');
                                toastr.success(response.message);
                                $('#mediationNoticeModal').modal('hide');
                                mediationListTable.ajax.reload();
                            },
                            error: function(xhr) {
                                let errors = xhr.responseJSON.errors;
                                let message = xhr.responseJSON.message || 'Failed to send Mediation Notices.';
                                if (errors) {
                                    message = Object.values(errors).flat().join('<br>');
                                }
                                $('#error-msg2').html(message);
                            },
                            complete: function() {
                                // ✅ Hide loader always (whether success or error)
                                $('#loader-overlay').fadeOut();
                            }
                        });
                    },
                    error: function(xhr) {
                        $('#error-msg2').text('Failed to fetch all filtered case IDs.');
                    }
                });
            });
        });
    </script>


    {{-- ################# DELETE ##################  --}}
    <script>
        $(document).on('click', '.delete', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the master record and all associated mediation notices.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('drp.mediationprocess.mediationnoticemaster.delete', ['id' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                $('.table-datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete the record.',
                                    'error'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'Something went wrong while deleting.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
