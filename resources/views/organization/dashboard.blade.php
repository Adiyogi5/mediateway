@extends('layouts.front')

@section('content')
    <section>
        <div class="container my-xl-5 my-lg-4 my-3">
            <div class="row">
                <div class="col-md-3 col-12">

                    @include('front.includes.sidebar_inner')

                </div>

                <div class="col-md-9 col-12">
                    <div class="card-inner card-dashboard">
                        <div class="row justify-content-lg-between justify-content-md-between justify-content-center">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0">Dashboard</h5>
                            </div>
                            <div class="col-auto ms-md-auto mx-md-0 mx-auto d-flex mt-md-0 mt-2">
                                <div class="custom-dropdown me-2">
                                    <select id="productFilter" class="form-control form-select form-dashboard-select">
                                        @foreach ($childOrganizations as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="custom-dropdown">
                                    <select id="productTypeFilter" class="form-control form-select form-dashboard-select">
                                        <option value="">Product Type</option>
                                        @foreach (config('constant.product_type') as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-xl-4 mt-3 justify-content-center">
                            <!-- Tabs Section -->
                            <div class="d-flex flex-wrap justify-content-center">
                                <div class="custom-tab active" data-target="product-type-overview">PRODUCT TYPE OVERVIEW</div>
                                {{-- <div class="custom-tab" data-target="product-wise-distribution">PRODUCT WISE DISTRIBUTION
                                </div> --}}
                                <div class="custom-tab" data-target="support-contacts">SUPPORT CONTACTS</div>
                            </div>

                            <!-- Content Boxes -->
                            <div id="product-type-overview" class="content-section row gy-lg-3 gy-2">
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
                                                <p>No of Case Filled</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a>
                                </div>
                                <div class="col-md-6 col-12 position-relative">
                                    <div class="custom-card row">
                                        <div class="col-4">
                                            <div class="icon-box-dash">
                                                <img src="{{ asset('assets/img/dashboard/new-case.png') }}" alt=""
                                                    class="img-fluid img-dashboard">
                                            </div>
                                        </div>
                                        <div class="col-8" style="display: flex;">
                                            <div class="my-auto">
                                                <h3>{{ $totalPendingCases }}</h3>
                                                <p>Total Pending Cases</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a>
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
                                                <h3>{{ $totalNewCases }}</h3>
                                                <p>New Cases Filled</p>
                                                <small>(In Current month)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a>
                                </div>
                                <div class="col-md-6 col-12 position-relative">
                                    <div class="custom-card row">
                                        <div class="col-4">
                                            <div class="icon-box-dash">
                                                <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}"
                                                    alt="" class="img-fluid img-dashboard">
                                            </div>
                                        </div>
                                        <div class="col-8" style="display: flex;">
                                            <div class="my-auto">
                                                <h3>{{ $awards }}</h3>
                                                <p>Award Passed</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a>
                                </div>
                                <div class="col-md-6 col-12 position-relative">
                                    <div class="custom-card row">
                                        <div class="col-4">
                                            <div class="icon-box-dash">
                                                <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}"
                                                    alt="" class="img-fluid img-dashboard">
                                            </div>
                                        </div>
                                        <div class="col-8" style="display: flex;">
                                            <div class="my-auto">
                                                <h3>{{ $interimOrders }}</h3>
                                                <p>Interim Order Passed</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a>
                                </div>
                                <div class="col-md-6 col-12 position-relative">
                                    <div class="custom-card row">
                                        <div class="col-4">
                                            <div class="icon-box-dash">
                                                <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}"
                                                    alt="" class="img-fluid img-dashboard">
                                            </div>
                                        </div>
                                        <div class="col-8" style="display: flex;">
                                            <div class="my-auto">
                                                <h3>{{ $upcomingHearings }}</h3>
                                                <p>Upcoming Hearing Dates with Stages</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.organizationcourtroom.organizationcourtroomlist') }}">
                                        <div class="view-all">
                                            view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            {{-- <div id="product-wise-distribution" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">type of dispute</th>
                                                    <th>number of cases</th>
                                                    <th>live</th>
                                                    <th>live</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">ecommerce</td>
                                                    <td>52</td>
                                                    <td>1</td>
                                                    <td>1</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ecommerce</td>
                                                    <td>52</td>
                                                    <td>10</td>
                                                    <td>22</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ecommerce</td>
                                                    <td>52</td>
                                                    <td>16</td>
                                                    <td>44</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ecommerce</td>
                                                    <td>52</td>
                                                    <td>45</td>
                                                    <td>15</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> --}}

                            <div id="support-contacts" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    @foreach ($arbitratorData as $arbitratorId => $cases)
                                        @if (count($cases) > 0)
                                            <div class="custom-table-container mb-4 overflow-scroll"
                                                style="max-height:600px;">
                                                <div class="p-2">
                                                    <p class="mb-0"><b>Arbitrator:
                                                        </b>{{ $cases[0]['arbitrator_name'] }}
                                                        {{ $cases[0]['arbitrator_last_name'] }}</p>
                                                    <span class="mb-0"><b>Email:
                                                        </b>{{ $cases[0]['arbitrator_email'] }} | <b>Mobile:
                                                        </b>{{ $cases[0]['arbitrator_mobile'] }}</span></br>
                                                    <span class="mb-0"><b>Gender:
                                                        </b>{{ $cases[0]['arbitrator_gender'] }} | <b>Profession:
                                                        </b>{{ $cases[0]['arbitrator_profession'] }}</span></br>
                                                    <span class="mb-0"><b>Specialization:
                                                        </b>{{ $cases[0]['arbitrator_specialization'] }}</span>
                                                </div>
                                                <table class="table custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-start ps-3 pt-1 pb-1 pe-1">Loan No</th>
                                                            <th class="p-1">Case Number</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cases as $case)
                                                            <tr>
                                                                <td class="text-start ps-3 pt-1 pb-1 pe-1">
                                                                    {{ $case['loan_number'] }}</td>
                                                                <td class="p-1">{{ $case['case_number'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    @endforeach
                                    @foreach ($caseManagerData as $caseManagerId => $cases)
                                        @if (count($cases) > 0)
                                            <div class="custom-table-container mb-4 overflow-scroll"
                                                style="max-height:600px;">
                                                <div class="p-2">
                                                    <p class="mb-0"><b>Case Manager:
                                                        </b>{{ $cases[0]['case_manager_name'] }}
                                                        {{ $cases[0]['case_manager_last_name'] }}</p>
                                                    <span class="mb-0"><b>Email:
                                                        </b>{{ $cases[0]['case_manager_email'] }} | <b>Mobile:
                                                        </b>{{ $cases[0]['case_manager_mobile'] }}</span></br>
                                                    <span class="mb-0"><b>Gender:
                                                        </b>{{ $cases[0]['case_manager_gender'] }} | <b>Profession:
                                                        </b>{{ $cases[0]['case_manager_profession'] }}</span></br>
                                                    <span class="mb-0"><b>Specialization:
                                                        </b>{{ $cases[0]['case_manager_specialization'] }}</span>
                                                </div>
                                                <table class="table custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-start ps-3 pt-1 pb-1 pe-1">Loan No</th>
                                                            <th class="p-1">Case Number</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cases as $case)
                                                            <tr>
                                                                <td class="text-start ps-3 pt-1 pb-1 pe-1">
                                                                    {{ $case['loan_number'] }}</td>
                                                                <td class="p-1">{{ $case['case_number'] }}</td>
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
    <script>
        $(document).ready(function() {
            function fetchDashboardData() {
                let productSlug = $('#productFilter').val();
                let productType = $('#productTypeFilter').val();

                $.ajax({
                    url: '{{ route('organization.dashboard.filter') }}',
                    type: 'GET',
                    data: {
                        product: productSlug,
                        product_type: productType
                    },
                    success: function(response) {
                        $('#product-type-overview').html(response.html);
                    },
                    error: function() {
                        alert('Something went wrong while fetching dashboard data.');
                    }
                });
            }

            $('#productFilter, #productTypeFilter').change(function() {
                fetchDashboardData();
            });
        });
    </script>
@endsection
