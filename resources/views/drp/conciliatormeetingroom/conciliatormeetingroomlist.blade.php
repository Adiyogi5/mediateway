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
                <div class="card mb-3 card-inner form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Conciliator Meeting Lists</h5>
                            </div>
                            {{-- <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0)" class="btn btn-outline-secondary create-meeting-room-btn">
                                        <i class="fa fa-plus me-1"></i> Create Meeting Room
                                    </a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <ul class="nav nav-tabs justify-content-center text-center" role="tablist">
                            <li class="nav-item w-50">
                                <a href="#info" role="tab" data-bs-toggle="tab" class="nav-link active"> Upcoming
                                    ({{ $upcomingroomCount }})</a>
                            </li>
                            <li class="nav-item w-50">
                                <a href="#ratings" role="tab" data-bs-toggle="tab" class="nav-link"> Closed
                                    ({{ $closedroomCount }})</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" role="tabpanel" id="info">
                                <div class="table-responsive">
                                    <table id="upcomingTable" class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        {{-- Ajax datatable  --}}
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" role="tabpanel" id="ratings">
                                <div class="table-responsive">
                                    <table id="closedTable" class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Recording</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        {{-- Ajax datatable  --}}
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="meetingRoomModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form id="createMeetingRoomForm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Create Meeting Room</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table id="caseTable" class="table table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll" /></th>
                                                <th>Case Number</th>
                                                <th>Conciliator</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="mt-2 row">
                                    <div class="col-md-6 col-12">
                                        <label>Date</label>
                                        <input type="date" name="date" id="meeting_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="mt-md-0 mt-2">Time</label>
                                        <input type="time" name="time" id="meeting_time" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <span class="text-danger" id="error-msg"></span>
                                <button type="submit" class="btn btn-sm px-3 btn-primary">Create Meeting Room</button>
                                <button type="button" class="btn btn-sm px-3 btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
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
    <script>
        $(document).ready(function() {
            // Initialize all popovers
            $('[data-bs-toggle="popover"]').popover({
                html: true,
                trigger: 'click',
                placement: 'right'
            });

            // Auto-close other popovers when one is clicked
            $(document).on('click', function(e) {
                $('[data-bs-toggle="popover"]').each(function() {
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover')
                        .has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });

            // Debugging: Check if the icon is clickable
            $('.info-icon').on('click', function() {
                console.log('Popover clicked:', $(this).attr('id'));
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            const initPopover = () => {
                $('[data-bs-toggle="popover"]').popover({
                    trigger: 'hover',
                    placement: 'top',
                });
            };

            const upcomingTable = $('#upcomingTable').DataTable({
                ajax: '{{ route('drp.conciliatormeetingroom.datatable.upcoming') }}',
                columns: [{
                        data: 'case_numbers'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'time'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action'
                    }
                ],
                drawCallback: initPopover
            });

            const closedTable = $('#closedTable').DataTable({
                ajax: '{{ route('drp.conciliatormeetingroom.datatable.closed') }}',
                columns: [{
                        data: 'case_numbers'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'time'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'recording'
                    },
                    {
                        data: 'action'
                    }
                ],
                drawCallback: initPopover
            });

            // Optional: reload on tab switch
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                upcomingTable.ajax.reload(null, false);
                closedTable.ajax.reload(null, false);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
            dateInput.addEventListener('change', function () {
                if (this.value === currentDate) {
                    timeInput.min = currentTime;
                } else {
                    timeInput.removeAttribute('min');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Open modal
            $('.create-meeting-room-btn').click(function() {
                $('#meetingRoomModal').modal('show');
            });

            // Initialize DataTable
            let table = $('#caseTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route('drp.conciliatormeetingroom.caseList') }}',
                columns: [{
                        data: 'id',
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="case-checkbox" name="case_ids[]" value="${data}">`;
                        },
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'case_number'
                    },
                    {
                        data: 'conciliator_name'
                    }
                ]
            });

            // Select All
            $('#selectAll').on('change', function() {
                $('.case-checkbox').prop('checked', this.checked);
            });

            // Form submission
            $('#createMeetingRoomForm').submit(function(e) {
                e.preventDefault();

                let selected = $('.case-checkbox:checked').length;
                if (selected == 0) {
                    toastr.error("Please select at least one case.");
                    return;
                }

                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('drp.conciliatormeetingroom.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function (res) {
                        $('#meetingRoomModal').modal('hide');
                        $('#createMeetingRoomForm')[0].reset();
                        $('#caseTable').DataTable().ajax.reload();

                        toastr.success("Meeting Room Created Successfully!");
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error("Something went wrong. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
