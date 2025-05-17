@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                <div class="card mb-3 card-inner">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Assign Case :: Assign </h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    @if (Helper::userCan(111, 'can_edit'))
                                        <a href="{{ route('drp.caseassign') }}" class="btn btn-outline-secondary">
                                            <i class="fa fa-list me-1"></i>
                                            Assign Case List
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-6 mb-3">
                            <ul class="list-group">
                                <li class="list-group-item bg-secondary">
                                    <strong>Case Details</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Case File By :</strong>
                                    @php
                                        $userType = $caseData['user_type'] == 1 ? 'Individual' : 'Organization';
                                    @endphp

                                    <span">{{ $userType }}</span>
                                </li>

                                @if (!empty($caseData['individual_id']))
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong>Individual Name :</strong>
                                        <span>{{ $caseData->individual?->name ?? 'N/A' }}</span>
                                    </li>
                                @endif

                                @if (!empty($caseData['organization_id']))
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong>Organization Name :</strong>
                                        <span>{{ $caseData->organization?->name ?? 'N/A' }}</span>
                                    </li>
                                @endif

                                @php
                                    $caseType = config('constant.case_type')[$caseData['case_type']] ?? null;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Case Type :</strong>
                                    <span>{{ $caseType ?? 'N/A' }}</span>
                                </li>

                                @php
                                    $langType = config('constant.language')[$caseData['language']] ?? null;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Language :</strong>
                                    <span class="">{{ $langType ?? 'N/A' }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Brief of Case :</strong>
                                    <span class="">{{ $caseData['brief_of_case'] }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Amount in Dispute :</strong>
                                    <span class="">{{ $caseData['amount_in_dispute'] }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Case File Date :</strong>
                                    <span class="">{{ $caseData['created_at']->format('d M, Y') }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Status :</strong>
                                    @php
                                        $status = $caseData['status'] == 1 ? 'Active' : 'Inactive';
                                    @endphp

                                    <span class="{{ $caseData['status'] == 1 ? 'text-success' : 'text-danger' }}">
                                        {{ $status }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <ul class="list-group">
                                <li class="list-group-item bg-secondary">
                                    <strong>Claimant and Respondent Details</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Claimant Name :</strong>
                                    <span class="">
                                        {{ $caseData['claimant_first_name'] }} {{ $caseData['claimant_middle_name'] }}
                                        {{ $caseData['claimant_last_name'] }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Claimant Mobile :</strong>
                                    <span class="">{{ $caseData['claimant_mobile'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Claimant Email :</strong>
                                    <span class="">{{ $caseData['claimant_email'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Claimant Address :</strong>
                                    @php
                                        $addressTypeClaimant =
                                            $caseData['claimant_address_type'] == 1 ? 'Home' : 'Office';
                                    @endphp
                                    <span class="">
                                        {{ $addressTypeClaimant }}: {{ $caseData['claimant_address1'] }},
                                        {{ $caseData['claimant_address2'] }}, {{ $caseData['claimant_pincode'] }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Respondent Name :</strong>
                                    <span class="">
                                        {{ $caseData['respondent_first_name'] }} {{ $caseData['respondent_middle_name'] }}
                                        {{ $caseData['respondent_last_name'] }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Respondent Mobile :</strong>
                                    <span class="">{{ $caseData['respondent_mobile'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Respondent Email :</strong>
                                    <span class="">{{ $caseData['respondent_email'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Respondent Address :</strong>
                                    @php
                                        $addressTypeRespondent =
                                            $caseData['respondent_address_type'] == 1 ? 'Home' : 'Office';
                                    @endphp
                                    <span class="">
                                        {{ $addressTypeRespondent }} :{{ $caseData['respondent_address1'] }},
                                        {{ $caseData['respondent_address2'] }}, {{ $caseData['respondent_pincode'] }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12 col-12 mb-3">
                            <ul class="list-group">
                                @php
                                    $documents = [
                                        'application_form' => 'Application Form',
                                        'account_statement' => 'Account Statement',
                                        'foreclosure_statement' => 'Foreclosure Statement',
                                        'loan_agreement' => 'Loan Agreement',
                                        'other_document' => 'Other Document',
                                    ];
                                @endphp

                                @foreach ($documents as $field => $label)
                                    @php
                                        $evidence = $caseData[$field] ?? null;
                                        $extension = $evidence ? pathinfo($evidence, PATHINFO_EXTENSION) : null;
                                    @endphp

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <strong>{{ $label }}:</strong>
                                        <span>
                                            @if ($evidence)
                                                @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/' . $evidence) }}"
                                                        alt="{{ $label }}" width="100" class="rounded">
                                                @elseif($extension === 'pdf')
                                                    <a href="{{ asset('storage/' . $evidence) }}" target="_blank"
                                                        class="btn btn-primary py-1 px-3 btn-sm">View PDF</a>
                                                @elseif(in_array($extension, ['doc', 'docx']))
                                                    <a href="{{ asset('storage/' . $evidence) }}" target="_blank"
                                                        class="btn btn-secondary py-1 px-3 btn-sm">View Document</a>
                                                @else
                                                    <a href="{{ asset('storage/' . $evidence) }}" target="_blank"
                                                        class="btn btn-dark py-1 px-3 btn-sm">Download</a>
                                                @endif
                                            @else
                                                <span class="text-muted">No Attachment</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <form class="row mx-auto px-auto border-top border-1 mt-4 py-4" id="assignCase" method="POST"
                            action="{{ route('drp.caseassign.updateassigndetail', $caseData->id) }}"
                            enctype='multipart/form-data'
                            style="background-color: #e1aeff21; border-color: #7d30cb !important;">
                            @csrf
                            @method('PUT')
                            <div class="col-12 text-center justify-content-center bg-secondary pt-2">
                                <h5 class=" text-white">Assign Case</h5>
                            </div>
                            {{-- <div class="col-lg-6 mt-2">
                                <label class="form-label" for="arbitrator_id">Arbitrator</label>
                                <select name="arbitrator_id" class="form-select" id="arbitrator_id">
                                    <option value="">Select Arbitrator</option>
                                    @foreach ($arbitrators as $arbitrator)
                                        <option value="{{ $arbitrator->id }}" @selected(old('arbitrator_id', $assignCase?->arbitrator_id) == $arbitrator->id)>
                                            {{ $arbitrator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('arbitrator_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}
                            @php
                                $selectedArbitrators = explode(',', old('arbitrator_id', $assignCase?->arbitrator_id ?? ''));
                            @endphp
                            
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="arbitrator_id">Arbitrator</label>
                                <select name="arbitrator_id[]" class="form-select select2 w-100" id="arbitrator_id" multiple>
                                    @foreach ($arbitrators as $arbitrator)
                                        <option value="{{ $arbitrator->id }}"
                                            @if (in_array($arbitrator->id, $selectedArbitrators)) selected @endif>
                                            {{ $arbitrator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('arbitrator_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>     
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="advocate_id">Advocate</label>
                                <select name="advocate_id" class="form-select" id="advocate_id">
                                    <option value="">Select Advocate</option>
                                    @foreach ($advocates as $advocate)
                                        <option value="{{ $advocate->id }}" @selected(old('advocate_id', $assignCase?->advocate_id) == $advocate->id)>
                                            {{ $advocate->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('advocate_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- <div class="col-lg-6 mt-2">
                                <label class="form-label" for="case_manager_id">Case Manager</label>
                                <select name="case_manager_id" class="form-select" id="case_manager_id">
                                    <option value="">Select Case Manager</option>
                                    @foreach ($casemanagers as $casemanager)
                                        <option value="{{ $casemanager->id }}" @selected(old('case_manager_id', $assignCase?->case_manager_id) == $casemanager->id)>
                                            {{ $casemanager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('case_manager_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> --}}
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="mediator_id">Mediator</label>
                                <select name="mediator_id" class="form-select" id="mediator_id">
                                    <option value="">Select Mediator</option>
                                    @foreach ($mediators as $mediator)
                                        <option value="{{ $mediator->id }}" @selected(old('mediator_id', $assignCase?->mediator_id) == $mediator->id)>
                                            {{ $mediator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mediator_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-lg-6 mt-2">
                                <label class="form-label" for="conciliator_id">Conciliator</label>
                                <select name="conciliator_id" class="form-select" id="conciliator_id">
                                    <option value="">Select Conciliator</option>
                                    @foreach ($conciliators as $conciliator)
                                        <option value="{{ $conciliator->id }}" @selected(old('conciliator_id', $assignCase?->conciliator_id) == $conciliator->id)>
                                            {{ $conciliator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('conciliator_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- <div class="col-lg-6 mt-2">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" class="form-select" id="status">
                        <option value="1" @selected(old('status', $assignCase?->status) == 1)> Active </option>
                        <option value="0" @selected(old('status', $assignCase?->status) == 0)> Inactive </option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div> --}}

                            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                                <button class="btn btn-primary submitbtn" type="submit">Assign</button>
                            </div>
                        </form>
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
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Arbitrator(s)"
            });
        });
    </script>
    <script type="text/javascript">
        $("#assignCase").validate({
            rules: {
                arbitrator_id: {
                    required: true,
                },
                advocate_id: {
                    required: true,
                },
                // case_manager_id: {
                //     required: true,
                // },
                mediator_id: {
                    required: true,
                },
                conciliator_id: {
                    required: true,
                },
            },
            messages: {
                arbitrator_id: {
                    required: "Please Select Arbitrator",
                },
                advocate_id: {
                    required: "Please Select Advocate",
                },
                // case_manager_id: {
                //     required: "Please Select Case Manager",
                // },
                mediator_id: {
                    required: "Please Select Mediator",
                },
                conciliator_id: {
                    required: "Please Select Conciliator",
                },
            },
        });
    </script>
@endsection
