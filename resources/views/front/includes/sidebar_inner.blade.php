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

<div class="offcanvas-header bg-dark px-xl-3 px-lg-2 px-1 text-start justify-content-start py-2 rounded">
    {{-- <img src="{{ basename(auth($currentGuard)->user()->image) == 'img-not-found.png' ? asset('assets/img/dummy-user.png') : asset('storage/' . auth($currentGuard)->user()->image) }}" class="d-block profile-img" alt="..."> --}}
    <img src="{{ $profileImage }}" class="d-block profile-img" alt="User Profile">
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
                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.dashboard') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.dashboard') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.dashboard') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.dashboard') }}"> <i class="fa-solid fa-grip faa-profile"></i>
                        Dashboard</a>
                </li>

                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.profile') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.profile') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.profile') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'profile') }}"><i class="fa-solid fa-user faa-profile"></i> Profile</a>
                </li>

                @if (auth('individual')->check())
                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.case.filecaseview') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'case.filecaseview') }}"><i class="fa-solid fa-cash-register faa-profile"></i> File A Case</a>
                </li>
                @endif

                @if (auth('organization')->check())
                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.staffroles') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.staffroles') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.staffroles') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'staffroles') }}"><i class="fa-brands fa-square-web-awesome-stroke faa-profile"></i> Staff Roles</a>
                </li>
                
                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.staffs') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.staffs') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.staffs') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'staffs') }}"><i class="fa-solid fa-users faa-profile"></i> Staffs</a>
                </li>

                <li class="nav-item px-3 text-center justify-content-start d-flex border-bottom {{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'text-white bg-lime' : '' }}">
                    <a class="nav-link sidebar-link {{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'text-white bg-lime' : '' }}"
                        aria-current="page"
                        style="{{ request()->routeIs($currentGuard . '.cases.filecaseview') ? 'color:white !important;;' : '' }}"
                        href="{{ route($currentGuard . '.' . 'cases.filecaseview') }}"><i class="fa-solid fa-cash-register faa-profile"></i> File Cases</a>
                </li>
                @endif
                
                <li class="nav-item py-3 text-center justify-content-start d-flex border-bottom">
                    
                </li>

                <li class="nav-item px-3 text-center justify-content-center d-flex bg-dark">
                    <span class="nav-link sidebar-link text-white"
                        aria-current="page"
                        style="height:40px"
                        href="">Last Updated : 28-Feb-2025</span>
                </li>
            </ul>
        </div>
    </div>
</div>
