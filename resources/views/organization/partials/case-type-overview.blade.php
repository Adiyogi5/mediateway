{{-- dashboard/partials/case-type-overview.blade.php --}}
<div class="content-section row gy-lg-4 gy-3">
    <div class="col-md-6 col-12 position-relative">
        <div class="custom-card row">
            <div class="col-4">
                <div class="icon-box-dash">
                    <img src="{{ asset('assets/img/dashboard/active-case.png') }}" alt=""
                        class="img-fluid img-dashboard">
                </div>
            </div>
            <div class="col-8" style="display: flex;">
                <div class="my-auto">
                    <h3>{{ $totalFiledCases }}</h3>
                    <p>No of Case Filled</p>
                </div>
            </div>
        </div>
        <a class="text-decoration-none text-dark" href="{{ route('organization.cases.filecaseview') }}">
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
        <a class="text-decoration-none text-dark" href="{{ route('organization.cases.filecaseview') }}">
            <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
        </a>
    </div>
    <div class="col-md-6 col-12 position-relative">
        <div class="custom-card row">
            <div class="col-4">
                <div class="icon-box-dash">
                    <img src="{{ asset('assets/img/dashboard/case-timing.png') }}" alt=""
                        class="img-fluid img-dashboard">
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
        <a class="text-decoration-none text-dark" href="{{ route('organization.cases.filecaseview') }}">
            <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
        </a>
    </div>
    <div class="col-md-6 col-12 position-relative">
        <div class="custom-card row">
            <div class="col-4">
                <div class="icon-box-dash">
                    <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}" alt=""
                        class="img-fluid img-dashboard">
                </div>
            </div>
            <div class="col-8" style="display: flex;">
                <div class="my-auto">
                    <h3>{{ $awards }}</h3>
                    <p>Award Passed</p>
                </div>
            </div>
        </div>
        <a class="text-decoration-none text-dark" href="{{ route('organization.cases.filecaseview') }}">
            <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
        </a>
    </div>
    <div class="col-md-6 col-12 position-relative">
        <div class="custom-card row">
            <div class="col-4">
                <div class="icon-box-dash">
                    <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}" alt=""
                        class="img-fluid img-dashboard">
                </div>
            </div>
            <div class="col-8" style="display: flex;">
                <div class="my-auto">
                    <h3>{{ $interimOrders }}</h3>
                    <p>Interim Order Passed</p>
                </div>
            </div>
        </div>
        <a class="text-decoration-none text-dark" href="{{ route('organization.cases.filecaseview') }}">
            <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
        </a>
    </div>
    <div class="col-md-6 col-12 position-relative">
        <div class="custom-card row">
            <div class="col-4">
                <div class="icon-box-dash">
                    <img src="{{ asset('assets/img/dashboard/upcoming-hearing.png') }}" alt=""
                        class="img-fluid img-dashboard">
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
