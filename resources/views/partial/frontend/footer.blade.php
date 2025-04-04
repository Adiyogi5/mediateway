<footer class="border-top">
    <section>
        <div class="container">
            <div class="row g-md-0 g-4 py-lg-5 py-md-4 py-3">
                <div class="col-md-4 col-12">
                    <div class="row">
                        <div class="col">
                            <ul class="nav flex-column footer-list">
                                <h3 class="footer-title">MEDIA CENTER</h3>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.news') }}" class="nav-link-footer ">
                                        NEWSROOM
                                    </a>
                                </li>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.blogs') }}" class="nav-link-footer">
                                        Blog
                                    </a>
                                </li>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.news') }}" class="nav-link-footer">
                                        FAQ's
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="col">
                            <ul class="nav flex-column footer-list">
                                <h3 class="footer-title">LEGAL</h3>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.show-cms', 'privacy-policy') }}" class="nav-link-footer ">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.show-cms', 'terms-conditions') }}" class="nav-link-footer">
                                        Terms & Conditions
                                    </a>
                                </li>
                                <li class="nav-item-footer mb-2">
                                    <a href="{{ route('front.show-cms', 'rules') }}" class="nav-link-footer">
                                        Rules
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-11 footer-search">
                            <div class="search">
                                <input type="tel" name="mobile" class="form-control"
                                    placeholder="Your Tel / Mobile">
                                <button class="btn btn-footer-warning">Call Me</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md col-6 mb-4 mb-lg-0">
                    <ul class="nav flex-column footer-list">
                        <h3 class="footer-title">CONNECT TO US</h3>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.home') }}" class="nav-link-footer ">
                                BUY AGREEMENT
                            </a>
                        </li>
                        @if (auth('individual')->check())
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('individual.case.filecase') }}" class="nav-link-footer {{ request()->routeIs('individual.case.filecase') ? 'active-footer' : '' }}">
                                FILE A CASE/DISPUTE
                            </a>
                        </li>
                        @endif
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.bookappointment') }}" class="nav-link-footer {{ request()->routeIs('front.bookappointment') ? 'active-footer' : '' }}">
                                BOOK APPOINMENT
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.callback') }}" class="nav-link-footer {{ request()->routeIs('front.callback') ? 'active-footer' : '' }}">
                                TALK TO AN EXPERT
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.contactus') }}" class="nav-link-footer {{ request()->routeIs('front.contactus') ? 'active-footer' : '' }}">
                                CONTACT US
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md col-6 mb-4 mb-lg-0">
                    <ul class="nav flex-column footer-list">
                        <h3 class="footer-title">JOIN US</h3>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.show-cms', 'terms-condition') }}" class="nav-link-footer">
                                ADR PRECTITIONERS
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.show-cms', 'privacy-policy') }}" class="nav-link-footer">
                                ADVOCATES
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.home') }}" class="nav-link-footer">
                                MEDIATORES
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.home') }}" class="nav-link-footer">
                                CONCILIATORES
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.home') }}" class="nav-link-footer">
                                ARBITRATORS
                            </a>
                        </li>
                        <li class="nav-item-footer mb-2">
                            <a href="{{ route('front.home') }}" class="nav-link-footer">
                                CASE MANAGERS
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3 col-12 mb-4 mb-lg-0 contact">
                    <h3 class="footer-title">CONTACT</h3>
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <div class="icon"><i class="fa fa-phone"></i></div>
                        <p class="mb-0"> {{ $site_settings['phone'] }}</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <div class="icon"><i class="fa fa-envelope"></i></div>
                        <p class="word-wrap mb-0"> {{ $site_settings['email'] }}</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                        <p class="word-wrap mb-0"> {{ $site_settings['address'] }}</p>
                    </div>

                    <div class="d-flex justify-content-start mt-3">
                        <h3 class="footer-title my-auto me-1">Social :-</h3>
                        <div class="social-icon">
                            <a href="{{ $site_settings['instagram'] }}" class="icon" target="_blank">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <a href="{{ $site_settings['facebook'] }}" class="icon" target="_blank">
                                <i class="fa-brands fa-square-facebook"></i>
                            </a>
                            <a href="{{ $site_settings['twitter'] }}" class="icon" target="_blank">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a href="{{ $site_settings['linkdin'] }}" class="icon" target="_blank">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 copyright">
                    <div class="d-flex my-md-2 my-1">
                        <p class="text-copiright text-center text-lg-start mb-0">
                            {{ $site_settings['copyright'] }} &nbsp;
                        </p>
                        <p class="text-copiright text-center text-lg-start mb-0">
                            Designed & Delvelop By :
                            <a href="https://adiyogitechnosoft.com"
                                class="text-warning font-regular text-decoration-none mb-0" target="_lucky">Adiyogi
                                Technosoft</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</footer>

<!-- ===============================================-->
<!--    JavaScripts-->
<!-- ===============================================-->

<input name="config" type="hidden" value="{{ isset($config) ? json_encode($config) : null }}">
<script src="{{ asset('assets/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

<script src="{{ asset('assets/js/countfect.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/chili-1.7.pack.js') }}"></script>
<script src="{{ asset('assets/js/jquery.cycle.all.js') }}"></script>
<script src="{{ asset('assets/js/jquery.easing.1.3.js') }}"></script>

@yield('js')
@include('partial.toastr')
