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

                <div class="col-md-9 col-12 mt-md-0 mt-3">
                    <div class="card-inner card-dashboard">
                        <div class="row justify-content-md-between justify-content-center">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0">Dashboard</h5>
                            </div>
                            <div class="col-auto ms-md-auto ms-none d-flex mt-md-0 mt-2">
                                <span class="case-id">Case ID : IND000001</span>
                            </div>
                        </div>
                        <div class="row mt-xl-4 mt-3 justify-content-center">
                            <!-- Tabs Section -->
                            <div class="d-flex flex-wrap justify-content-center">
                                <div class="custom-tab active" data-target="case-overview">CASE OVERVIEW</div>
                                <div class="custom-tab" data-target="important-date">IMPORTANT DATE</div>
                                <div class="custom-tab" data-target="document-checklist">DOCUMENT CHECKLIST</div>
                                <div class="custom-tab" data-target="communication-log">COMMUNICATION LOG</div>
                                <div class="custom-tab" data-target="financial-summary">FINANCIAL SUMMARY</div>
                                <div class="custom-tab" data-target="case-progress">CASE PROGRESS</div>
                                <div class="custom-tab" data-target="my-position-summary">MY POSITION SUMMARY</div>
                                <div class="custom-tab" data-target="opposing-party-position">OPPOSING PARTY'S POSITION</div>
                                <div class="custom-tab" data-target="to-do-list">TO-DO LIST</div>
                                <div class="custom-tab" data-target="support-contacts">SUPPORT CONTACTS</div>
                                <div class="custom-tab" data-target="notes-reminders">NOTES/ REMINDERS</div>
                            </div>

                            <!-- Content Boxes -->
                            <div id="case-overview" class="content-section row gy-lg-4 gy-3">
                                <div class="col-md-12 col-12 position-relative">
                                    <div class="custom-case-card">
                                       <h4 class="case-heading">Case Overview</h4>
                                       <div class="row gx-5 gy-3">
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Dispute Type</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Neutral Third Party</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Case Administered</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Opposing Party</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Case Status</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <p class="case-title">Days Since filing</p>
                                            <p class="case-text">______________</p>
                                        </div>
                                       </div>
                                    </div>
                                </div>
                            </div>

                            <div id="important-date" class="content-section row d-none mt-xl-5 mt-3">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">Event</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>20-02-25</td>
                                                    <td>Completed</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>20-02-25</td>
                                                    <td>Upcoming</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>20-02-25</td>
                                                    <td>Completed</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>20-02-25</td>
                                                    <td>Upcoming</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="document-checklist" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">Document</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>Completed</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>Upcoming</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>Completed</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>Upcoming</td>
                                                    <td>20-02-25</td>
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
                                                    <td>Abitrator</td>
                                                    <td>Clarification on Procedure</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>Call</td>
                                                    <td>Opposing Counsel</td>
                                                    <td>Settlement Discussion</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>Meeting</td>
                                                    <td>All Parties</td>
                                                    <td>Hearing Schedule Change</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>Notice</td>
                                                    <td>Abitrator</td>
                                                    <td>Hearing Schedule Change</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="financial-summary" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">item</th>
                                                    <th>amount</th>
                                                    <th>status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">filing fess</td>
                                                    <td>amount</td>
                                                    <td>paid/due</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">legal representation</td>
                                                    <td>amount</td>
                                                    <td>paid/due</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">expert fess</td>
                                                    <td>amount</td>
                                                    <td>paid/due</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">total costs to due</td>
                                                    <td>amount</td>
                                                    <td>paid/due</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="case-progress" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">stage</th>
                                                    <th>status</th>
                                                    <th>notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">initial filing</td>
                                                    <td>completed</td>
                                                    <td>any notes</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">documentation exchange</td>
                                                    <td>completed</td>
                                                    <td>any notes</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">preliminary hearing</td>
                                                    <td>completed</td>
                                                    <td>any notes</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">main procedings</td>
                                                    <td>completed</td>
                                                    <td>any notes</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="my-position-summary" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <h6 class="case-heading">Initial Claim/ Defence</h6>
                                    <textarea name="initial-claim" id="initial-claim" class="w-100 rounded-1" rows="6" placeholder="brief descripition"></textarea>
                                </div>
                                <div class="col-12">
                                    <h6 class="case-heading">Current Position</h6>
                                    <textarea name="initial-claim" id="initial-claim" class="w-100 rounded-1" rows="6" placeholder="brief descripition"></textarea>
                                </div>
                            </div>

                            <div id="opposing-party-position" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    
                                </div>
                            </div>

                            <div id="to-do-list" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">task</th>
                                                    <th>date</th>
                                                    <th>status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">initial filing</td>
                                                    <td>20-02-25</td>
                                                    <td>completed</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">documentation exchange</td>
                                                    <td>20-02-25</td>
                                                    <td>completed</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">preliminary hearing</td>
                                                    <td>20-02-25</td>
                                                    <td>completed</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">main procedings</td>
                                                    <td>20-02-25</td>
                                                    <td>completed</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="support-contacts" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">Date</th>
                                                    <th>name</th>
                                                    <th>contact information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>iD001</td>
                                                    <td>email</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>iD001</td>
                                                    <td>call</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>iD001</td>
                                                    <td>meeting</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">20-02-25</td>
                                                    <td>iD001</td>
                                                    <td>notice</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div id="notes-reminders" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                   
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
