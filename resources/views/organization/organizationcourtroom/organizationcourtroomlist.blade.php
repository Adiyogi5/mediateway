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
                                <h5 class="mb-0" data-anchor="data-anchor">Court Room Lists</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <ul class="nav nav-tabs justify-content-center text-center" role="tablist">
                            <li class="nav-item w-50">
                                <a href="#info" role="tab" data-bs-toggle="tab" class="nav-link active"> Upcoming ({{$upcomingroomCount}})</a>
                            </li>
                            <li class="nav-item w-50">
                                <a href="#ratings" role="tab" data-bs-toggle="tab" class="nav-link"> Closed ({{$closedroomCount}})</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" role="tabpanel" id="info">
                                <div class="table-responsive">
                                    <table id="upcomingTable" class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Case Number</th>
                                                <th scope="col">Hearing Type</th>
                                                <th scope="col">Arbitrator Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        {{-- <tbody>
                                            @forelse ($upcomingRooms as $room)
                                                <tr class="bg-blue">
                                                    <td class="pt-2">
                                                        <div class="pl-lg-5 pl-md-3 pl-1 name">
                                                            {{ Str::before($room->case_numbers, ',') ?? '-' }}
                
                                                            @if(Str::contains($room->case_numbers, ','))
                                                                @php
                                                                    $caseList = '<ul>';
                                                                    foreach (explode(',', $room->case_numbers) as $case) {
                                                                        $caseList .= "<li>$case</li>";
                                                                    }
                                                                    $caseList .= '</ul>';
                                                                @endphp
                                            
                                                                <i class="fa fa-info-circle text-primary ml-2 info-icon"
                                                                   id="caseInfo_{{ $room->id }}"
                                                                   data-bs-toggle="popover"
                                                                   data-bs-html="true"
                                                                   data-bs-content='{!! $caseList !!}'>
                                                                </i>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="pt-3">
                                                        @if ($room->hearing_type == 1)
                                                            First Hearing
                                                        @elseif ($room->hearing_type == 2)
                                                            Second Hearing
                                                        @elseif ($room->hearing_type == 3)
                                                            Final Hearing
                                                        @else
                                                            Unknown
                                                        @endif
                                                    </td>
                                                    <td class="pt-2">
                                                        <div class="pl-lg-5 pl-md-3 pl-1 name">{{ $room->arbitrator_name }}</div>
                                                    </td>
                                                    <td class="pt-3">{{ \Carbon\Carbon::parse($room->date)->format('d F Y') }}</td>
                                                    <td class="pt-3">{{ \Carbon\Carbon::parse($room->time)->format('h:i A') }}</td>
                                                    <td class="pt-3">
                                                        <span class="fa {{ $room->status == 1 ? 'fa-check' : 'fa-clock' }} pl-3"></span>
                                                    </td>
                                                    <td class="pt-3">
                                                        @if($room->status == 1)
                                                            <a href="{{ route('organization.organizationcourtroom.liveorganizationcourtroom', $room->room_id) }}?case_id={{ $room->court_room_case_id }}"
                                                               class="fa fa-video btn bg-success text-white text-capitalize fs-6">
                                                            </a>
                                                        @else
                                                            <span class="fa fa-video btn bg-secondary text-white text-capitalize fs-6" style="cursor: not-allowed;"></span>
                                                        @endif
                                                    </td>                                                    
                                                </tr>
                                                <tr id="spacing-row"><td colspan="6"></td></tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center">No upcoming cases found.</td></tr>
                                            @endforelse
                                        </tbody>                                             --}}
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" role="tabpanel" id="ratings">
                                <div class="table-responsive">
                                    <table id="closedTable" class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Case Number</th>
                                                <th scope="col">Hearing Type</th>
                                                <th scope="col">Arbitrator Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        {{-- <tbody>
                                            @forelse ($closedRooms as $room)
                                                <tr class="bg-blue">
                                                    <td class="pt-2">
                                                        <div class="pl-lg-5 pl-md-3 pl-1 name">
                                                            {{ Str::before($room->case_numbers, ',') ?? '-' }}
                
                                                            @if(Str::contains($room->case_numbers, ','))
                                                                @php
                                                                    $caseList = '<ul>';
                                                                    foreach (explode(',', $room->case_numbers) as $case) {
                                                                        $caseList .= "<li>$case</li>";
                                                                    }
                                                                    $caseList .= '</ul>';
                                                                @endphp
                                            
                                                                <i class="fa fa-info-circle text-primary ml-2 info-icon"
                                                                   id="caseInfo_{{ $room->id }}"
                                                                   data-bs-toggle="popover"
                                                                   data-bs-html="true"
                                                                   data-bs-content='{!! $caseList !!}'>
                                                                </i>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="pt-3">
                                                        @if ($room->hearing_type == 1)
                                                            First Hearing
                                                        @elseif ($room->hearing_type == 2)
                                                            Second Hearing
                                                        @elseif ($room->hearing_type == 3)
                                                            Final Hearing
                                                        @else
                                                            Unknown
                                                        @endif
                                                    </td>
                                                    <td class="pt-2">
                                                        <div class="pl-lg-5 pl-md-3 pl-1 name">{{ $room->arbitrator_name }}</div>
                                                    </td>
                                                    <td class="pt-3">{{ \Carbon\Carbon::parse($room->date)->format('d F Y') }}</td>
                                                    <td class="pt-3">{{ \Carbon\Carbon::parse($room->time)->format('h:i A') }}</td>
                                                    <td class="pt-3">
                                                        <span class="fa fa-close pl-3"></span>
                                                    </td>
                                                    <td class="pt-3">
                                                        <button class="fa fa-handshake btn bg-secondary text-white"></button>
                                                    </td>
                                                </tr>
                                                <tr id="spacing-row"><td colspan="6"></td></tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center">No closed cases found.</td></tr>
                                            @endforelse
                                        </tbody> --}}
                                    </table>
                                </div>
                            </div>
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
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    
    @if (session('showProfilePopup') || isset($showProfilePopup))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Profile Incomplete!",
                    text: "Please complete your profile before proceeding.",
                    icon: "warning",
                    confirmButtonText: "Update Now",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showCancelButton: false,
                    showCloseButton: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('organization.profile') }}";
                    }
                });
            });
        </script>
    @endif
    <script>
        $(document).ready(function () {
            // Initialize all popovers
            $('[data-bs-toggle="popover"]').popover({
                html: true,
                trigger: 'click',
                placement: 'right'
            });
    
            // Auto-close other popovers when one is clicked
            $(document).on('click', function (e) {
                $('[data-bs-toggle="popover"]').each(function () {
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });
    
            // Debugging: Check if the icon is clickable
            $('.info-icon').on('click', function () {
                console.log('Popover clicked:', $(this).attr('id'));
            });
        });
    </script>     
    <script>
        $(document).ready(function () {
            const initPopover = () => {
                $('[data-bs-toggle="popover"]').popover({
                    trigger: 'hover',
                    placement: 'top',
                });
            };

            const upcomingTable = $('#upcomingTable').DataTable({
                ajax: '{{ route("organization.organizationcourtroom.datatable.upcoming") }}',
                columns: [
                    { data: 'case_numbers' },
                    { data: 'hearing_type' },
                    { data: 'arbitrator_name' },
                    { data: 'date' },
                    { data: 'time' },
                    { data: 'status' },
                    { data: 'action' }
                ],
                drawCallback: initPopover
            });

            const closedTable = $('#closedTable').DataTable({
                ajax: '{{ route("organization.organizationcourtroom.datatable.closed") }}',
                columns: [
                    { data: 'case_numbers' },
                    { data: 'hearing_type' },
                    { data: 'arbitrator_name' },
                    { data: 'date' },
                    { data: 'time' },
                    { data: 'status' },
                    { data: 'action' }
                ],
                drawCallback: initPopover
            });

            // Optional: reload on tab switch
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                upcomingTable.ajax.reload(null, false);
                closedTable.ajax.reload(null, false);
            });
        });
    </script>
@endsection
