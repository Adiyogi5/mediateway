@extends('layouts.front')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">

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
                                <h5 class="mb-0" data-anchor="data-anchor">Arbitrator Draw OrderSheet Room</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="{{ route('drp.courtroom.courtroomlist') }}" class="btn btn-outline-secondary">
                                        <i class="fa fa-list me-1"></i>
                                        Court Room List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <div class="row gy-3">

                            <div class="col-lg-12 col-12">
                                <form id="sendnoticeForm" action="{{ route('drp.courtroom.savenotice') }}" method="POST">
                                    @csrf
                                    <div class="livemeeting-card h-100">
                                        <h4 class="livemeetingcard-heading text-center justify-content-center"
                                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                            Cases OrderSheet Activity</h4>
                                        <!-- Case Number Select -->
                                        <div class="row">
                                            <div class="col-md-6 col-12 form-group mb-3">
                                                <label for="file_case_id" class="form-label fw-bold">Select Case</label>
                                                <select class="form-select" id="caseSelector" name="file_case_id"
                                                    style="background-color: #fff2dc !important;">
                                                    <option selected disabled>Select Case Number</option>
                                                    @foreach ($caseData as $case)
                                                        <option value="{{ $case->id }}"
                                                            data-final_hearing_date="{{ $case->final_hearing_date }}">
                                                            {{ $case->case_number }} / {{ $case->loan_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {{-- data-case="{{ json_encode($case) }}" --}}
                                            </div>
                                        </div>
                                        
                                        <!-- Document Type and File Upload -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Document Type and Attach</label>
                                            <div class="row g-2">
                                                <div class="col-xl-6 col-12">
                                                    <select class="form-select" id="docType" name="docType"
                                                        style="background-color: #fff2dc !important;">
                                                        <option selected disabled>Document Type</option>
                                                        <option value="ordersheet">Case OrderSheet</option>
                                                        {{-- <option value="settlementletter">Settlement Agreement</option> --}}
                                                    </select>
                                                </div>
                                                <div class="col-xl-6 col-12">
                                                    <select class="form-select" id="tempType" name="tempType"
                                                        style="background-color: #fff2dc !important;">
                                                        <option selected disabled>Template Type</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <textarea class="form-control" rows="5" id="livemeetingdata" name="livemeetingdata">{{ old('livemeetingdata') }}</textarea>
                                            @error('livemeetingdata')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Upload Button -->
                                        <div class="text-center mt-3">
                                            <button class="btn btn-secondary w-100" id="uploadBtn" type="submit">UPLOAD /
                                                SAVE</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Notice Display Area -->
                            <div class="col-lg-6 col-12">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                        Notice Updates</h4>
                                    <!-- Notice Display Area -->
                                    <div id="noticesContainer">

                                        {{-- Data Comes via selecting Case_id using Ajax script --}}

                                    </div>
                                </div>
                            </div>

                            <!-- OrderSheet Display Area -->
                            <div class="col-lg-6 col-12">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                        Daily OrderSheet</h4>
                                    <!-- OrderSheet Display Area -->
                                    <div id="awardsContainer">

                                        {{-- Data Comes via selecting Case_id using Ajax script --}}

                                    </div>
                                </div>
                            </div>

                             <!-- Hearing Data Display Area -->
                            <div class="col-12">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                        Hearing Updates</h4>
                                    <!-- OrderSheet Display Area -->
                                    <div id="hearingsContainer">

                                        {{-- Data Comes via selecting Case_id using Ajax script --}}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Include in your <head> or before </body> -->
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>

    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>

    <script>
        $('#caseSelector').select2({
            placeholder: "Select Case / Loan Number",
            allowClear: true,
            width: '100%'
        });
    </script>

    {{-- ####### Fetch the flattened data dynamically #######
         ####### Replace placeholders in the template ####### --}}
    <script type="text/javascript">
        const allTemplates = {
            ordersheet: @json($orderSheetTemplates),
            settlementletter: @json($settlementLetterTemplates),
        };
        let flattenedCaseData = @json($flattenedCaseData); // Make sure this is correct

        $(document).ready(function() {
            $('#livemeetingdata').summernote({
                height: 350,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['paragraph']],
                ]
            });

            // Handle Case Selector Change
            $('#caseSelector').on('change', function() {
                const caseId = $(this).val();
                // Reset `docType` and `tempType` to their default options
                $('#docType').prop('selectedIndex', 0); // Reset to "Document Type"
                $('#tempType').html('<option selected disabled>Template Type</option>'); // Clear and reset
                // Fetch the flattened data dynamically
                $.ajax({
                    url: "{{ route('drp.courtroom.getFlattenedCaseData', ':caseId') }}".replace(
                        ':caseId', caseId),
                    method: 'GET',
                    success: function(data) {
                        console.log("Flattened Data:", data);
                        flattenedCaseData = data; // Update the global flattenedCaseData
                    },
                    error: function(error) {
                        console.error("Error fetching case data:", error);
                    }
                });
            });

            $('#docType').on('change', function() {
                const docType = $(this).val();
                const templates = allTemplates[docType] || [];

                let options = '<option selected disabled>Template Type</option>';
                templates.forEach(template => {
                    const format = encodeURIComponent(template.notice_format || '');
                    options +=
                        `<option value="${template.id}" data-format="${format}">${template.name}</option>`;
                });

                $('#tempType').html(options);
            });

            $('#tempType').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const rawFormat = decodeURIComponent(selectedOption.data('format') || '');

                try {
                    if (!flattenedCaseData) {
                        console.error("flattenedCaseData is not available");
                        return;
                    }

                    // Replace placeholders in the template
                    const content = rawFormat.replace(/\{\{([\s\S]+?)\}\}/g, function(match, key) {
                        // Create a temporary element to strip all HTML formatting
                        const temp = document.createElement('div');
                        temp.innerHTML = key;

                        // Get plain text content (removes bold, font, size, etc.)
                        let plainKey = temp.textContent || temp.innerText || '';

                        // Normalize: convert to lowercase, trim, replace spaces/special characters
                        const normalizedKey = plainKey
                            .replace(/&nbsp;/gi, ' ') // decode HTML spaces
                            .replace(/\s+/g, ' ') // collapse multiple spaces
                            .trim() // trim edges
                            .toLowerCase() // lowercase for matching
                            .replace(/[^a-z0-9]/g, '_'); // remove non-alphanumeric, use _

                        const value = flattenedCaseData[normalizedKey];

                        if (value !== undefined && value !== null) {
                            return value;
                        } else {
                            console.warn(`Placeholder {${plainKey}} is missing a value.`);
                            return match; // leave original placeholder
                        }
                    });

                    $('#livemeetingdata').summernote('code', content);

                } catch (err) {
                    console.error("Template parse error:", err);
                }
            });
        });
    </script>

    {{-- ############# Show Notices Using Ajax ############### --}}
    <script>
        function getStatusLabel(status, type = 'general') {
            let label = '';
            let className = '';

            switch (status) {
                case 0:
                    label = 'Pending';
                    className = 'text-warning';
                    break;
                case 1:
                    label = 'Sent';
                    className = 'text-success';
                    break;
                case 2:
                    label = 'Failed';
                    className = 'text-danger';
                    break;
                default:
                    label = 'Unknown';
                    className = 'text-danger';
                    break;
            }

            return `<small class="${className}">${label}</small>`;
        }
    </script>
    <script>
        $(document).ready(function() {
            const noticeTypes = @json(config('constant.notice_type'));

            $('#caseSelector').on('change', function() {
                const caseId = $(this).val();

                // Fetch Notices
                $.ajax({
                    url: "{{ route('drp.courtroom.fetch.notices') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function(response) {
                        $('#noticesContainer').empty(); // Clear the container

                        if (response.length > 0) {
                            response.forEach(notice => {
                                const noticeTypeLabel = noticeTypes[notice.notice_type] || 'Unknown Notice Type';
                                const storageBaseUrl = "{{ asset('storage') }}";

                                let pdfLink = notice.notice ? `
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                        href="${storageBaseUrl}/${notice.notice}" target="_blank">
                                        <img src="{{ asset('assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                    </a>` :
                                    `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;

                                const formattedDate = new Date(notice.notice_date).toLocaleDateString('en-GB');

                                const smsStatus = notice.sms_status == 0 ? 'Pending' :
                                    notice.sms_status == 1 ? 'Sent' :
                                    notice.sms_status == 2 ? 'Failed' : 'Unknown';

                                const whatsappStatus = notice.whatsapp_notice_status == 0 ? 'Pending' :
                                    notice.whatsapp_notice_status == 1 ? 'Sent' :
                                    notice.whatsapp_notice_status == 2 ? 'Failed' : 'Unknown';

                                const emailStatus = notice.email_status == 0 ? 'Pending' :
                                    notice.email_status == 1 ? 'Sent' :
                                    notice.email_status == 2 ? 'Failed' : 'Unknown';

                                $('#noticesContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Notice Date :</h4>
                                                        <small>${formattedDate}</small>
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">SMS :</h4>
                                                        ${getStatusLabel(notice.sms_status)}
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Whatsapp :</h4>
                                                        ${getStatusLabel(notice.whatsapp_notice_status)}
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Email :</h4>
                                                        ${getStatusLabel(notice.email_status)}
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small d-flex justify-content-between text-center">
                                                        ${noticeTypeLabel}
                                                        ${pdfLink}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            });
                        } else {
                            $('#noticesContainer').append(`<p class="text-muted mt-3">No notices found for the selected case.</p>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching notices:", error);
                    }
                });

                // Fetch Awards
                $.ajax({
                    url: "{{ route('drp.courtroom.fetch.awards') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function(response) {
                        $('#awardsContainer').empty(); // Clear the container

                        if (response.length > 0) {
                            response.forEach(notice => {
                                const noticeTypeLabel = noticeTypes[notice.notice_type] || 'Unknown Award Type';
                                const storageBaseUrl = "{{ asset('storage') }}";
                                
                                let pdfLink = notice.notice ? `
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                        href="${storageBaseUrl}/${notice.notice}" target="_blank">
                                        <img src="{{ asset('assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                    </a>` :
                                    `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;

                                const formattedDate = new Date(notice.notice_date).toLocaleDateString('en-GB');

                                $('#awardsContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Award Date :</h4>
                                                        <small>${formattedDate}</small>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small d-flex justify-content-between text-center">
                                                        ${noticeTypeLabel}
                                                        ${pdfLink}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            });
                        } else {
                            $('#awardsContainer').append(`<p class="text-muted mt-3">No OrderSheet found for the selected case.</p>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching OrderSheet:", error);
                    }
                });

                // Fetch Hearings
                // Hearing type mapping
                const hearingTypesMap = {
                    1: "First Hearing",
                    2: "Second Hearing",
                    3: "Final Hearing"
                };
                $.ajax({
                    url: "{{ route('drp.courtroom.fetch.hearings') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function(response) {
                        $('#hearingsContainer').empty(); // Clear the container

                        if (response.length > 0) {
                            response.forEach(hearing => {
                                const formattedDate = new Date(hearing.date).toLocaleDateString('en-GB');
                                const formattedTime = hearing.time ? hearing.time : '-';
                                const hearingType = hearingTypesMap[hearing.hearing_type] || 'Unknown Hearing';

                                $('#hearingsContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Hearing Type :</h4>
                                                        <small>${hearingType}</small>
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Hearing Date :</h4>
                                                        <small>${formattedDate}</small>
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Time :</h4>
                                                        <small>${formattedTime}</small>
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">SMS :</h4>
                                                        ${getStatusLabel(hearing.sms_status)}
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Whatsapp :</h4>
                                                        ${getStatusLabel(hearing.whatsapp_status)}
                                                    </div>
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Email :</h4>
                                                        ${getStatusLabel(hearing.email_status)}
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small text-start">
                                                        ${hearing.link}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            });
                        } else {
                            $('#hearingsContainer').append(`<p class="text-muted mt-3">No hearing data found for the selected case.</p>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching hearing data:", error);
                    }
                });
            });
        });
    </script>

    {{-- ############# validation form ############### --}}
    <script type="text/javascript">
        $("#sendnoticeForm").validate({
            rules: {
                docType: {
                    required: true,
                },
                tempType: {
                    required: true,
                },
            },
            messages: {
                docType: {
                    required: "Please select Document Type",
                },
                tempType: {
                    required: "Please select Template Type",
                },
            },
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>

    {{-- ############# form Submission using ajax ############### --}}
    <script>
        $(document).ready(function() {
            $('#sendnoticeForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                let formData = new FormData(this);
                $('#uploadBtn').prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('drp.courtroom.savenotice') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#uploadBtn').prop('disabled', false).text('UPLOAD / SAVE');

                        if (response.success) {
                            toastr.success('Notice/OrderSheet saved successfully.');
                            $('#sendnoticeForm')[0].reset();
                            $('#tempType').empty(); // Clear template type options
                        } else {
                            toastr.error(response.message || 'Notice/OrderSheet could not be saved.');
                        }
                    },
                    error: function(xhr) {
                        $('#uploadBtn').prop('disabled', false).text('UPLOAD / SAVE');
                        
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value); // Show each error in Toastr
                            });
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
