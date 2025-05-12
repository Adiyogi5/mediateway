@php
    $guards = ['individual', 'organization', 'drp'];
    $currentGuard = null;

    foreach ($guards as $guard) {
        if (auth($guard)->check()) {
            $currentGuard = $guard;
            break;
        }
    }
@endphp

@php
    $user = auth($currentGuard)->user();
    $userImage = $user && !empty($user->image) ? basename($user->image) : null;
    $defaultImage = asset('assets/img/dummy-user.png');

    // Ensure correct image selection
    if (!$userImage || $userImage === 'img-not-found.png') {
        $profileImage = $defaultImage;
    } else {
        $profileImage = asset('storage/' . $user->image);
    }
@endphp

<div
    class="offcanvas-header bg-dark px-xl-3 px-lg-2 px-1 text-lg-start text-md-center text-start justify-content-lg-start justify-content-md-center justify-content-start py-2 rounded">
    {{-- <img src="{{ basename(auth($currentGuard)->user()->image) == 'img-not-found.png' ? asset('assets/img/dummy-user.png') : asset('storage/' . auth($currentGuard)->user()->image) }}" class="d-block profile-img" alt="..."> --}}
    <img src="{{ $profileImage }}" class="d-block profile-img mx-auto" alt="User Profile">
    <div class="w-100">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
            {{ auth($currentGuard)->user()->name }}</h5>
        <p class="sidebar-p">{{ auth($currentGuard)->user()->email }}</p>
        <p class="sidebar-p">{{ auth($currentGuard)->user()->mobile }}</p>
    </div>
    <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse"
        class="ms-auto text-white d-md-none d-block text-center justify-content-center border bg-lime rounded-3 p-1 text-decoration-none">
        <i class="fa fa-list fa-lg py-2 p-1 text-white"></i></a>
</div>

