@extends('layouts.front')

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}
    
    <div class="container my-5">
        
        <div id="root" style="width: 100%; height: 550px; border: 1px solid #dfdfdf;border-radius:18px;overflow: hidden;"></div>

    </div>
@endsection

{{-- http://localhost/mediateway/guest/livecourtroom/INDI-1234?case_id=31 --}}

@section('js')
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
    <script>
        const roomID = "{{ $room_id }}";
        const userID = "{{ $localUserID }}";
        const userName = "{{ $guestName }}";
        const appID = {{ config('services.zegocloud.app_id') }};
        const serverSecret = "{{ config('services.zegocloud.server_secret') }}";

        // Generate Kit Token
        const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(appID, serverSecret, roomID, userID, userName);

        try {
            const zp = ZegoUIKitPrebuilt.create(kitToken);
            zp.joinRoom({
                container: document.querySelector("#root"),
                sharedLinks: [{
                    url: window.location.href
                }],
                scenario: {
                    mode: ZegoUIKitPrebuilt.VideoConference,
                },
                turnOnCameraWhenJoining: true,
                turnOnMicrophoneWhenJoining: true,
                showPreJoinView: true
            });
        } catch (e) {
            alert("Unable to access camera or microphone. Please check your device and browser permissions.");
            console.error("【ZEGOCLOUD】toggleStream/createStream failed !!", JSON.stringify(e));
        }
    </script>
@endsection
