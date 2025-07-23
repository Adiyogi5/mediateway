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
                                <div class="custom-tab active" data-target="product-type-overview">OVERVIEW</div>
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a> --}}
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a> --}}
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a> --}}
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a> --}}
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.cases.filecaseview') }}">
                                        <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                                    </a> --}}
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
                                    {{-- <a class="text-decoration-none text-dark"
                                        href="{{ route('organization.organizationcourtroom.organizationcourtroomlist') }}">
                                        <div class="view-all">
                                            view all <i class="fa-solid fa-circle-arrow-right"></i>
                                        </div>
                                    </a> --}}
                                </div>
                            </div>

                            <div id="support-contacts" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    {{-- ############## arbitratorSearch ################ --}}
                                    <div class="border border-3 rounded p-1 mb-lg-4 mb-3">
                                        <div class="d-flex align-item-self justify-content-center my-2 gap-2">
                                            <input type="text" id="arbitratorSearch"
                                                placeholder="Search Arbitrator: Loan No / Case Number"
                                                class="form-control" />
                                            <button onclick="filterArbitratorTable()"
                                                class="btn btn-primary">Search</button>
                                            <button onclick="resetArbitratorTable()"
                                                class="btn btn-secondary">Reset</button>
                                        </div>
                                        <div id="arbitratorSection">
                                            @foreach ($arbitratorData as $arbitratorId => $cases)
                                                @if (count($cases) > 0)
                                                    <div class="custom-table-container mb-4 overflow-scroll arbitrator-table"
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
                                                                            <a class="text-decoration-none cursor-pointer text-secondary"
                                                                                href="{{ route('organization.cases.viewcasedetail', $case['id']) }}">
                                                                                {{ $case['loan_number'] }}
                                                                            </a>
                                                                        </td>
                                                                        <td class="p-1">
                                                                            <a class="text-decoration-none cursor-pointer text-secondary"
                                                                                href="{{ route('organization.cases.viewcasedetail', $case['id']) }}">
                                                                                {{ $case['case_number'] }}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>


                                    {{-- ################ caseManagerSearch ################# --}}
                                    <div class="border border-3 rounded p-1">
                                        <div class="d-flex align-item-self justify-content-center my-2 gap-2">
                                            <input type="text" id="caseManagerSearch"
                                                placeholder="Search Case Manager: Loan No / Case Number"
                                                class="form-control" />
                                            <button onclick="filterCaseManagerTable()"
                                                class="btn btn-primary">Search</button>
                                            <button onclick="resetCaseManagerTable()"
                                                class="btn btn-secondary">Reset</button>
                                        </div>
                                        <div id="caseManagerSection">
                                            @foreach ($caseManagerData as $caseManagerId => $cases)
                                                @if (count($cases) > 0)
                                                    <div class="custom-table-container mb-4 overflow-scroll case-manager-table"
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
                                                                            <a class="text-decoration-none cursor-pointer text-secondary"
                                                                                href="{{ route('organization.cases.viewcasedetail', $case['id']) }}">
                                                                                {{ $case['loan_number'] }}
                                                                            </a>
                                                                        </td>
                                                                        <td class="p-1">
                                                                            <a class="text-decoration-none cursor-pointer text-secondary"
                                                                                href="{{ route('organization.cases.viewcasedetail', $case['id']) }}">
                                                                                {{ $case['case_number'] }}
                                                                            </a>
                                                                        </td>
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
            </div>
        </div>
    </section>
@endsection

@section('js')
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
    <script>
        function filterArbitratorTable() {
            const query = document.getElementById("arbitratorSearch").value.toLowerCase().trim();
            const containers = document.querySelectorAll("#arbitratorSection .arbitrator-table");

            containers.forEach(container => {
                let matchFound = false;
                const rows = container.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    const loanNo = row.cells[0]?.innerText.toLowerCase();
                    const caseNo = row.cells[1]?.innerText.toLowerCase();
                    const isMatch = loanNo.includes(query) || caseNo.includes(query);

                    row.style.display = isMatch ? "" : "none";
                    if (isMatch) matchFound = true;
                });

                container.style.display = matchFound ? "" : "none";
            });
        }

        function filterCaseManagerTable() {
            const query = document.getElementById("caseManagerSearch").value.toLowerCase().trim();
            const containers = document.querySelectorAll("#caseManagerSection .case-manager-table");

            containers.forEach(container => {
                let matchFound = false;
                const rows = container.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    const loanNo = row.cells[0]?.innerText.toLowerCase();
                    const caseNo = row.cells[1]?.innerText.toLowerCase();
                    const isMatch = loanNo.includes(query) || caseNo.includes(query);

                    row.style.display = isMatch ? "" : "none";
                    if (isMatch) matchFound = true;
                });

                container.style.display = matchFound ? "" : "none";
            });
        }
    </script>
    <script>
        document.getElementById("arbitratorSearch").addEventListener("keypress", function(e) {
            if (e.key === "Enter") filterArbitratorTable();
        });

        document.getElementById("caseManagerSearch").addEventListener("keypress", function(e) {
            if (e.key === "Enter") filterCaseManagerTable();
        });

        function resetArbitratorTable() {
            const input = document.getElementById("arbitratorSearch");
            input.value = "";
            input.focus();

            const containers = document.querySelectorAll("#arbitratorSection .arbitrator-table");
            containers.forEach(container => {
                container.style.display = "";
                container.querySelectorAll("tbody tr").forEach(row => row.style.display = "");
            });
        }

        function resetCaseManagerTable() {
            const input = document.getElementById("caseManagerSearch");
            input.value = "";
            input.focus();

            const containers = document.querySelectorAll("#caseManagerSection .case-manager-table");
            containers.forEach(container => {
                container.style.display = "";
                container.querySelectorAll("tbody tr").forEach(row => row.style.display = "");
            });
        }
    </script>
@endsection