<div class="collapse collapse-vertical show" id="sidebar">
    <div class="offcanvas-body mt-xl-3 mt-2" id="sidebar-nav">
        <div class="sidebar-content">
            <ul class="navbar-nav justify-content-start flex-grow-1 border bg-white" style="overflow:hidden;">
                <li
                    class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.dashboard') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.dashboard') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.dashboard') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.dashboard') }}"> <i class="fa-solid fa-grip faa-profile"></i>
                        Dashboard</a>
                </li>

                <li
                    class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.profile') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.profile') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.profile') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'profile') }}"><i
                            class="fa-solid fa-user faa-profile"></i> Profile</a>
                </li>

                {{-- #################### Individual #################### --}}
                {{-- ####################################################### --}}
                @if (auth('individual')->check())
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'case.filecaseview') }}"><i
                                class="fa-solid fa-cash-register faa-profile"></i> File A Case</a>
                    </li>
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'courtroom.courtroomlist') }}"><i
                                class="fa-solid fa-video faa-profile"></i> Court Room</a>
                    </li>
                @endif

                {{-- #################### Organization #################### --}}
                {{-- ####################################################### --}}
                @if (auth('organization')->check())
                    @if (Helper::organizationCan(203))
                        <li
                            class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.staffroles') ? 'text-white bg-lime' : '' }}">
                            <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.staffroles') ? 'text-white bg-lime' : '' }}"
                                aria-current="page"
                                style="{{ request()->routeIs($currentGuard . '.staffroles') ? 'color:white !important;;' : '' }}"
                                href="{{ route($currentGuard . '.' . 'staffroles') }}"><i
                                    class="fa-brands fa-square-web-awesome-stroke faa-profile"></i> Staff Roles</a>
                        </li>
                    @endif
                    @if (Helper::organizationCan(204))
                        <li
                            class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.staffs') ? 'text-white bg-lime' : '' }}">
                            <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.staffs') ? 'text-white bg-lime' : '' }}"
                                aria-current="page"
                                style="{{ request()->routeIs($currentGuard . '.staffs') ? 'color:white !important;;' : '' }}"
                                href="{{ route($currentGuard . '.' . 'staffs') }}"><i
                                    class="fa-solid fa-users faa-profile"></i> Staffs</a>
                        </li>
                    @endif
                    @if (Helper::organizationCan(205))
                        <li
                            class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'text-white bg-lime' : '' }}">
                            <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'text-white bg-lime' : '' }}"
                                aria-current="page"
                                style="{{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'color:white !important;;' : '' }}"
                                href="{{ route($currentGuard . '.' . 'cases.filecaseview') }}"><i
                                    class="fa-solid fa-cash-register faa-profile"></i> File Cases</a>
                        </li>
                    @endif
                    @if (Helper::organizationCan(206))
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'courtroom.courtroomlist') }}"><i
                                class="fa-solid fa-video faa-profile"></i> Court Room</a>
                    </li>
                    @endif
                @endif

                {{-- ################ DRP TYPE = 1,5 Arbitrator, Conciliator ################# --}}
                {{-- ############################# ORDER SHEET ############################### --}}
                @if (auth('drp')->check() && in_array(auth('drp')->user()->drp_type, [1, 5]))
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.ordersheet') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.ordersheet') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.ordersheet') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'ordersheet') }}"><i
                                class="fa-solid fa-file-pen faa-profile"></i> Case Order Sheets</a>
                    </li>
                @endif

                {{-- #################### DRP TYPE = 3 Case Manager ################# --}}
                @if (auth('drp')->check() && auth('drp')->user()->drp_type == 3)
                    {{-- ############################# ASSIGN CASE ############################### --}}
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.caseassign') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.caseassign') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.caseassign') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'caseassign') }}"><i
                                class="fa-solid fa-bookmark faa-profile"></i> Cases Assign</a>
                    </li>

                    {{-- ############################# All CASE NOTICES ############################### --}}
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.allnotices.cashmanagercasenoticelist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.allnotices.cashmanagercasenoticelist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.allnotices.cashmanagercasenoticelist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'allnotices.cashmanagercasenoticelist') }}"><i
                                class="fa-solid fa-flag faa-profile"></i> All Assigned Case notices</a>
                    </li>

                    {{-- ############################# SEND NOTICES ############################### --}}
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.notices.noticelist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.notices.noticelist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.notices.noticelist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'notices.noticelist') }}"><i
                                class="fa-solid fa-paper-plane faa-profile"></i> Send Notices</a>
                    </li>

                    {{-- ########################### BULK UPDATE CASES ############################ --}}
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.cases.casebulkupdate') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.cases.casebulkupdate') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.cases.casebulkupdate') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'cases.casebulkupdate') }}"><i
                                class="fa-solid fa-upload faa-profile"></i> Case Bulk Update</a>
                    </li>

                    {{-- ########################### COURT ROOM ############################ --}}
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.casemanagercourtroom.courtroomlist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.casemanagercourtroom.courtroomlist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.casemanagercourtroom.courtroomlist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'casemanagercourtroom.courtroomlist') }}"><i
                                class="fa-solid fa-video faa-profile"></i> Court Room</a>
                    </li>
                @endif

                {{-- ################## DRP TYPE = 1 Arbitrator ################## --}}
                {{-- ########################## AWARD ########################### --}}
                @if (auth('drp')->check() && auth('drp')->user()->drp_type == 1)
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.award') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.award') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.award') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'award') }}"><i
                                class="fa-solid fa-file-shield faa-profile"></i> Awards</a>
                    </li>
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.allcases.caselist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.allcases.caselist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.allcases.caselist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'allcases.caselist') }}"><i
                                class="fa-solid fa-tags faa-profile"></i> All Assigned Cases</a>
                    </li>
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.allnotices.arbitratorcasenoticelist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.allnotices.arbitratorcasenoticelist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.allnotices.arbitratorcasenoticelist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'allnotices.arbitratorcasenoticelist') }}"><i
                                class="fa-solid fa-flag faa-profile"></i> All Assigned Case notices</a>
                    </li>
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.courtroom.courtroomlist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'courtroom.courtroomlist') }}"><i
                                class="fa-solid fa-video faa-profile"></i> Court Room</a>
                    </li>
                @endif

                {{-- ################### DRP TYPE 5 Conciliator #################### --}}
                {{-- ##################### SETTLEMENT AGREEMENT #################### --}}
                @if (auth('drp')->check() && auth('drp')->user()->drp_type == 5)
                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.settlementletter') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.settlementletter') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.settlementletter') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'settlementletter') }}"><i
                                class="fa-solid fa-file-zipper faa-profile"></i> Settlement Agreements</a>
                    </li>

                    <li
                        class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.meetingroom.meetinglist') ? 'text-white bg-lime' : '' }}">
                        <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.meetingroom.meetinglist') ? 'text-white bg-lime' : '' }}"
                            aria-current="page"
                            style="{{ request()->routeIs($currentGuard . '.meetingroom.meetinglist') ? 'color:white !important;;' : '' }}"
                            href="{{ route($currentGuard . '.' . 'meetingroom.meetinglist') }}"><i
                                class="fa-solid fa-video faa-profile"></i> Meeting Room</a>
                    </li>
                @endif

                <li class="nav-item py-3 text-center justify-content-start d-flex border-bottom">
                </li>

                <li class="nav-item px-3 text-center justify-content-center d-flex bg-dark">
                    <span class="nav-link sidebar-link text-white" aria-current="page" style="height:40px"
                        href="">Last Updated : 28-Feb-2025</span>
                </li>
            </ul>
        </div>
    </div>
</div>
