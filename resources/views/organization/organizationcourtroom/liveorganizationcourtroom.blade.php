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
                                    <a href="{{ route('organization.organizationcourtroom.organizationcourtroomlist') }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fa fa-list me-1"></i>
                                        Court Lists
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

                            <div class="col-lg-12 col-12"> 
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                        style="background-color: black;color: white;padding: 5px;border-radius: 8px">Notice And Award Updates</h4>

                                    <div class="form-group mb-3">
                                        <label for="file_case_id" class="form-label fw-bold">Select Case</label>
                                        <select class="form-select" id="caseSelector" name="file_case_id"
                                            style="background-color: #fff2dc !important;">
                                            <option selected disabled>Select Case Number</option>
                                            @foreach ($caseData as $case)
                                                <option value="{{ $case->id }}" data-case="{{ json_encode($case) }}">
                                                    {{ $case->case_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Notices Container -->
                                    <div class="row mt-3">
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold">All Notices:</label>
                                            {{-- <h5 style="background-color: #f5f5f5; padding: 5px; border-radius: 5px;">Notices:</h5> --}}
                                            <div id="noticesContainer" style="max-height: 400px; overflow: scroll;">

                                            </div>
                                        </div>

                                    <!-- Awards Container -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold">Daily OrderSheet:</label>
                                            {{-- <h5 style="background-color: #f5f5f5; padding: 5px; border-radius: 5px;">Awards:</h5> --}}
                                            <div id="awardsContainer" style="max-height: 400px; overflow: scroll;">

                                            </div>
                                        </div>
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
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>

    {{-- ######### Gegocloud for live court room ######### --}}
    <script>
        const roomID = "{{ $roomID }}";
        const userID = "{{ $localUserID }}";
        const userName = "{{ $organization->name }}";
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

    {{-- ############# Show Notices Using Ajax ############### --}}
    <script>
       $(document).ready(function() {
            const noticeTypes = @json(config('constant.notice_type'));

            $('#caseSelector').on('change', function() {
                const caseId = $(this).val();

                // 📝 Fetch Notices
                $.ajax({
                    url: "{{ route('organization.organizationcourtroom.fetch.notices') }}",
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
                                        <img src="{{ asset('public/assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                    </a>` :
                                    `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;

                                const formattedDate = new Date(notice.notice_date).toLocaleDateString('en-GB');

                                const emailStatus = notice.email_status == 0 ? 'Unsend' :
                                    notice.email_status == 1 ? 'Send' :
                                    notice.email_status == 2 ? 'Failed' : 'Unknown';

                                const whatsappStatus = notice.whatsapp_status == 0 ? 'Unseen' :
                                    notice.whatsapp_status == 1 ? 'Seen' :
                                    notice.whatsapp_status == 2 ? 'Failed' : 'Unknown';

                                // 📝 **Appending Notice Card**
                                $('#noticesContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title mb-0">Notice Date : <small>${formattedDate}</small></h4>
                                                    <h4 class="livemeetingcard-title mb-0">Email : <small>${emailStatus}</small></h4>
                                                    <h4 class="livemeetingcard-title mb-0">WhatsApp : <small>${whatsappStatus}</small></h4>
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

                // 📝 Fetch Awards
                $.ajax({
                    url: "{{ route('organization.organizationcourtroom.fetch.awards') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        case_id: caseId
                    },
                    success: function(response) {
                        $('#awardsContainer').empty(); // Clear the container

                        if (response.length > 0) {
                            response.forEach(award => {
                                const noticeTypeLabel = noticeTypes[award.notice_type] || 'Unknown Award Type';
                                const storageBaseUrl = "{{ asset('storage') }}";
                                
                                let pdfLink = award.notice ? `
                                    <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                        href="${storageBaseUrl}/${award.notice}" target="_blank">
                                        <img src="{{ asset('public/assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                    </a>` :
                                    `<span class="text-muted" style="font-size: 13px">No PDF Available</span>`;

                                const formattedDate = new Date(award.notice_date).toLocaleDateString('en-GB');

                                $('#awardsContainer').append(`
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title mb-0">Award Date : <small>${formattedDate}</small></h4>
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
                            $('#awardsContainer').append(`<p class="text-muted mt-3">No awards found for the selected case.</p>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching awards:", error);
                    }
                });
            });
        });
    </script>
@endsection
