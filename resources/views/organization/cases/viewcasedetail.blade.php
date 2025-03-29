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
                                <h5 class="mb-0" data-anchor="data-anchor">Filed Case Details</h5>
                            </div>
                        </div>
                    </div>
                    @if ($caseData->isNotEmpty())
                        <div class="card-body table-padding">
                            <div class="col-md-12 col-12 position-relative">
                                <div class="custom-case-card">
                                    @foreach ($caseData as $case)
                                        <h4 class="case-heading">Case Overview :</h4>
                                        <div class="row gx-5 gy-3">
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Dispute Type</p>
                                                <p class="case-text">
                                                    {{ config('constant.case_type')[$case->case_type] ?? 'Unknown Case Type' }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">First Party</p>
                                                <p class="case-text">{{ $case->claimant_first_name }}
                                                    {{ $case->claimant_last_name }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Arbitrator</p>
                                                <p class="case-text">______________</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Opposite Party</p>
                                                <p class="case-text">{{ $case->respondent_first_name }}
                                                    {{ $case->respondent_last_name }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Case Stage</p>
                                                <p class="case-text {{ $case->status == 1 ? 'text-success' : 'text-danger' }}"> {{ $case->status == 1 ? 'Active' : 'Inactive' }}</p>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <p class="case-title">Days Since Filing</p>
                                                <p class="case-text">
                                                    {{ intval(\Carbon\Carbon::parse($case->created_at)->diffInDays(now())) }}
                                                    days
                                                </p>
                                            </div>
                                        </div>                                       
                                        
                                        <hr>

                                        <h4 class="case-heading">Assigned Cases :</h4>
                                        @if ($case->assignedCases->isNotEmpty())
                                            @foreach ($case->assignedCases as $assigned)
                                                <div class="row gx-5 gy-3">
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To arbitrator</p>
                                                        <p class="case-text">{{ $assigned->arbitrator?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To advocate</p>
                                                        <p class="case-text">{{ $assigned->advocate?->name ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To case_manager</p>
                                                        <p class="case-text">{{ $assigned->caseManager?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To mediator</p>
                                                        <p class="case-text">{{ $assigned->mediator?->name ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                        <p class="case-title">Assigned To conciliator</p>
                                                        <p class="case-text">{{ $assigned->conciliator?->name ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <small class="text-center justify-content-center">
                                                <p class="mb-0 text-danger">Wait for case assignment.</p>
                                            </small>
                                        @endif

                                        <hr>

                                        <div class="accordion" id="accordionImportantInfo">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="true" aria-controls="collapseOne">
                                                        Important Communication :
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionImportantInfo">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Notice</th>
                                                                        <th>Notice Date</th>
                                                                        <th>Summary</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                                class="text-decoration-none text-dark"
                                                                                href="#" target="_blank">
                                                                                <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                    height="30" alt="PDF File" /> File
                                                                                Name
                                                                            </a></td>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                                class="text-decoration-none text-dark"
                                                                                href="#" target="_blank">
                                                                                <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                    height="30" alt="PDF File" /> File
                                                                                Name
                                                                            </a></td>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                                class="text-decoration-none text-dark"
                                                                                href="#" target="_blank">
                                                                                <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                    height="30" alt="PDF File" /> File
                                                                                Name
                                                                            </a></td>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                                class="text-decoration-none text-dark"
                                                                                href="#" target="_blank">
                                                                                <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                    height="30" alt="PDF File" /> File
                                                                                Name
                                                                            </a></td>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionHearingDate">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                        aria-expanded="true" aria-controls="collapseTwo">
                                                        Last Hearing :
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwo" data-bs-parent="#accordionHearingDate">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Hearing Date</th>
                                                                        <th>Summary</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionNextDate">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                        aria-expanded="true" aria-controls="collapseThree">
                                                        Next Hearing :
                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse"
                                                    aria-labelledby="headingThree" data-bs-parent="#accordionNextDate">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Hearing Date</th>
                                                                        <th>Summary</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>15-04-2025</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionStage">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingFour">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                        aria-expanded="true" aria-controls="collapseFour">
                                                        Stage :
                                                    </button>
                                                </h2>
                                                <div id="collapseFour" class="accordion-collapse collapse"
                                                    aria-labelledby="headingFour" data-bs-parent="#accordionStage">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Stage Name</th>
                                                                        <th>Summary</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Service of summons on defendant</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Appearance of parties</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Examination of parties</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Argument</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Judgment</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>

                                        <div class="accordion" id="accordionIntrimOrder">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingFive">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                        aria-expanded="true" aria-controls="collapseFive">
                                                        Interim Order : If Any
                                                    </button>
                                                </h2>
                                                <div id="collapseFive" class="accordion-collapse collapse"
                                                    aria-labelledby="headingFive" data-bs-parent="#accordionIntrimOrder">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Order Type</th>
                                                                        <th>Date of Order</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Service of summons on defendant</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Appearance of parties</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Examination of parties</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Argument</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Judgment</td>
                                                                        <td>------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>

                                        <div class="accordion" id="accordionToOrganization">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingSeven">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                                        aria-expanded="true" aria-controls="collapseSeven">
                                                        Assignment Notice To Organization :
                                                    </button>
                                                </h2>
                                                <div id="collapseSeven" class="accordion-collapse collapse"
                                                    aria-labelledby="headingSeven" data-bs-parent="#accordionToOrganization">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Notice</th>
                                                                        <th>Date</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>

                                        <div class="accordion" id="accordionByOrganization">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingSix">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                        aria-expanded="true" aria-controls="collapseSix">
                                                        Notice Given By Organization :
                                                    </button>
                                                </h2>
                                                <div id="collapseSix" class="accordion-collapse collapse"
                                                    aria-labelledby="headingSix" data-bs-parent="#accordionByOrganization">
                                                    <div class="accordion-body p-0">
                                                        <div class="custom-table-container">
                                                            <table class="table custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-start ps-3">Notice</th>
                                                                        <th>Date</th>
                                                                        <th>Summary</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-3"><a
                                                                            class="text-decoration-none text-dark"
                                                                            href="#" target="_blank">
                                                                            <img src="{{ asset('public/assets/img/pdf.png') }}"
                                                                                height="30" alt="PDF File" /> File
                                                                            Name
                                                                        </a></td>
                                                                    <td>15-04-2025</td>
                                                                    <td>----------</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="accordion" id="accordionCaseDocuments">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingEight">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseEight"
                                                        aria-expanded="true" aria-controls="collapseEight">
                                                        Case Documents :
                                                    </button>
                                                </h2>
                                                <div id="collapseEight" class="accordion-collapse collapse overflow-hidden"
                                                    aria-labelledby="headingEight" data-bs-parent="#accordionCaseDocuments">
                                                    <div class="accordion-body">
                                                        <div class="custom-table-container">
                                                            <div class="row gx-5 gy-3">
                                                                @php
                                                                    $documents = [
                                                                        'application_form' => $case->application_form,
                                                                        'account_statement' => $case->account_statement,
                                                                        'foreclosure_statement' => $case->foreclosure_statement,
                                                                        'loan_agreement' => $case->loan_agreement,
                                                                        'other_document' => $case->other_document
                                                                    ];
                                                                @endphp
                                                            
                                                                @foreach ($documents as $label => $filename)
                                                                    @php
                                                                        $filePath = $filename ? asset('storage/' . $filename) : null;
                                                                        $extension = $filename ? strtolower(pathinfo($filename, PATHINFO_EXTENSION)) : null;
                                                                    @endphp
                                                            
                                                                    <div class="col-md-4 col-6">
                                                                        <p class="case-title">{{ str_replace('_', ' ', ucfirst($label)) }}</p>
                                                                        <p class="case-text">
                                                                            @if ($filePath && $extension)
                                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                                    <img src="{{ $filePath }}" class="img-thumbnail" width="100" alt="Image Preview" />
                                                                                @elseif ($extension === 'pdf')
                                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="{{ $filePath }}" target="_blank">
                                                                                        <img src="{{ asset('public/assets/img/pdf.png') }}" height="30" alt="PDF File" />
                                                                                        View PDF
                                                                                    </a>
                                                                                @elseif (in_array($extension, ['doc', 'docx']))
                                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="{{ $filePath }}" target="_blank">
                                                                                        <img src="{{ asset('public/assets/img/doc.png') }}" height="30" alt="DOC File" />
                                                                                        View Document
                                                                                    </a>
                                                                                @else
                                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="{{ $filePath }}" target="_blank">Download File</a>
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">No file uploaded</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                @endforeach

                                                                <hr>

                                                                <div class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                    <p class="case-title my-auto">Claimant Petition Filed Or Not</p>
                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="#" target="_blank">
                                                                        <img src="{{ asset('public/assets/img/pdf.png') }}" height="30" alt="PDF File" />
                                                                        View PDF
                                                                    </a>
                                                                </div>
                                                                
                                                                <hr>

                                                                <div class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                    <p class="case-title my-auto">Evidence Filed Or Not</p>
                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="#" target="_blank">
                                                                        <img src="{{ asset('public/assets/img/pdf.png') }}" height="30" alt="PDF File" />
                                                                        View PDF
                                                                    </a>
                                                                </div>

                                                                <hr>

                                                                <div class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                    <p class="case-title my-auto">Award Recieved Or Not</p>
                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="#" target="_blank">
                                                                        <img src="{{ asset('public/assets/img/pdf.png') }}" height="30" alt="PDF File" />
                                                                        View PDF
                                                                    </a>
                                                                </div>

                                                                <hr>

                                                                <div class="col-12 d-flex justify-content-between item-align-self my-0">
                                                                    <p class="case-title my-auto">Interim Order</p>
                                                                    <a class="text-decoration-none case-text" style="font-size: 13px" href="#" target="_blank">
                                                                        <img src="{{ asset('public/assets/img/pdf.png') }}" height="30" alt="PDF File" />
                                                                        View PDF
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
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
    <script type="text/javascript"></script>
@endsection
