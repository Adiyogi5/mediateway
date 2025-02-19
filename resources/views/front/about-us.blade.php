@extends('layouts.front')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/flexslider/flexslider.css') }}">
    <style>
        .tab-content-container .tab-content {
            display: none;
        }

        .tab-content-container .tab-content.active {
            display: block;
        }

        .tab {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container about-us">
        
        {!! $content->description !!}
        
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Function to handle tab clicks
            function handleTabClick($tab) {
                var targetTab = $tab.data('target');

                $('.tab-content').removeClass('active');
                $('#' + targetTab).addClass('active');

                $('.tab').removeClass('bg-theme text-white').addClass('bg-light-theme text-first');

                $tab.removeClass('bg-light-theme text-first').addClass('bg-theme text-white');

                $('.tab-link').removeClass('text-white').addClass('text-first');

                $tab.find('.tab-link').removeClass('text-first').addClass('text-white');
            }

            $('.tab').on('click', function() {
                handleTabClick($(this));
            });
        });
    </script>
@endsection
