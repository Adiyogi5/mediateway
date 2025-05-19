@extends('layouts.front')

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <section>
        <div class="container my-xl-5 my-lg-4 my-3">
            <div class="row">
                <div class="col-md-3 col-12">

                    @include('front.includes.sidebar_inner')

                </div>

                <div class="col-md-9 col-12">
                    <div class="card-inner card-dashboard">
                        {{-- <div class="row justify-content-md-between justify-content-center">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0">Dashboard</h5>
                            </div>
                            <div class="col-auto ms-md-auto mx-md-0 mx-auto d-flex mt-md-0 mt-2">
                                <a href="#" class="btn btn-dasboard">All Individuals</a>
                                <div class="custom-dropdown">
                                    <select name="Organization" class="form-control form-select form-dashboard-select">
                                        <option value="">All Organizations</option>
                                        @foreach($allData as $key => $organization)
                                            <option value="{{$key}}">{{$organization->organization_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="custom-dropdown">
                                    <select name="Organization" class="form-control form-select form-dashboard-select">
                                        <option value="">Case Type</option>
                                         @foreach($allData as $key => $casetype)
                                            <option value="{{$key}}">{{$casetype->case_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row justify-content-center">
                            <!-- Tabs Section -->
                            <div class="d-flex flex-wrap justify-content-center">
                                @if (auth('drp')->check() && in_array(auth('drp')->user()->drp_type, [1, 2, 3, 4]))
                                    <div class="custom-tab active" data-target="case-overview">CASE OVERVIEW</div>
                                @endif
                                @if (auth('drp')->check() && auth('drp')->user()->drp_type == 5)
                                    <div class="custom-tab" data-target="upcoming-sessions">UPCOMING CONCILIATION MEETINGS
                                    </div>
                                @endif
                                @if (auth('drp')->check() && in_array(auth('drp')->user()->drp_type, [1, 2, 3, 4]))
                                    <div class="custom-tab" data-target="communication-log">COMMUNICATION</div>
                                @endif
                            </div>

                            <!-- Content Boxes -->
                            <div id="case-overview" class="content-section row gy-lg-4 gy-3">
                                {{-- ################ DRP TYPE 1 Arbitrator ################# --}}
                                {{-- ####################################################### --}}
                                @if (auth('drp')->check() && in_array(auth('drp')->user()->drp_type, [1, 2, 3, 4]))
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/active-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $totalFiledCases }}</h3>
                                                    <p>Total Filed Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/Resolved-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $totalPendingCases }}</h3>
                                                    <p>Total Pending Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/new-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $totalResolvedCases }}</h3>
                                                    <p>Total Resolved Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $upcomingHearings }}</h3>
                                                    <p>Upcoming Hearing Dates</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $interimOrders }}</h3>
                                                    <p>Interims Orders</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>{{ $awards }}</h3>
                                                    <p>Awards</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </div>
                                @endif

                                {{-- ################ DRP TYPE 5 Conciliator ################# --}}
                                {{-- ####################################################### --}}
                                @if (auth('drp')->check() && auth('drp')->user()->drp_type == 5)
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/active-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>50</h3>
                                                    <p>Total Filed Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/Resolved-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>50</h3>
                                                    <p>Total Pending Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/new-case.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>50</h3>
                                                    <p>Total Resolved Cases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>50</h3>
                                                    <p>Upcoming Hearing Dates</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 position-relative">
                                        <div class="custom-card row">
                                            <div class="col-4">
                                                <div class="icon-box-dash">
                                                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                        alt="" class="img-fluid img-dashboard">
                                                </div>
                                            </div>
                                            <div class="col-8" style="display: flex;">
                                                <div class="my-auto">
                                                    <h3>50</h3>
                                                    <p>Settlement Agreements</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>


                            @if (auth('drp')->check() && auth('drp')->user()->drp_type == 5)
                                <div id="upcoming-sessions" class="content-section row mt-xl-5 mt-3 d-none">
                                    <div class="col-12">
                                        <div class="custom-table-container">
                                            <table class="table custom-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-start ps-3">Date</th>
                                                        <th>Case ID</th>
                                                        <th>Session Type</th>
                                                        <th>Preparation Needed</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-start ps-3">20-02-2020</td>
                                                        <td>ID001</td>
                                                        <td>Initial Meeting</td>
                                                        <td>Review case file</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start ps-3">20-02-2020</td>
                                                        <td>ID002</td>
                                                        <td>Initial Meeting</td>
                                                        <td>Review case file</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start ps-3">20-02-2020</td>
                                                        <td>ID003</td>
                                                        <td>Initial Meeting</td>
                                                        <td>Review case file</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start ps-3">20-02-2020</td>
                                                        <td>ID004</td>
                                                        <td>Initial Meeting</td>
                                                        <td>Review case file</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div id="communication-log" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                   @foreach ($caseManagerData as $caseManagerId => $cases)
                                        @if(count($cases) > 0)
                                            <div class="custom-table-container mb-4">
                                                <div class="p-2">
                                                    <h5>Case Manager: {{ $cases[0]['case_manager_name'] }}</h5>
                                                    <p>Email: {{$cases[0]['case_manager_email']}} | Mobile: {{$cases[0]['case_manager_mobile']}}</p>
                                                    <p>Gender: {{$cases[0]['case_manager_gender']}} | Profession: {{$cases[0]['case_manager_profession']}}</p>
                                                    <p>Specialization: {{$cases[0]['case_manager_specialization']}}</p>
                                                </div>
                                                <table class="table custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-start ps-3">Loan No</th>
                                                            <th>Case Number</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cases as $case)
                                                            <tr>
                                                                <td class="text-start ps-3">{{ $case['loan_number'] }}</td>
                                                                <td>{{ $case['case_number'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".custom-tab").click(function() {
                $(".custom-tab").removeClass("active");
                $(this).addClass("active");

                let target = $(this).data("target");
                $(".content-section").addClass("d-none");
                $("#" + target).removeClass("d-none");
            });
        });
    </script>
@endsection
