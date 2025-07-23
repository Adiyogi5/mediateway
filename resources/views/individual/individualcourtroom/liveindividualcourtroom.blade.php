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
                                    <a href="{{ route('individual.individualcourtroom.individualcourtroomlist') }}"
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

                            <div class="col-md-6 col-12">
                                <div class="livemeeting-card">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                    style="background-color: black;color: white;padding: 5px;border-radius: 8px">Notice Updates</h4>
                                
                                    <div id="noticesContainer" style="max-height: 400px;overflow:scroll;">
                                        @if($noticeData->isNotEmpty())
                                            @foreach ($noticeData as $notice)
                                                <div class="card mt-3 border-1 active overflow-hidden">
                                                    <div class="card-body py-2 px-md-3 px-2">
                                                        <div class="row">
                                                            <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                                <h4 class="livemeetingcard-title mb-0">
                                                                    Notice Date : <small>{{ \Carbon\Carbon::parse($notice->notice_date)->format('d-m-Y') }}</small>
                                                                </h4>
                                                                <h4 class="livemeetingcard-title mb-0">
                                                                    Email : <small>
                                                                        @if($notice->email_status == 0)
                                                                            Unsend
                                                                        @elseif($notice->email_status == 1)
                                                                            Send
                                                                        @elseif($notice->email_status == 2)
                                                                            Failed
                                                                        @else
                                                                            Unknown
                                                                        @endif
                                                                    </small>
                                                                </h4>
                                                            </div>
                                                            <div class="col">
                                                                <p class="livemeetingcard-text text-muted small d-flex justify-content-between text-center">
                                                                    {{ config('constant.notice_type')[$notice->notice_type] ?? 'Unknown Notice Type' }}
                                                                    
                                                                    @if($notice->notice)
                                                                        <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                                           href="{{ asset('storage/' . $notice->notice) }}" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted" style="font-size: 13px">No PDF Available</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mt-3">No notices found for this case.</p>
                                        @endif
                                    </div>
                                </div>                                
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="livemeeting-card">
                                    <h4 class="livemeetingcard-heading text-center justify-content-center"
                                    style="background-color: black;color: white;padding: 5px;border-radius: 8px">Daily OrderSheet</h4>
                                
                                    <div id="ordersheetContainer" style="max-height: 400px;overflow:scroll;">
                                        @if($ordersheetData->isNotEmpty())
                                            @foreach ($ordersheetData as $ordersheet)
                                                <div class="card mt-3 border-1 active overflow-hidden">
                                                    <div class="card-body py-2 px-md-3 px-2">
                                                        <div class="row">
                                                            <div class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                                <h4 class="livemeetingcard-title mb-0">
                                                                    OrderSheet Date : <small>{{ \Carbon\Carbon::parse($ordersheet->notice_date)->format('d-m-Y') }}</small>
                                                                </h4>
                                                                <h4 class="livemeetingcard-title mb-0">
                                                                    Email : <small>
                                                                        @if($ordersheet->email_status == 0)
                                                                            Unsend
                                                                        @elseif($ordersheet->email_status == 1)
                                                                            Send
                                                                        @elseif($ordersheet->email_status == 2)
                                                                            Failed
                                                                        @else
                                                                            Unknown
                                                                        @endif
                                                                    </small>
                                                                </h4>
                                                            </div>
                                                            <div class="col">
                                                                <p class="livemeetingcard-text text-muted small d-flex justify-content-between text-center">
                                                                    {{ config('constant.notice_type')[$ordersheet->notice_type] ?? 'Unknown OrderSheet Type' }}
                                                                    
                                                                    @if($ordersheet->notice)
                                                                        <a class="text-decoration-none text-secondary" style="font-size: 13px"
                                                                           href="{{ asset('storage/' . $ordersheet->notice) }}" target="_blank">
                                                                            <img src="{{ asset('assets/img/pdf.png') }}" alt="PDF File" style="width: 20px;height: 24px;" />
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted" style="font-size: 13px">No PDF Available</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mt-3">No notices found for this case.</p>
                                        @endif
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

    <script>
        const roomID = "{{ $roomID }}";
        const userID = "{{ $localUserID }}";
        const userName = "{{ $individual->name }}";
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

@endsection
