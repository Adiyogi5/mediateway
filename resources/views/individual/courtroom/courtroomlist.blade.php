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
                                <h5 class="mb-0" data-anchor="data-anchor">Court Room Lists</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <ul class="nav nav-tabs justify-content-center text-center" role="tablist">
                            <li class="nav-item w-50">
                                <a href="#info" role="tab" data-bs-toggle="tab" class="nav-link active"> Upcoming </a>
                            </li>
                            <li class="nav-item w-50">
                                <a href="#ratings" role="tab" data-bs-toggle="tab" class="nav-link"> Closed </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" role="tabpanel" id="info">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Emilia Kollette</div>
                                                </td>
                                                <td class="pt-3 mt-1">25 April 2025</td>
                                                <td class="pt-3">11:00 AM</td>
                                                <td class="pt-3"><span class="fa fa-check pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-video btn bg-success text-white text-capitalize fs-6"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/3765114/pexels-photo-3765114.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Anny Adams</div>
                                                </td>
                                                <td class="pt-3">26 April 2025</td>
                                                <td class="pt-3">11:00 AM</td>
                                                <td class="pt-3"><span class="fa fa-check pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-video btn bg-secondary text-white"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Arnold Linn</div>
                                                </td>
                                                <td class="pt-3">26 April 2025</td>
                                                <td class="pt-3">02:00 PM</td>
                                                <td class="pt-3"><span class="fa fa-check pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-video btn bg-secondary text-white"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Josh Limosel</div>
                                                </td>
                                                <td class="pt-3">26 April 2025</td>
                                                <td class="pt-3">04:00 PM</td>
                                                <td class="pt-3"><span class="fa fa-check pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-video btn bg-secondary text-white"></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" role="tabpanel" id="ratings">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Emilia Kollette</div>
                                                </td>
                                                <td class="pt-3 mt-1">25 Sep 2020</td>
                                                <td class="pt-3">11:00 AM</td>
                                                <td class="pt-3"><span class="fa fa-close pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-handshake btn bg-secondary text-white"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/3765114/pexels-photo-3765114.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Anny Adams</div>
                                                </td>
                                                <td class="pt-3">26 Sep 2020</td>
                                                <td class="pt-3">11:00 AM</td>
                                                <td class="pt-3"><span class="fa fa-close pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-handshake btn bg-secondary text-white"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Arnold Linn</div>
                                                </td>
                                                <td class="pt-3">26 Sep 2020</td>
                                                <td class="pt-3">02:00 PM</td>
                                                <td class="pt-3"><span class="fa fa-close pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-handshake btn bg-secondary text-white"></button></td>
                                            </tr>
                                            <tr id="spacing-row">
                                                <td></td>
                                            </tr>
                                            <tr class="bg-blue">
                                                <td class="pt-2"> <img
                                                        src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                                        class="rounded-circle" alt="">
                                                    <div class="pl-lg-5 pl-md-3 pl-1 name">Josh Limosel</div>
                                                </td>
                                                <td class="pt-3">26 Sep 2020</td>
                                                <td class="pt-3">04:00 PM</td>
                                                <td class="pt-3"><span class="fa fa-close pl-3"></span></td>
                                                <td class="pt-3"><button
                                                        class="fa fa-handshake btn bg-secondary text-white"></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
     
    </script>
@endsection
