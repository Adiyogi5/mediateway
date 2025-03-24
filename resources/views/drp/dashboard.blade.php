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
                    <div class="row justify-content-md-between justify-content-center">
                        <div class="col-auto align-self-center">
                            <h5 class="mb-0">Dashboard</h5>
                        </div>
                        <div class="col-auto ms-md-auto mx-auto d-flex mt-md-0 mt-2">
                            <a href="#" class="btn btn-dasboard">Individual</a>
                            <div class="custom-dropdown">
                                <select name="Organization" class="form-control form-select form-dashboard-select">
                                    <option value="">Organization</option>
                                </select>
                            </div>
                            <div class="custom-dropdown">
                                <select name="Organization" class="form-control form-select form-dashboard-select">
                                    <option value="">Case Type</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-xl-5 mt-3 justify-content-center">
                        <!-- Tabs Section -->
                        <div class="d-flex flex-wrap justify-content-center">
                            <div class="custom-tab active" data-target="case-overview">CASE OVERVIEW</div>
                            <div class="custom-tab" data-target="status-summary">CASE STATUS SUMMARY</div>
                            <div class="custom-tab" data-target="upcoming-sessions">UPCOMING SESSIONS</div>
                            <div class="custom-tab" data-target="case-details">CASE DETAILS</div>
                            <div class="custom-tab" data-target="resolution-progress">RESOLUTION PROGRESS</div>
                            <div class="custom-tab" data-target="communication-log">COMMUNICATION lOG</div>
                        </div>

                        <!-- Content Boxes -->
                        <div id="case-overview" class="content-section row gy-lg-4 gy-3">
                            <div class="col-md-6 col-12 position-relative">
                                <div class="custom-card row">
                                    <div class="col-4">
                                        <div class="icon-box-dash">
                                            <img src="{{ asset('assets/img/dashboard/active-case.png') }}"
                                                alt="" class="img-fluid img-dashboard">
                                        </div>
                                    </div>
                                    <div class="col-8" style="display: contents;">
                                        <div class="my-auto">
                                            <h3>50</h3>
                                            <p>Total Active Cases</p>
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
                                    <div class="col-8" style="display: contents;">
                                        <div class="my-auto">
                                            <h3>50</h3>
                                            <p>Cases Resolved</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                            </div>
                            <div class="col-md-6 col-12 position-relative">
                                <div class="custom-card row">
                                    <div class="col-4">
                                        <div class="icon-box-dash">
                                            <img src="{{ asset('assets/img/dashboard/new-case.png') }}" alt="" class="img-fluid img-dashboard">
                                        </div>
                                    </div>
                                    <div class="col-8" style="display: contents;">
                                        <div class="my-auto">
                                            <h3>50</h3>
                                            <p>New Cases</p>
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
                                    <div class="col-8" style="display: contents;">
                                        <div class="my-auto">
                                            <h3>50</h3>
                                            <p>Pending Cases</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                            </div>
                        </div>

                        <div id="status-summary" class="content-section row d-none mt-xl-5 mt-3">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Status</th>
                                                <th>Number Of Cases</th>
                                                <th>Percentages</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">Pre-Mediation</td>
                                                <td>2</td>
                                                <td>20%</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Initial Session</td>
                                                <td>6</td>
                                                <td>30%</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Ongoing Negotiation</td>
                                                <td>8</td>
                                                <td>60%</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Post-Mediation</td>
                                                <td>5</td>
                                                <td>50%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

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

                        <div id="case-details" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Case ID</th>
                                                <th>Parties</th>
                                                <th>Loan.No.</th>
                                                <th>Product</th>
                                                <th>Notice Service Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Initial Meeting</td>
                                                <td>Action</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Initial Meeting</td>
                                                <td>Action</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Initial Meeting</td>
                                                <td>Action</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Initial Meeting</td>
                                                <td>Action</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="resolution-progress" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Case ID</th>
                                                <th>Initial Position</th>
                                                <th>Current Positions</th>
                                                <th>Key Issues Reamaining</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>Brief description</td>
                                                <td>Brief description</td>
                                                <td>List key issues</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>Brief description</td>
                                                <td>Brief description</td>
                                                <td>List key issues</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>Brief description</td>
                                                <td>Brief description</td>
                                                <td>List key issues</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>Brief description</td>
                                                <td>Brief description</td>
                                                <td>List key issues</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="communication-log" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Date</th>
                                                <th>Type</th>
                                                <th>With</th>
                                                <th>Summary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>Email</td>
                                                <td>Person/entity</td>
                                                <td>Brief description</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>Phone call</td>
                                                <td>Person/entity</td>
                                                <td>Brief description</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>Meeting</td>
                                                <td>Person/entity</td>
                                                <td>Brief description</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>Email</td>
                                                <td>Person/entity</td>
                                                <td>Brief description</td>
                                            </tr>
                                        </tbody>
                                    </table>
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