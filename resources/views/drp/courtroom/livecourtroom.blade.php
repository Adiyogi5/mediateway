@extends('layouts.front')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
<style type="text/css">
    #local-video,
    #remote-video {
        width: 100%;
        height: 600px;
        border: 1px solid #dfdfdf;
    }

    #local-video {
        position: relative;
        margin: 0 auto;
        display: block;
    }

    #remote-video {
        display: flex;
        margin: auto;
        position: relative !important;
    }
</style>
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
                                <h5 class="mb-0" data-anchor="data-anchor">Court Room - Live</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0);" class="btn btn-outline-danger py-1" id="endCourtRoom" data-room-id="{{ $roomID }}">
                                        <i class="fa-solid fa-rectangle-xmark"></i>
                                        End Court Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <div class="row gy-3">
                            <div class="col-12 mb-3">

                                <div class="livemeeting-card">
                                    <div class="w-100" id="root"></div>
                                </div>

                            </div>

                            <div class="col-lg-4 col-12 order-lg-1 order-2">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">Hearing
                                        Updates</h4>
                                    <!-- Notice Display Area -->
                                    <div id="noticesContainer">

                                        {{-- Data Comes via selecting Case_id using Ajax script --}}

                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-12 order-lg-2 order-1">
                                <form id="sendnoticeForm" action="{{ route('drp.courtroom.savenotice') }}" method="POST">
                                    @csrf
                                    <div class="livemeeting-card h-100">
                                        <h4 class="livemeetingcard-heading text-center justify-content-center"
                                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                            Case OrderSheets / Settlement Agreements</h4>
                                        <!-- Case Number Select -->
                                        <div class="form-group mb-3">
                                            <label for="file_case_id" class="form-label fw-bold">Select Case</label>
                                            <select class="form-select" id="caseSelector" name="file_case_id"
                                                style="background-color: #fff2dc !important;">
                                                <option selected disabled>Select Case Number</option>
                                                @foreach ($caseData as $case)
                                                    <option value="{{ $case->id }}"
                                                        data-case="{{ json_encode($case) }}">
                                                        {{ $case->case_number }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                        <option value="settlementletter">Settlement Agreement</option>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
    {{-- ######### Gegocloud for live court room ######### --}}
    <script>
        const roomID = "{{ $roomID }}";
        const userID = "{{ $localUserID }}";
        const userName = "{{ $drp->name }}";
        const appID = {{ config('services.zegocloud.app_id') }};
        const serverSecret = "{{ config('services.zegocloud.server_secret') }}";

        // Generate a Kit Token using test method (ONLY for dev, not production)
        const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(appID, serverSecret, roomID, userID, userName);

        try {
            const zp = ZegoUIKitPrebuilt.create(kitToken);
            zp.joinRoom({
                container: document.querySelector("#root"),
                sharedLinks: [{
                    url: window.location.protocol + '//' + window.location.host + window.location.pathname +
                        '?roomID=' + roomID,
                }],
                scenario: {
                    mode: ZegoUIKitPrebuilt.VideoConference,
                },
                turnOnCameraWhenJoining: true,
                turnOnMicrophoneWhenJoining: true,
                showPreJoinView: false
            });
        } catch (e) {
            alert("Unable to access camera or microphone. Please check your device and browser permissions.");
            console.error("【ZEGOCLOUD】toggleStream/createStream failed !!", JSON.stringify(e));
        }
    </script>

    {{-- ####### Fetch the flattened data dynamically #######
         ####### Replace placeholders in the template #######--}}
    <script type="text/javascript">
        const allTemplates = {
            ordersheet: @json($orderSheetTemplates),
            settlementletter: @json($settlementLetterTemplates),
        };
        let flattenedCaseData = @json($flattenedCaseData); // Make sure this is correct

        $(document).ready(function() {
            $('#livemeetingdata').summernote({
                height: 200,
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
        $(document).ready(function () {
            const noticeTypes = @json(config('constant.notice_type'));
    
            $('#caseSelector').on('change', function () {
                const caseId = $(this).val();
                
                $.ajax({
                    url: "{{ route('drp.courtroom.fetch.notices') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function (response) {
                        $('#noticesContainer').empty(); // Clear the container
    
                        if (response.length > 0) {
                            response.forEach(notice => {
                                // Get the notice type label from the preloaded object
                                const noticeTypeLabel = noticeTypes[notice.notice_type] || 'Unknown Notice Type';
    
                                // Check if PDF file exists
                                let pdfLink = '';
                                if (notice.notice) {
                                    pdfLink = `<a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                    href="/storage/${notice.notice}" target="_blank">
                                                    <img src="{{ asset('public/assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                                </a>`;
                                } else {
                                    pdfLink = `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;
                                }

                                // Format the date to d-m-Y format
                                const formattedDate = new Date(notice.notice_date).toLocaleDateString('en-GB');

                                // Map email status to readable text
                                const emailStatus = notice.email_status == 0 ? 'Unsend' 
                                                : notice.email_status == 1 ? 'Send' 
                                                : notice.email_status == 2 ? 'Failed' 
                                                : 'Unknown';
    
                                // Append notice card
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
                                                        <h4 class="livemeetingcard-title mb-0">Email :</h4>
                                                        <small>${emailStatus}</small>
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
                            $('#noticesContainer').append(`
                                <p class="text-muted mt-3">No notices found for the selected case.</p>
                            `);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching notices:", error);
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

    {{-- ############# End Live Court Room ############### --}}
    <script>
        $(document).on('click', '#endCourtRoom', function () {
            const roomId = $(this).data('room-id');
    
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to close this Court Room?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Close it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('drp.courtroom.close') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            room_id: roomId
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Closed!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Redirect with success message
                                    window.location.href = "{{ route('drp.courtroom.courtroomlist') }}?success=1";
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                }
            });
        });
    
        // Display SweetAlert on page load if redirected with success message
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                Swal.fire({
                    title: 'Success',
                    text: 'Court Room has been closed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
@endsection
