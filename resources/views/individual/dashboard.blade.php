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
                                @if (!empty($caseData) && $caseData->isNotEmpty())
                                    @foreach ($caseData as $case)
                                        @foreach ($case->payments as $payment)
                                            <span class="case-id">Case ID : {{ $payment->file_case_no }}</span>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="row mt-xl-4 mt-3 justify-content-center">
                            <!-- Tabs Section -->
                            <div class="d-flex flex-wrap justify-content-center">
                                <div class="custom-tab active" data-target="case-overview">CASE OVERVIEW</div>
                                <div class="custom-tab" data-target="important-date">IMPORTANT DATE</div>
                                <div class="custom-tab" data-target="document-checklist">DOCUMENT CHECKLIST</div>
                                {{-- <div class="custom-tab" data-target="communication-log">COMMUNICATION LOG</div> --}}
                                <div class="custom-tab" data-target="financial-summary">FINANCIAL SUMMARY</div>
                                <div class="custom-tab" data-target="case-progress">CASE PROGRESS</div>
                                <div class="custom-tab" data-target="opposing-party-position">OPPOSING PARTY'S POSITION
                                </div>
                                <div class="custom-tab" data-target="to-do-list">TO-DO LIST</div>
                                <div class="custom-tab" data-target="support-contacts">SUPPORT CONTACTS</div>
                                {{-- <div class="custom-tab" data-target="notes-reminders">NOTES/ REMINDERS</div> --}}
                            </div>

                            <!-- Content Boxes -->
                            <div id="case-overview" class="content-section row gy-lg-4 gy-3">
                                @if (!empty($caseData) && $caseData->isNotEmpty())
                                    <div class="col-md-12 col-12 position-relative">
                                        <div class="custom-case-card">
                                            @foreach ($caseData as $case)
                                                <hr style="border: 3px solid #ffb000; margin-bottom:0px; margin-top:0px;">
                                                <h4 class="case-heading">Case Overview :</h4>
                                                <div class="row gx-5 gy-3">
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Dispute Type</p>
                                                        <p class="case-text">
                                                            {{ config('constant.case_type')[$case->case_type] ?? 'Unknown Case Type' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Case Status</p>
                                                        <p
                                                            class="case-text {{ $case->status == 1 ? 'text-success' : 'text-danger' }}">
                                                            {{ $case->status == 1 ? 'Active' : 'Inactive' }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Days Since filing</p>
                                                        <p class="case-text">
                                                            {{ intval(\Carbon\Carbon::parse($case->created_at)->diffInDays(now())) }}
                                                            days</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Brief of Case</p>
                                                        <p class="case-text"> {{ $case->brief_of_case }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Disputed Amount</p>
                                                        <p class="case-text"> {{ $case->amount_in_dispute }}</p>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">First Party</p>
                                                        <p class="case-text"> {{ $case->claimant_first_name }}
                                                            {{ $case->claimant_last_name }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">First Party Mobile</p>
                                                        <p class="case-text"> {{ $case->claimant_mobile }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">First Party Email</p>
                                                        <p class="case-text"> {{ $case->claimant_email }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">First Party Address</p>
                                                        <p class="case-text">
                                                            {{ $case->claimant_address_type == 1 ? 'Home' : ($case->claimant_address_type == 2 ? 'Office' : 'Other') }} - 
                                                            {{ $case->claimant_address1 }}, 
                                                            {{ $case->claimant_address2 }}, 
                                                            {{ $case->claimant_state_name }}, 
                                                            {{ $case->claimant_city_name }}, 
                                                            {{ $case->claimant_pincode }}
                                                        </p>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Opposing Party</p>
                                                        <p class="case-text"> {{ $case->respondent_first_name }}
                                                            {{ $case->respondent_last_name }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Opposing Party Mobile</p>
                                                        <p class="case-text"> {{ $case->respondent_mobile }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Opposing Party Email</p>
                                                        <p class="case-text"> {{ $case->respondent_email }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Opposing Party Address</p>
                                                        <p class="case-text">
                                                            {{ $case->respondent_address_type == 1 ? 'Home' : ($case->respondent_address_type == 2 ? 'Office' : 'Other') }} - 
                                                            {{ $case->respondent_address1 }}, 
                                                            {{ $case->respondent_address2 }}, 
                                                            {{ $case->respondent_state_name }}, 
                                                            {{ $case->respondent_city_name }}, 
                                                            {{ $case->respondent_pincode }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <hr style="border: 3px solid #ffb000; margin-bottom:0px;">

                                                <h4 class="case-heading">My Position Summary :</h4>
                                                <div class="row gx-5 gy-3">
                                                    <div class="col-12">
                                                    <table class="table custom-table bg-white">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-start ps-3 py-1">Initial Claim/ Defence</th>
                                                                <th class="py-1">Current Position</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-start ps-3 py-1 border-bottom-0">---------</td>
                                                                <td class="py-1 border-bottom-0">--------</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start ps-3 py-1 border-bottom-0">---------</td>
                                                                <td class="py-1 border-bottom-0">--------</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start ps-3 py-1 border-bottom-0">---------</td>
                                                                <td class="py-1 border-bottom-0">--------</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                </div>

                                                <hr style="border: 3px solid #ffb000; margin-bottom:0px;">

                                                <h4 class="case-heading">Assigned Cases :</h4>
                                                @if ($case->assignedCases->isNotEmpty())
                                                    @foreach ($case->assignedCases as $assigned)
                                                        <div class="row gx-5 gy-3">
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Assigned To arbitrator</p>
                                                                <p class="case-text">
                                                                    {{ $assigned->arbitrator?->name ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Assigned To Advocate</p>
                                                                <p class="case-text">
                                                                    {{ $assigned->advocate?->name ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Assigned To Case Manager</p>
                                                                <p class="case-text">
                                                                    {{ $assigned->caseManager?->name ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Assigned To Mediator</p>
                                                                <p class="case-text">
                                                                    {{ $assigned->mediator?->name ?? 'N/A' }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Assigned To Conciliator</p>
                                                                <p class="case-text">
                                                                    {{ $assigned->conciliator?->name ?? 'N/A' }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <small class="text-center justify-content-center">
                                                        <p class="mb-0 text-danger">Wait for case assignment.</p>
                                                    </small>
                                                @endif

                                                <hr style="border: 3px solid #ffb000; margin-bottom:0px;">

                                                <h4 class="case-heading">Payments :</h4>
                                                @if ($case->payments->isNotEmpty())
                                                    @foreach ($case->payments as $payment)
                                                        <div class="row gx-5 gy-3">
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Case</p>
                                                                <p class="case-text">{{ $payment->file_case_no }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Transaction ID</p>
                                                                <p class="case-text">{{ $payment->transaction_id }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Amount</p>
                                                                <p class="case-text">{{ $payment->payment_amount }}</p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Payment Date</p>
                                                                <p class="case-text">
                                                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-4 col-6">
                                                                <p class="case-title">Status</p>
                                                                <p
                                                                    class="case-text {{ $payment->payment_status == 1 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $payment->payment_status == 1 ? 'Paid' : 'UnPaid' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <small class="text-center justify-content-center">
                                                        <p class="mb-0 text-danger">Please Make Payment of Filed Case.</p>
                                                    </small>
                                                @endif

                                                <hr style="border: 3px solid #ffb000; margin-bottom:0px;">

                                                <h4 class="case-heading">Case Documents :</h4>
                                                <div class="row gx-5 gy-3">
                                                    @php
                                                        $documents = [
                                                            'application_form' => $case->application_form,
                                                            'account_statement' => $case->account_statement,
                                                            'foreclosure_statement' => $case->foreclosure_statement,
                                                            'loan_agreement' => $case->loan_agreement,
                                                            'other_document' => $case->other_document,
                                                        ];
                                                    @endphp

                                                    @foreach ($documents as $label => $filename)
                                                        @php
                                                            $filePath = $filename
                                                                ? asset('storage/' . $filename)
                                                                : null;
                                                            $extension = $filename
                                                                ? strtolower(pathinfo($filename, PATHINFO_EXTENSION))
                                                                : null;
                                                        @endphp

                                                        <div class="col-md-4 col-6">
                                                            <p class="case-title">
                                                                {{ str_replace('_', ' ', ucfirst($label)) }}
                                                            </p>
                                                            <p class="case-text">
                                                                @if ($filePath && $extension)
                                                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                        <img src="{{ $filePath }}"
                                                                            class="img-thumbnail" width="100"
                                                                            alt="Image Preview" />
                                                                    @elseif($extension === 'pdf')
                                                                        <a class="text-decoration-none case-text"
                                                                            style="font-size: 13px"
                                                                            href="{{ $filePath }}" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" />
                                                                            View PDF
                                                                        </a>
                                                                    @elseif(in_array($extension, ['doc', 'docx']))
                                                                        <a class="text-decoration-none case-text"
                                                                            style="font-size: 13px"
                                                                            href="{{ $filePath }}" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/doc.png') }}"
                                                                                height="30" alt="DOC File" />
                                                                            View Document
                                                                        </a>
                                                                    @else
                                                                        <a class="text-decoration-none case-text"
                                                                            style="font-size: 13px"
                                                                            href="{{ $filePath }}"
                                                                            target="_blank">Download
                                                                            File</a>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">No file
                                                                        uploaded</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endforeach

                                                    <hr>

                                                    <div
                                                        class="col-12 d-flex justify-content-between item-align-self my-0">
                                                        <p class="case-title my-auto">Claimant Petition
                                                            Filed Or Not</p>
                                                        <a class="text-decoration-none case-text" style="font-size: 13px"
                                                            href="#" target="_blank">
                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                height="30" alt="PDF File" />
                                                            View PDF
                                                        </a>
                                                    </div>

                                                    <hr>

                                                    <div
                                                        class="col-12 d-flex justify-content-between item-align-self my-0">
                                                        <p class="case-title my-auto">Evidence Filed Or
                                                            Not</p>
                                                        <a class="text-decoration-none case-text" style="font-size: 13px"
                                                            href="#" target="_blank">
                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                height="30" alt="PDF File" />
                                                            View PDF
                                                        </a>
                                                    </div>

                                                    <hr>

                                                    <div
                                                        class="col-12 d-flex justify-content-between item-align-self my-0">
                                                        <p class="case-title my-auto">Award Recieved Or
                                                            Not</p>
                                                        <a class="text-decoration-none case-text" style="font-size: 13px"
                                                            href="#" target="_blank">
                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                height="30" alt="PDF File" />
                                                            View PDF
                                                        </a>
                                                    </div>

                                                    <hr>

                                                    <div
                                                        class="col-12 d-flex justify-content-between item-align-self my-0">
                                                        <p class="case-title my-auto">Interim Order</p>
                                                        <a class="text-decoration-none case-text" style="font-size: 13px"
                                                            href="#" target="_blank">
                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                height="30" alt="PDF File" />
                                                            View PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div id="important-date" class="content-section row d-none mt-xl-5 mt-3">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">Event</th>
                                                    <th>Date</th>
                                                    <th>Notice</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">Respondent Notice</td>
                                                    <td>20-02-25</td>
                                                    <td>pdf download</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">Notice</td>
                                                    <td>20-02-25</td>
                                                    <td>pdf download</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">Hearing</td>
                                                    <td>20-02-25</td>
                                                    <td>pdf download</td>
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
                                                    <th class="text-start ps-3">Claimant/ Respodent</th>
                                                    <th>Doc</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>pdf download</td>
                                                    <td>Completed</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>pdf download</td>
                                                    <td>Upcoming</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>pdf download</td>
                                                    <td>Completed</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-start ps-3">ID001</td>
                                                    <td>pdf download</td>
                                                    <td>Upcoming</td>
                                                    <td>20-02-25</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- <div id="communication-log" class="content-section row mt-xl-5 mt-3 d-none">
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
                            </div> --}}

                            <div id="financial-summary" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <div class="custom-table-container">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-start ps-3">Item</th>
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

                            <div id="opposing-party-position" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">
                                    <p>Appeared/Not Appeared</p>
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
                                    <p>Case Manager Details here...</p>
                                </div>
                            </div>

                            {{-- <div id="notes-reminders" class="content-section row mt-xl-5 mt-3 d-none">
                                <div class="col-12">

                                </div>
                            </div> --}}

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
                    showCancelButton: false,
                    confirmButtonText: "Update Now",
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('individual.profile') }}";
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
@endsection
