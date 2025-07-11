<header>
    <section class="bg-orange-custom nav-top-head">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center justify-content-md-betweeen">
                <div class="col-12 col-sm-6 col-md-8 col-lg-6 px-0 d-block d-md-block align-self-center">
                    <ul
                        class="nav align-self-center text-center justify-content-center text-md-start justify-content-md-start">
                        <li class="top-li me-1 ps-lg-0 pe-xl-2 pe-2 my-auto d-block d-md-block">
                            @if (!empty($site_settings['phone']))
                                <a class="text-decoration-none" href="tel:{{ $site_settings['phone'] }}">
                                    <i class="fa-solid fa-phone nav-icon-font"></i>
                                    <span class="nav-icon-text">{{ $site_settings['phone'] }} </span>
                                </a>
                            @endif
                        </li>
                        <li class="top-li-line">|</li>
                        @if (!empty($site_settings['email']))
                            <li class="top-li me-1 px-xl-2 my-auto d-block d-md-block">
                                <a class="text-decoration-none" href="mailto:{{ $site_settings['email'] }}">
                                    <i class="fa-solid fa-envelope nav-icon-font"></i>
                                    <span class="nav-icon-text">{{ $site_settings['email'] }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-6 px-0 d-block d-md-block align-self-center">
                    <ul class="nav align-self-center gap-2">
                        <li class="top-li me-1 ps-lg-0 pe-xl-2 pe-2 ms-none ms-md-auto my-auto d-block d-md-block">
                            @if (!empty($site_settings['instagram']))
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link link-dark ps-lg-0 ps-md-0 pe-lg-auto pe-md-1"
                                href="{{ $site_settings['instagram'] }}" target="_blank">
                                <i class="fa-brands fa-instagram faa-header-icon"></i>
                            </a>
                        </li>
                        @endif
                        @if (!empty($site_settings['linkdin']))
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link link-dark ps-lg-2 ps-md-1 pe-lg-auto pe-md-1" target="_blank"
                                    href="{{ $site_settings['linkdin'] }}">
                                    <i class="fa-brands fa-linkedin faa-header-icon"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($site_settings['twitter']))
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link link-dark ps-lg-2 ps-md-1 pe-lg-auto pe-md-1" target="_blank"
                                    href="{{ $site_settings['twitter'] }}">
                                    <i class="fa-brands fa-x-twitter faa-header-icon"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($site_settings['facebook']))
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link link-dark ps-lg-2 ps-md-1 pe-lg-auto pe-md-1" target="_blank"
                                    href="{{ $site_settings['facebook'] }}">
                                    <i class="fa-brands fa-square-facebook faa-header-icon"></i>
                                </a>
                            </li>
                        @endif
                        @if (!empty($site_settings['youtube']))
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link link-dark ps-lg-2 ps-md-1 pe-xl-0 pe-lg-0 pe-md-1" target="_blank"
                                    href="{{ $site_settings['youtube'] }}">
                                    <i class="fa-brands fa-youtube faa-header-icon"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white nav-border">
        <div class="container">
            <div class="row">
                <nav class="navbar navbar-expand-lg navbar-light" aria-label="Fifth navbar example">
                    <div class="container-fluid px-md-0 px-1">
                        <a class="navbar-brand" href="{{ route('front.home') }}">
                            <img class="img-fluid header-logo" src="{{ asset('storage/' . $site_settings['logo']) }}"
                                alt="">
                        </a>

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

                        @if ($currentGuard)
                            <ul class="nav ms-auto d-flex">
                                <li class="nav-item dropdown rounded-2 d-xl-none d-lg-none d-md-block d-block">
                                    <a class="nav-link text-dark dropdown-toggle d-flex align-items-center px-1"
                                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">

                                        {{-- <img src="{{ basename(auth($currentGuard)->user()->image) == 'img-not-found.png' ? asset('assets/img/dummy-user.png') : asset('storage/' . auth($currentGuard)->user()->image) }}"
                                            class="rounded-circle me-2" alt="" width="40px" height="40px"> --}}
                                        <img src="{{ auth($currentGuard)->user() &&
                                        auth($currentGuard)->user()->image &&
                                        basename(auth($currentGuard)->user()->image) != 'img-not-found.png'
                                            ? asset('storage/' . auth($currentGuard)->user()->image)
                                            : asset('assets/img/dummy-user.png') }}"
                                            class="rounded-circle me-2" alt="" width="40px" height="40px">

                                            <div class="flex-column text-start">
                                                @php
                                                    $name = auth($currentGuard)->user()->name;
                                                    $formattedName = strlen($name) > 15
                                                        ? substr($name, 0, 14) . '<br>' . substr($name, 14)
                                                        : $name;
                                                @endphp
                                                <small style="line-height: 18px;">{!! $formattedName !!}</small>


                                                @php
                                                    $drpType =
                                                        config('constant.drp_type')[
                                                            auth($currentGuard)->user()->drp_type
                                                        ] ?? null;
                                                @endphp

                                                @if ($drpType)
                                                    <div class="text-muted small-type">({{ $drpType }})</div>
                                                @endif
                                            </div>
                                    </a>
                                    <ul class="dropdown-menu py-1" aria-labelledby="navbarDropdown">
                                        <li class="border-bottom">
                                            <a class="dropdown-item" href="{{ route($currentGuard . '.dashboard') }}">
                                                <i class="fa-duotone fa-chalkboard me-1"></i>
                                                <span>Dashboard</span>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a class="dropdown-item"
                                                href="{{ route($currentGuard . '.' . 'profile') }}">
                                                <i class="fa-duotone fa-user me-1"></i>
                                                <span>Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i>
                                                <span>Log Out</span>
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item my-auto d-xl-none d-lg-none d-md-block d-block ms-auto">
                                    <a class="nav-title btn btn-sm btn-warning" href="{{ url('individual/login') }}"><i
                                            class="fa-solid fa-right-to-bracket"></i> &nbsp;&nbsp;Login</a>
                                </li>
                                <li class="nav-item my-auto d-xl-none d-lg-none d-md-block d-block me-1">
                                    <a class="nav-title btn btn-sm btn-warning" href="{{ route('register') }}"><i
                                            class="fa-solid fa-user-plus"></i> &nbsp;&nbsp;Register</a>
                                </li>
                            </ul>
                        @endif

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarsExample05">
                            <ul class="nav navbar-nav ms-auto">
                                <li class="nav-item my-auto">
                                    <a class="nav-link nav-title {{ request()->is('/') ? 'active-nav' : '' }}"
                                        href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="nav-item my-auto">
                                    <a class="nav-link nav-title {{ request()->route()->getName() === 'front.show-cms' && request()->route('cms') === 'about-us' ? 'active-nav' : '' }}"
                                        href="{{ route('front.show-cms', 'about-us') }}">About us</a>
                                </li>
                                {{-- <li class="nav-item my-auto">
                                    <a class="nav-link nav-title {{ request()->routeIs('front.news') ? 'active-nav' : '' }}"
                                        href="{{ route('front.news') }}">News Room</a>
                                </li>
                                <li class="nav-item my-auto">
                                    <a class="nav-link nav-title {{ request()->routeIs('front.blogs') ? 'active-nav' : '' }}"
                                        href="{{ route('front.blogs') }}">Blogs</a>
                                </li> --}}
                                <li class="nav-item dropdown my-auto">
                                    <a class="nav-link nav-title dropdown-toggle {{ request()->routeIs('front.services.services') ? 'active-nav' : '' }}"
                                        href="{{ route('front.services.services') }}" id="serviceDropdown" role="button">
                                        Services
                                        <span class="dropdown-arrow">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 9l6 6 6-6" stroke="black" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu py-1" aria-labelledby="serviceDropdown">
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.services.msme') }}">MSME</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.services.mediation') }}">MEDIATION</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.services.conciliation') }}">CONCILIATION</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.services.arbitration') }}">ARBITRATION</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.services.odr') }}">ODR</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ route('front.services.lokadalat') }}">LOK ADALAT</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown me-xl-5 me-xl-4 me-md-3 me-auto my-auto">
                                    <a class="nav-link nav-title dropdown-toggle {{ request()->routeIs('front.products.digitalroom') ? 'active-nav' : '' }}"
                                        href="" id="serviceDropdown" role="button">
                                        Products
                                        <span class="dropdown-arrow">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 9l6 6 6-6" stroke="black" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu py-1" aria-labelledby="serviceDropdown">
                                        <li class="border-bottom"><a class="dropdown-item" href="{{ route('front.products.digitalroom') }}">Digital Room</a></li>
                                        <li class=""><a class="dropdown-item" href="{{ route('front.products.odrplatform') }}">ODR Platform</a></li>
                                    </ul>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link nav-title {{ request()->route('slug') === 'about-us' ? 'active-nav' : '' }}" href="{{ route('front.cms', ['slug' => 'about-us']) }}">Product</a>
                                </li> --}}

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

                                @if ($currentGuard)
                                    <li class="nav-item dropdown rounded-2 d-none d-md-none d-lg-block d-xl-block">
                                        <a class="nav-link text-dark dropdown-toggle d-flex align-items-center"
                                            href="#" id="navbarDropdown" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">

                                            {{-- <img src="{{ basename(auth($currentGuard)->user()->image) == 'img-not-found.png' ? asset('assets/img/dummy-user.png') : asset('storage/' . auth($currentGuard)->user()->image) }}" class="rounded-circle me-2" alt="" width="40px" height="40px"> --}}
                                            <img src="{{ auth($currentGuard)->user() &&
                                            auth($currentGuard)->user()->image &&
                                            basename(auth($currentGuard)->user()->image) != 'img-not-found.png'
                                                ? asset('storage/' . auth($currentGuard)->user()->image)
                                                : asset('assets/img/dummy-user.png') }}"
                                                class="rounded-circle me-2" alt="" width="40px"
                                                height="40px">
                                            <div class="flex-column text-start">
                                            @php
                                                $name = auth($currentGuard)->user()->name;
                                                $formattedName = strlen($name) > 20
                                                    ? substr($name, 0, 20) . '<br>' . substr($name, 20)
                                                    : $name;
                                            @endphp
                                            <span style="line-height: 18px">{!! $formattedName !!}</span>

                                                @php
                                                    $drpType =
                                                        config('constant.drp_type')[
                                                            auth($currentGuard)->user()->drp_type
                                                        ] ?? null;
                                                @endphp

                                                @if ($drpType)
                                                    <div class="text-muted small-type">({{ $drpType }})</div>
                                                @endif
                                            </div>
                                        </a>
                                        <ul class="dropdown-menu py-1" aria-labelledby="navbarDropdown">
                                            <li class="border-bottom">
                                                <a class="dropdown-item"
                                                    href="{{ route($currentGuard . '.dashboard') }}">
                                                    <i class="fa-duotone fa-chalkboard me-1"></i>
                                                    <span>Dashboard</span>
                                                </a>
                                            </li>
                                            <li class="border-bottom">
                                                <a class="dropdown-item"
                                                    href="{{ route($currentGuard . '.' . 'profile') }}">
                                                    <i class="fa-duotone fa-user me-1"></i>
                                                    <span>Profile</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="fa-solid fa-right-from-bracket me-1"></i>
                                                    <span>Log Out</span>
                                                    <form id="logout-form" action="{{ route('logout') }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                    </form>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @else
                                    <li class="nav-item my-auto d-none d-md-none d-lg-block d-xl-block">
                                        <a class="nav-title btn btn-sm btn-warning"
                                            href="{{ url('individual/login') }}">
                                            <i class="fa-solid fa-right-to-bracket"></i> &nbsp;&nbsp;Login
                                        </a>
                                    </li>
                                    <li class="nav-item my-auto d-none d-md-none d-lg-block d-xl-block">
                                        <a class="nav-title btn btn-sm btn-warning me-0"
                                            href="{{ route('register') }}">
                                            <i class="fa-solid fa-user-plus"></i> &nbsp;&nbsp;Register
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</header>
