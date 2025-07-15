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
                            <div class="col-12 d-flex justify-content-between align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Conciliation Notices :: Notice List </h5>
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)" id="btn-export" class="btn btn-success py-1 my-1">
                                        <i class="fa fa-file-excel"></i> Export to Excel
                                    </a>
                                </div>
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="{{ route('drp.conciliationprocess.conciliationnoticemasterlist') }}"
                                        class="btn btn-warning py-1 my-1">
                                        <i class="fa fa-list me-1"></i>
                                        Notice List
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
                            <select class="form-control form-select py-1" id="filter_conciliation_notice_type">
                                <option value="">All Notice Types</option>
                                <option value="1">Pre Conciliation</option>
                                <option value="2">Conciliation</option>
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
                                        <th>Organization Name</th>
                                        <th>Case Type</th>
                                        <th>Product Type</th>
                                        <th>Notice Type</th>
                                        <th>Notice</th>
                                        <th>Status</th>
                                        <th>Case File Date</th>
                                        <th>Notice Date</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
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
                    url: "{{ route('drp.conciliationprocess.conciliationnoticelist', $master_id) }}",
                    data: function(d) {
                        d.case_type = $('#filter_case_type').val();
                        d.product_type = $('#filter_product_type').val();
                        d.conciliation_notice_type = $('#filter_conciliation_notice_type').val();
                        d.case_number = $('#filter_case_number').val();
                        d.loan_number = $('#filter_loan_number').val();
                        d.status = $('#filter_status').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                    }
                },
                order: [
                    [9, 'desc']
                ], // order by created_at
                columns: [{
                        data: 'case_number'
                    },
                    {
                        data: 'loan_number'
                    },
                    { data: 'claimant_first_name' },
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
                    { data: 'notice_date' },
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
                $('#filter_conciliation_notice_type').val('');
                $('#filter_case_number').val('');
                $('#filter_loan_number').val('');
                $('#filter_status').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                table.ajax.reload();
            });
        });
    </script>


    {{-- ############# Export Filters ############### --}}
    <script>
        $('#btn-export').on('click', function() {
            let params = {
                master_id: "{{ $master_id }}",
                case_type: $('#filter_case_type').val(),
                product_type: $('#filter_product_type').val(),
                conciliation_notice_type: $('#filter_conciliation_notice_type').val(),
                case_number: $('#filter_case_number').val(),
                loan_number: $('#filter_loan_number').val(),
                status: $('#filter_status').val(),
                date_from: $('#filter_date_from').val(),
                date_to: $('#filter_date_to').val()
            };

            let query = $.param(params);
            window.location.href = "{{ route('drp.conciliation.export') }}?" + query;
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
