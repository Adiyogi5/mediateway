<footer class="border-top">
    <div class="container-fluid">
        <div class="row py-5 mt-5">
            <div class="col-lg-4 col-md-6 px-4 mb-4  mb-lg-0">
                <a href="{{ route('front.home') }}" class="d-flex align-items-center mb-3 link-dark text-decoration-none">
                    <img src="{{ asset('storage/' . $site_settings['logo']) }}" class="logo" alt="logo" />
                </a>
                <p class="text-justify">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Inventore, deleniti! Possimus,
                    consequatur! Sint dolore ex tenetur cumque atque, dolorum repudiandae.
                </p>
                <div>
                    <p class="mb-1">Follow Us:</p>
                    <div class="social-icon">
                        <a href="{{ $site_settings['facebook'] }}" class="icon" target="_blank">
                            <i class="fa-brands fa-square-facebook"></i>
                        </a>
                        <a href="{{ $site_settings['instagram'] }}" class="icon" target="_blank">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="{{ $site_settings['twitter'] }}" class="icon" target="_blank">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="{{ $site_settings['linkdin'] }}" class="icon" target="_blank">
                            <i class="fa-brands fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 px-4 mb-4  mb-lg-0">
                <ul class="nav flex-column footer-list">
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.home') }}" class="nav-link">
                            Home
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.show-cms', 'about-us') }}" class="nav-link">
                            About Us
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.contact-us') }}" class="nav-link">
                            Contact Us
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('register') }}" class="nav-link">
                            List Your Property
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 px-4 mb-4  mb-lg-0">
                <ul class="nav flex-column footer-list">
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.show-cms', 'terms-condition') }}" class="nav-link">
                            Terms Condition
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.show-cms', 'privacy-policy') }}" class="nav-link">
                            Privacy Policy
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('front.faqs') }}" class="nav-link">
                            FAQs
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6 px-4 mb-4  mb-lg-0 contact">
                <div class="d-flex gap-2 align-items-start mb-3">
                    <div class="icon"><i class="fa fa-phone"></i></div>
                    <p> {{ $site_settings['phone'] }}</p>
                </div>
                <div class="d-flex gap-2 align-items-start mb-3">
                    <div class="icon"><i class="fa fa-envelope"></i></div>
                    <p> {{ $site_settings['email'] }}</p>
                </div>
                <div class="d-flex gap-2 align-items-start mb-3">
                    <div class="icon"><i class="fa-regular fa-location-dot"></i></div>
                    <p> {{ $site_settings['address'] }}</p>
                </div>
            </div>
        </div>
        <div class="row bg-white text-muted fw-semi-bold">
            <div class="col-lg-6 mt-lg-3 my-1">
                <p class="text-center px-4 text-lg-end border-0 border-lg-2 border-end mb-0">
                    {{ $site_settings['copyright'] }}
                </p>
            </div>
            <div class="col-lg-6 mt-lg-3 my-1">
                <p class="text-center text-lg-start">
                    Designed & Delvelop By :
                    <a href="https://adiyogitechnosoft.com" class="text-muted text-decoration-none mb-0" target="_lucky">Adiyogi Technosoft</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- ===============================================-->
<!--    JavaScripts-->
<!-- ===============================================-->

<input name="config" type="hidden" value="{{ isset($config) ? json_encode($config) : null }}">
<script src="{{ asset('assets/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/custom-methods.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
@yield('js')
@include('partial.toastr')