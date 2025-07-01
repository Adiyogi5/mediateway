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
                                <h5 class="mb-0" data-anchor="data-anchor">Conciliation Notices :: Send Notice List </h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)"
                                        class="btn btn-outline-secondary send-pre-conciliation-notice-btn py-1 my-1">
                                        <i class="fa fa-paper-plane me-1"></i>
                                        Send Pre Conciliation Notice
                                    </a>
                                </div>
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)"
                                        class="btn btn-outline-primary send-conciliation-notice-btn py-1 my-1">
                                        <i class="fa fa-paper-plane me-1"></i>
                                        Send Conciliation Notice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row my-2 g-2">
                        <div class="col-md-3">
                            <select class="form-control form-select py-1" id="filter_product_type">
                                <option value="">All Product Types</option>
                                @foreach (config('constant.product_type') as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control py-1" id="filter_case_number"
                                placeholder="Case Number">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control py-1" id="filter_loan_number"
                                placeholder="Loan Number">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control form-select py-1" id="filter_status">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control py-1" id="filter_date_from" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control py-1" id="filter_date_to" placeholder="To Date">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary w-100 py-1" id="btn-filter">Search</button>
                            <button class="btn btn-secondary w-100 py-1" id="btn-reset">Reset</button>
                        </div>
                    </div>

                    <div class="card-body table-padding">
                        <div class="table-responsive scrollbar">
                            <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                                style="width:100%">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>Case Number</th>
                                        <th>Loan Number</th>
                                        <th>Case Type</th>
                                        <th>Product Type</th>
                                        <th>Notice Type</th>
                                        <th>Notice</th>
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

            <!-- Modal for Send Notices -->
            <div class="modal fade" id="preconciliationProcessModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <form id="sendconciliationNoticeForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Send Pre-Conciliation Notices</h5>
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
                                        class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-conciliation-process"
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
                            </div>

                            <div class="modal-footer">
                                <span class="text-danger" id="error-msg"></span>
                                <button type="submit" class="btn btn-sm px-3 btn-primary">Send Pre Conciliation
                                    Notice</button>
                                <button type="button" class="btn btn-sm px-3 btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Modal for Sending Conciliation Notices -->
            <div class="modal fade" id="conciliationNoticeModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <form id="sendConciliationNoticeForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Send Conciliation Notices</h5>
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
                                        class="table table-conciliation-list custom-table table-striped dt-table-hover fs--1 mb-0"
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
                                <button type="submit" class="btn btn-sm btn-primary px-3">Send Conciliation
                                    Notice</button>
                                <button type="button" class="btn btn-sm btn-secondary px-3"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Modal for View Detail -->
            <div class="modal fade" id="viewNoticeModal" tabindex="-1" aria-labelledby="viewNoticeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Conciliation Notice Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="noticeDetailContent">
                            <!-- Content will be loaded here via JS -->
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
                    url: "{{ route('drp.conciliationprocess.conciliationnoticelist') }}",
                    data: function(d) {
                        d.case_type = $('#filter_case_type').val();
                        d.product_type = $('#filter_product_type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.status = $('#filter_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                order: [
                    [7, 'desc']
                ], // order by created_at
                columns: [{
                        data: 'case_number'
                    },
                    {
                        data: 'loan_number'
                    },
                    {
                        data: 'case_type'
                    },
                    {
                        data: 'product_type'
                    },
                    {
                        data: 'conciliation_notice_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'notice_copy',
                        orderable: false,
                        searchable: false
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
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Filter button
            $('#btn-filter').click(function() {
                table.ajax.reload();
            });

            // Reset button
            $('#btn-reset').click(function() {
                $('#filter_case_type').val('');
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


    {{-- #################################################################### --}}
    {{-- ############## Data Table for Pre Conciliation Modal ############### --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('.send-pre-conciliation-notice-btn').click(function() {
                $('#preconciliationProcessModal').modal('show');
            });

            let table = $('.table-conciliation-process').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('drp.conciliationprocess.caseList') }}",
                    data: function(d) {
                        d.case_type = $('#filter_case_type').val();
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
                $('#filter_case_type').val('');
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
    {{-- #################### Send Pre Conciliation Notice ###################### --}}
    <script>
        $('#sendconciliationNoticeForm').on('submit', function(e) {
            e.preventDefault();

            // Fetch all visible rows from DataTable
            let allCaseIds = [];
            let data = $('.table-conciliation-process').DataTable().rows({
                search: 'applied'
            }).data();

            for (let i = 0; i < data.length; i++) {
                allCaseIds.push(data[i].id); // use 'id' if 'case_id' is not mapped
            }

            if (allCaseIds.length === 0) {
                $('#error-msg').text('No cases found to send notice.');
                return;
            }

            $.ajax({
                url: "{{ route('drp.conciliationprocess.sendpreconciliationNotices') }}",
                method: "POST",
                data: {
                    _token: $('input[name="_token"]').val(),
                    file_case_ids: allCaseIds
                },
                success: function(response) {
                    $('#error-msg').text('');
                    toastr.success('Pre-Conciliation Notices sent successfully!');
                    $('#preconciliationProcessModal').modal('hide');
                    $('.table-conciliation-process').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    $('#error-msg').text('Failed to send notices.');
                }
            });
        });
    </script>


    {{-- ############################################################################ --}}
    {{-- ############# Send Conciliation Notice and Create Meeting Room ############# --}}
    <script>
        $(document).ready(function() {
            // Open modal
            $('.send-conciliation-notice-btn').click(function() {
                $('#conciliationNoticeModal').modal('show');
                conciliationListTable.ajax.reload();
            });

            // Datatable
            let conciliationListTable = $('.table-conciliation-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('drp.conciliationprocess.conciliationcaselist') }}",
                    data: function(d) {
                        d.case_type = ''; // if needed
                        d.product_type = $('#filter2_product_type').val();
                        d.case_number = $('#filter2_case_number').val();
                        d.loan_number = $('#filter2_loan_number').val();
                        d.status = $('#filter2_status').val();
                        d.date_from = $('#filter2_date_from').val();
                        d.date_to = $('#filter2_date_to').val();
                        d.is_conciliation = true;
                    }
                },
                order: [
                    [5, 'desc']
                ],
                columns: [
                    { data: 'case_type' },
                    { data: 'product_type' },
                    { data: 'case_number' },
                    { data: 'loan_number' },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'created_at' },
                    {
                        data: 'file_case_id',  // ✅ Include this field
                        visible: false,        // 👈 Keep it hidden in the UI
                        searchable: false
                    }
                ]
            });

            // Filters
            $('#btn2-filter').on('click', function() {
                conciliationListTable.ajax.reload();
            });
            $('#btn2-reset').on('click', function() {
                $('#filter2_product_type, #filter2_case_number, #filter2_loan_number, #filter2_status, #filter2_date_from, #filter2_date_to')
                    .val('');
                conciliationListTable.ajax.reload();
            });

            // Submit form
            $('#sendConciliationNoticeForm').on('submit', function(e) {
                e.preventDefault();

                let data = conciliationListTable.rows({
                    search: 'applied'
                }).data();
                let allCaseIds = [];

                for (let i = 0; i < data.length; i++) {
                    allCaseIds.push(data[i].file_case_id);
                }

                if (allCaseIds.length === 0) {
                    $('#error-msg2').text('No eligible cases to send Conciliation notice.');
                    return;
                }

                // For Creating Conciliation Meeting Room 
                $.ajax({
                    url: "{{ route('drp.conciliationprocess.sendconciliationNotices') }}",
                    method: "POST",
                    data: {
                        _token: $('input[name="_token"]').val(),
                        file_case_ids: allCaseIds,
                        date: $('#meeting_date').val(),
                        time: $('#meeting_time').val()
                    },
                    success: function(response) {
                        $('#error-msg2').text('');
                        toastr.success('Conciliation Meeting Rooms Created Successfully!');
                        $('#conciliationNoticeModal').modal('hide');
                        conciliationListTable.ajax.reload();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let message = xhr.responseJSON.message || 'Failed to send Conciliation Notices.';
                        if (errors) {
                            message = Object.values(errors).flat().join('<br>');
                        }
                        $('#error-msg2').html(message);
                    }
                });
            });
        });
    </script>


    {{-- ########################################### --}}
    {{-- ############## View Details ############### --}}
    <script>
        $(document).on('click', '.view-details-btn', function() {
            const id = $(this).data('id');
            const pdfIconUrl = "{{ asset('public/assets/img/pdf.png') }}";
            $.ajax({
                url: `{{ url('drp/conciliation-notice') }}/${id}`,
                method: 'GET',
                beforeSend: function() {
                    $('#noticeDetailContent').html('<p class="text-center">Loading...</p>');
                },
                success: function(data) {
                    const html = `
                        <small><strong>Case Number  : </strong> ${data.case_number}</small></br>
                        <small><strong>Loan Number  : </strong> ${data.loan_number}</small></br></br>

                        <small><strong>Customer Name  : </strong> ${data.respondent_first_name}</small></br>
                        <small><strong>Customer Email  : </strong> ${data.respondent_email}</small></br>
                        <small><strong>Customer Mobile  : </strong> ${data.respondent_mobile}</small></br></br>

                        <small><strong>Case Type  : </strong> ${data.case_type}</small></br>
                        <small><strong>Product Type  : </strong> ${data.product_type}</small></br></br>

                        <small><strong>Notice Type  : </strong> ${data.conciliation_notice_type}</small></br>
                        <small><strong>Notice Date  : </strong> ${data.notice_date}</small></br>
                        <small><strong>Notice Copy  : </strong> <a href="${data.notice_copy}" target="_blank"><img src="${pdfIconUrl}" height="30" alt="PDF File" /></a></small></br></br>
                        
                        <small><strong>Email Status  : </strong> ${data.email_status}</small></br>
                        <small><strong>Notice Send Date  : </strong> ${data.notice_send_date}</small></br></br>

                        <small><strong>Whatsapp Notice Status  : </strong> ${data.whatsapp_notice_status}</small></br>
                        <small><strong>Whatsapp Send Date  : </strong> ${data.whatsapp_dispatch_datetime}</small></br></br>

                        <small><strong>SMS Status  : </strong> ${data.sms_status}</small></br>
                        <small><strong>SMS Send Date  : </strong> ${data.sms_send_date}</small>
                    `;
                    $('#noticeDetailContent').html(html);
                    $('#viewNoticeModal').modal('show');
                },
                error: function() {
                    $('#noticeDetailContent').html('<p class="text-danger">Failed to load data.</p>');
                }
            });
        });
    </script>
@endsection
