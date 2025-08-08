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
                                <h5 class="mb-0" data-anchor="data-anchor">Conciliator Meeting Room - Live</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    <a href="javascript:void(0);" class="btn btn-outline-danger py-1" id="endMeetingRoom"
                                        data-room-id="{{ $roomID }}">
                                        <i class="fa-solid fa-rectangle-xmark"></i>
                                        End Meeting Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <div class="row gy-3">
                            <div class="col-12">
                                
                                <div class="livemeeting-card">
                                    <div class="w-100" id="root"></div>
                                </div>

                            </div>

                            <div class="col-lg-12 col-12">
                                <form id="sendnoticeForm" action="{{ route('drp.conciliatormeetingroom.savenotice') }}" method="POST">
                                    @csrf
                                    <div class="livemeeting-card h-100">
                                        <h4 class="livemeetingcard-heading text-center justify-content-center"
                                            style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                            Live Cases Hearing Activity</h4>
                                        <!-- Case Number Select -->
                                        <div class="row">
                                            <div class="col-12 form-group mb-3">
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

                            <!-- Notice Display Area -->
                            <div class="col-lg-4 col-12">
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
                            <div class="col-lg-4 col-12">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                        Daily Orders</h4>
                                    <!-- OrderSheet Display Area -->
                                    <div id="awardsContainer">

                                        {{-- Data Comes via selecting Case_id using Ajax script --}}

                                    </div>
                                </div>
                            </div>

                            <!-- Settlement Agreement Display Area -->
                            <div class="col-lg-4 col-12">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">
                                        Settlement Agreements</h4>
                                    <!-- Settlement Agreement Display Area -->
                                    <div id="settlementAgreementsContainer">

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
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
    
    {{-- ######### Gegocloud for live Meeting room ######### --}}
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
                    config: {
                        recording: {
                            isRecording: true, // Enable recording
                        }
                    }
                },
                turnOnCameraWhenJoining: true,
                turnOnMicrophoneWhenJoining: true,
                showPreJoinView: false
            });

            // const startRecording = async () => {
            //     const response = await axios.post(
            //         'https://api.zegocloud.com/v1/room/recording/start', {
            //             roomID: roomID,
            //             userID: userID,
            //             userName: userName,
            //             appID: appID,
            //         }, {
            //             headers: {
            //                 'Authorization': `Bearer ${kitToken}`,
            //                 'Content-Type': 'application/json',
            //             }
            //         }
            //     );

            //     console.log('Recording started:', response.data);
            //     return response.data.recordingID; // Save this ID for later when stopping the recording
            // };

            // // Stop Recording API Call
            // const stopRecording = async (recordingID) => {
            //     const token = generateJWT(appID, serverSecret, roomID, userID, userName);

            //     const response = await axios.post(
            //         'https://api.zegocloud.com/v1/room/recording/stop', {
            //             roomID: roomID,
            //             recordingID: recordingID,
            //         }, {
            //             headers: {
            //                 'Authorization': `Bearer ${token}`,
            //                 'Content-Type': 'application/json',
            //             }
            //         }
            //     );

            //     console.log('Recording stopped:', response.data);
            //     return response.data.recordingURL; // This is the URL of the recorded video
            // };

            // // Example usage
            // startRecording()
            //     .then((recordingID) => {
            //         // After some time (or when session ends), stop the recording
            //         setTimeout(() => {
            //             stopRecording(recordingID)
            //                 .then((recordingURL) => {
            //                     console.log('Recording URL:',
            //                     recordingURL); // The URL of the recorded video
            //                 })
            //                 .catch((error) => console.error('Error stopping recording:', error));
            //         }, 30000); // Simulate a 30-second recording
            //     })
            //     .catch((error) => console.error('Error starting recording:', error));

            // === ⚡️ Listen for recording completion event ⚡️ ===
            // zp.on('recordingCompleted', async (event) => {
            //     console.log("Recording Completed Event: ", event);

            //     // Assuming event.data.blob contains the recording
            //     const recordingBlob = event.data.blob;
            //     const formData = new FormData();
            //     formData.append('room_id', roomID);
            //     formData.append('recording', recordingBlob, `recording_${roomID}.mp4`);

            //     try {
            //         const response = await fetch("{{ route('drp.conciliatormeetingroom.saveRecording') }}", {
            //             method: "POST",
            //             headers: {
            //                 "X-CSRF-TOKEN": "{{ csrf_token() }}"
            //             },
            //             body: formData
            //         });

            //         if (response.ok) {
            //             const data = await response.json();
            //             console.log("Recording saved successfully: ", data);
            //         } else {
            //             console.error("Failed to save recording");
            //         }
            //     } catch (error) {
            //         console.error("Error saving recording: ", error);
            //     }
            // });

            // // Handle when the conference ends or the user quits the room
            // zp.on('roomWillEnd', () => {
            //     console.log("Conference ended or user is leaving the room");
            //     // Here, you can trigger any final steps or checks
            //     // e.g., ensure the recording is saved automatically before the user leaves
            // });

        } catch (e) {
            alert("Unable to access camera or microphone. Please check your device and browser permissions.");
            console.error("【ZEGOCLOUD】toggleStream/createStream failed !!", JSON.stringify(e));
        }
    </script>

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
                    url: "{{ route('drp.conciliatormeetingroom.getFlattenedCaseData', ':caseId') }}".replace(
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
                    url: "{{ route('drp.conciliatormeetingroom.fetch.notices') }}",
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
                    url: "{{ route('drp.conciliatormeetingroom.fetch.awards') }}",
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

                // Fetch settlement Agreements
                $.ajax({
                    url: "{{ route('drp.conciliatormeetingroom.fetch.settlementagreements') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function(response) {
                        $('#settlementAgreementsContainer').empty(); // Clear the container

                        if (response.length > 0) {
                            response.forEach(notice => {
                                const noticeTypeLabel = noticeTypes[notice.notice_type] || 'Unknown Settlement Agreement Type';
                                const storageBaseUrl = "{{ asset('storage') }}";
                                
                                let pdfLink = notice.notice ? `
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                        href="${storageBaseUrl}/${notice.notice}" target="_blank">
                                        <img src="{{ asset('assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                    </a>` :
                                    `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;

                                const formattedDate = new Date(notice.notice_date).toLocaleDateString('en-GB');

                                $('#settlementAgreementsContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <div class="text-center d-grid">
                                                        <h4 class="livemeetingcard-title mb-0">Settlement Agreement Date :</h4>
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
                            $('#settlementAgreementsContainer').append(`<p class="text-muted mt-3">No Settlement Agreement found for the selected case.</p>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching Settlement Agreements:", error);
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
                    url: "{{ route('drp.conciliatormeetingroom.savenotice') }}",
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

    {{-- ############# End Live Meeting Room ############### --}}
    <script>
        $(document).on('click', '#endMeetingRoom', function() {
            const roomId = $(this).data('room-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to close this Meeting Room?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Close it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('drp.conciliatormeetingroom.close') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            room_id: roomId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Closed!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Redirect with success message
                                    window.location.href =
                                        "{{ route('drp.conciliatormeetingroom.conciliatormeetingroomlist') }}?success=1";
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'Something went wrong. Please try again.',
                                'error');
                        }
                    });
                }
            });
        });

        // Display SweetAlert on page load if redirected with success message
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                Swal.fire({
                    title: 'Success',
                    text: 'Meeting Room has been closed successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
@endsection
