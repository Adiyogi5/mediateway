<header>
    <section class="bg-orange-custom nav-top-head">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center justify-content-md-betweeen">
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 px-0 d-block d-md-block align-self-center">
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
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 px-0 d-block d-md-block align-self-center">
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
                        <ul class="nav ms-auto d-flex">
                            <li class="nav-item my-auto d-xl-none d-lg-none d-md-block d-none">
                                <a class="nav-title btn btn-sm btn-warning" href="{{ route('front.home') }}"><i
                                        class="fa-solid fa-right-to-bracket"></i> &nbsp;&nbsp;&nbsp;Login</a>
                            </li>
                            <li class="nav-item my-auto d-xl-none d-lg-none d-md-block d-none me-1">
                                <a class="nav-title btn btn-sm btn-warning" href="{{ route('front.home') }}"><i
                                        class="fa-solid fa-user-plus"></i> &nbsp;&nbsp;&nbsp;Register</a>
                            </li>
                        </ul>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarsExample05">
                            <ul class="nav navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a class="nav-link nav-title {{ request()->is('/') ? 'active-nav' : '' }}"
                                        href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link nav-title {{ request()->routeis('front.home') ? 'active-nav' : '' }}"
                                        href="{{ route('front.home') }}">About us</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link nav-title dropdown-toggle {{ request()->routeIs('front.home') ? 'active-nav' : '' }}"
                                        href="#" id="serviceDropdown" role="button">
                                        Service
                                        <span class="dropdown-arrow">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 9l6 6 6-6" stroke="black" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu py-1" aria-labelledby="serviceDropdown">
                                        <li class="border-bottom"><a class="dropdown-item" href="">Web Development</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="">App Development</a></li>
                                        <li><a class="dropdown-item" href="">Digital Marketing</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown me-xl-5 me-xl-4 me-md-3 me-auto">
                                    <a class="nav-link nav-title dropdown-toggle {{ request()->routeIs('front.home') ? 'active-nav' : '' }}"
                                        href="#" id="serviceDropdown" role="button">
                                        Product
                                        <span class="dropdown-arrow">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 9l6 6 6-6" stroke="black" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu py-1" aria-labelledby="serviceDropdown">
                                        <li class="border-bottom"><a class="dropdown-item" href="">Web Development</a></li>
                                        <li class="border-bottom"><a class="dropdown-item" href="">App Development</a></li>
                                        <li><a class="dropdown-item" href="">Digital Marketing</a></li>
                                    </ul>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link nav-title {{ request()->route('slug') === 'about-us' ? 'active-nav' : '' }}" href="{{ route('front.cms', ['slug' => 'about-us']) }}">Product</a>
                                </li> --}}
                                <li class="nav-item my-auto d-block d-md-none d-lg-block d-xl-block">
                                    <a class="nav-title btn btn-sm btn-warning" href="{{ route('front.home') }}"><i
                                            class="fa-solid fa-right-to-bracket"></i> &nbsp;&nbsp;&nbsp;Login</a>
                                </li>
                                <li class="nav-item my-auto d-block d-md-none d-lg-block d-xl-block">
                                    <a class="nav-title btn btn-sm btn-warning me-0" href="{{ route('front.home') }}"><i
                                            class="fa-solid fa-user-plus"></i> &nbsp;&nbsp;&nbsp;Register</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</header>
