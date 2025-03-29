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
                            <div class="custom-dropdown">
                                <select name="Organization" class="form-control form-select form-dashboard-select">
                                    <option value="">Products</option>
                                    <option value="">Two Wheeler</option>
                                    <option value="">Four Wheeler</option>
                                </select>
                            </div>
                            <div class="custom-dropdown">
                                <select name="Organization" class="form-control form-select form-dashboard-select">
                                    <option value="">Case Type</option>
                                    <option value="">Conciliation</option>
                                    <option value="">Arbitration</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-xl-4 mt-3 justify-content-center">
                        <!-- Tabs Section -->
                        <div class="d-flex flex-wrap justify-content-center">
                            <div class="custom-tab active" data-target="case-type-overview">CASE TYPE OVERVIEW</div>
                            {{-- <div class="custom-tab" data-target="live-case-details">LIVE CASE DETAILS</div>
                            <div class="custom-tab" data-target="closed-case-details">CLOSED CASE DETAILS</div>
                            <div class="custom-tab" data-target="case-type-distribution">CASE TYPE DISTRIBUTION</div> --}}
                            <div class="custom-tab" data-target="product-wise-distribution">PRODUCT WISE DISTRIBUTION</div>
                            {{-- <div class="custom-tab" data-target="loan-product-case-details">LOAN PRODUCT CASE DETAILS</div>
                            <div class="custom-tab" data-target="communication-log">COMMUNICATION LOG</div> --}}
                            <div class="custom-tab" data-target="support-contacts">SUPPORT CONTACTS</div>
                        </div>

                        <!-- Content Boxes -->
                        <div id="case-type-overview" class="content-section row gy-lg-4 gy-3">
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
                                            <p>No of Case Filled</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                            </div>
                            {{-- <div class="col-md-6 col-12 position-relative">
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
                                            <p>Total Active Cases</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                            </div> --}}
                            <div class="col-md-6 col-12 position-relative">
                                <div class="custom-card row">
                                    <div class="col-4">
                                        <div class="icon-box-dash">
                                            <img src="{{ asset('assets/img/dashboard/new-case.png') }}" alt="" class="img-fluid img-dashboard">
                                        </div>
                                    </div>
                                    <div class="col-8" style="display: flex;">
                                        <div class="my-auto">
                                            <h3>50</h3>
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
                                            <img src="{{ asset('assets/img/dashboard/case-timing.png') }}"
                                                alt="" class="img-fluid img-dashboard">
                                        </div>
                                    </div>
                                    <div class="col-8" style="display: flex;">
                                        <div class="my-auto">
                                            <h3>50</h3>
                                            <p>New Cases Filled</p>
                                            <small>(In Current month)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
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
                                            <h3>20</h3>
                                            <p>Award Passed</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
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
                                            <h3>20</h3>
                                            <p>Interim Order Passed</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
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
                                            {{-- <h3>20</h3> --}}
                                            <p>Upcoming Hearing Dates with Stages</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-all">view all <i class="fa-solid fa-circle-arrow-right"></i></div>
                            </div>
                        </div>

                        {{-- <div id="live-case-details" class="content-section row d-none mt-xl-5 mt-3">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Case ID</th>
                                                <th>Parties</th>
                                                <th>Loan.NO.</th>
                                                <th>Product</th>
                                                <th>filing date</th>
                                                <th>Status</th>
                                                <th>signatory name</th>
                                                <th>claim amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>12-02-25</td>
                                                <td>Completed</td>
                                                <td>Action</td>
                                                <td>000000</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>C vs D</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>12-02-25</td>
                                                <td>Completed</td>
                                                <td>Action</td>
                                                <td>000000</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>G vs H</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>12-02-25</td>
                                                <td>Completed</td>
                                                <td>Action</td>
                                                <td>000000</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>E vs F</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>12-02-25</td>
                                                <td>Completed</td>
                                                <td>Action</td>
                                                <td>000000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="closed-case-details" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Case ID</th>
                                                <th>Parties</th>
                                                <th>Loan.NO.</th>
                                                <th>Product</th>
                                                <th>filing date</th>
                                                <th>status</th>
                                                <th>claim amount</th>
                                                <th>interim order</th>
                                                <th>interim Copy</th>
                                                <th>interim Copy</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>20-02-25</td>
                                                <td>Active</td>
                                                <td>000000</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>20-02-25</td>
                                                <td>Active</td>
                                                <td>000000</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>20-02-25</td>
                                                <td>Active</td>
                                                <td>000000</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">ID001</td>
                                                <td>A vs B</td>
                                                <td>12</td>
                                                <td>Meeting</td>
                                                <td>20-02-25</td>
                                                <td>Active</td>
                                                <td>000000</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                                <td>-----</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="case-type-distribution" class="content-section row mt-xl-5 mt-3 d-none">
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

                        <div id="product-wise-distribution" class="content-section row mt-xl-5 mt-3 d-none">
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
                        </div>

                        {{-- <div id="loan-product-case-details" class="content-section row mt-xl-5 mt-3 d-none">
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
                        </div>

                        <div id="communication-log" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">Date</th>
                                                <th>case iD</th>
                                                <th>Type</th>
                                                <th>Recipient</th>
                                                <th>Summary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>iD001</td>
                                                <td>Email</td>
                                                <td>Abitrator</td>
                                                <td>Clarification on Procedure</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>iD001</td>
                                                <td>Call</td>
                                                <td>Opposing Counsel</td>
                                                <td>Settlement Discussion</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>iD001</td>
                                                <td>Meeting</td>
                                                <td>All Parties</td>
                                                <td>Hearing Schedule Change</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">20-02-25</td>
                                                <td>iD001</td>
                                                <td>Notice</td>
                                                <td>Abitrator</td>
                                                <td>Hearing Schedule Change</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> --}}

                        <div id="support-contacts" class="content-section row mt-xl-5 mt-3 d-none">
                            <div class="col-12">
                                <div class="custom-table-container">
                                    <table class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start ps-3">role</th>
                                                <th>name</th>
                                                <th>contact information</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-start ps-3">Demo</td>
                                                <td>Test</td>
                                                <td>1234567891</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Demo</td>
                                                <td>Test</td>
                                                <td>1234567891</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Demo</td>
                                                <td>Test</td>
                                                <td>1234567891</td>
                                            </tr>
                                            <tr>
                                                <td class="text-start ps-3">Demo</td>
                                                <td>Test</td>
                                                <td>1234567891</td>
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