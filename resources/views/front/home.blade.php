@extends('layouts.front')

@section('content')
    {{-- ############### Banner Start ################## --}}
    <section>
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($banners as $key => $banner)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        @php
                            $fileExt = pathinfo($banner['image'], PATHINFO_EXTENSION);
                        @endphp

                        @if (in_array($fileExt, ['mp4']))
                            <!-- If it's a video -->
                            <video autoplay loop muted playsinline class="w-100 img-vid-banner">
                                <source src="{{ $banner['image'] }}" type="video/{{ $fileExt }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <!-- If it's an image -->
                            <img src="{{ $banner['image'] }}" class="d-block w-100 img-vid-banner" alt="Banner Image">
                        @endif

                        <div class="carousel-caption">
                            <h2>{{ $banner['heading'] ?? 'THE DIGITAL COURT ROOM' }}</h2>
                            <p>{{ $banner['sub_heading'] ?? 'Conduct ADR Proceedings from Anywhere, Anytime & on Any Device' }}
                            </p>
                            <a href="{{ $banner['url'] }}" class="btn btn-light">FILE A CASE</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="carousel-indicators">
                @foreach ($banners as $key => $banner)
                    <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $key }}"
                        class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </section>
    {{-- ############### Banner End #################### --}}


    {{-- ############### CMS Welcome Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative mt-5 mb-2">
                    <h1 class="section-heading">Welcome</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 col-12 order-md-1 order-2 d-flex align-items-center text-start justify-content-center">
                    <div class="">
                    <h3 class="section-title">{{ $frontHomecmsWelcome->title }}</h3>
                    <p class="section-content">
                        {{ $frontHomecmsWelcome->description }}
                    </p>
                    <a href="{{ route('front.show-cms', 'why-choose') }}" class="btn btn-learn-more">Learn More &nbsp;&nbsp;&nbsp;<i
                            class="fa-solid fa-arrow-right"></i></a>
                        </div>
                </div>
                <div class="col-md-5 col-12 order-md-2 order-1 position-relative">
                    <img class="img-fluid" src="{{ asset('storage/' . $frontHomecmsWelcome->image) }}" alt="{{ $frontHomecmsWelcome->title }}">
                </div>
            </div>
        </div>
    </section>
    {{-- ############### CMS Welcome End #################### --}}


    {{-- ############### Digital Court room Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative mt-5 mb-2">
                    <h1 class="section-heading">File Your Case With Digital Court room</h1>
                </div>
            </div>
            <div class="row">
                <div class="slider-container">
                    <div class="owl-carousel process-slider">
                        <!-- Step 1 -->
                        <div class="process-card-1">
                            <div class="icon-border">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/img/digital-court-room/contract.png') }}"
                                        alt="Signup / Register">
                                </div>
                            </div>
                            <p>Signup / Register</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="process-card-2">
                            <div class="icon-border">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/img/digital-court-room/agreement.png') }}"
                                        alt="File a Case">
                                </div>
                            </div>
                            <p>File a Case</p>
                        </div>

                        <!-- Step 3 -->
                        <div class="process-card-1">
                            <div class="icon-border">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/img/digital-court-room/methodology.png') }}"
                                        alt="Case Processing">
                                </div>
                            </div>
                            <p>Case Processing</p>
                        </div>

                        <!-- Step 4 -->
                        <div class="process-card-1">
                            <div class="icon-border">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/img/digital-court-room/legal.png') }}"
                                        alt="Digital Court room">
                                </div>
                            </div>
                            <p>Digital Court room</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ############### Digital Court room End ################## --}}


    {{-- ############### How it Works Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative my-xl-5 my-lg-4 my-3">
                    <h1 class="section-heading">How it Works</h1>
                    <p class="section-para">Perform All Your ADR Processes On A Single Platform</p>
                </div>
            </div>
            <div class="row how-it-works-carousel">
                <!-- Tabs -->
                <div class="tab-container">
                    <div class="tab active-tab" data-slider="mediation">MEDIATION</div>
                    <div class="tab" data-slider="conciliation">CONCILIATION</div>
                    <div class="tab" data-slider="arbitration">ARBITRATION</div>
                </div>

                <!-- Sliders -->
                <div id="mediation-slider" class="slider-container active-slider">
                    <div class="owl-carousel mediation-carousel">
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}" alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Mediation</h3>
                                    <p>
                                        In India, the mediation landscape has been significantly shaped by the enactment of the Mediation Act, 2023. This legislation aims to promote and facilitate mediation, particularly institutional mediation, for the resolution of various disputes, including commercial matters. The Act emphasizes voluntary and mutual consented pre-litigation mediation, encouraging parties to seek amicable settlements before initiating formal legal proceedings. E-Gazette
                                    </p>
                                    {{-- <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul> --}}
                                </div>
                                <div class="col-md col-12">
                                    <h3>The process is structured by MWADR into the following stages</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Online request for Mediation along with Prescribed claim form.</p>
                                        </li>
                                        <li>
                                            <p>Appointment of Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Issuance of Case Number.</p>
                                        </li>
                                        <li>
                                            <p>Intimation to Opposite party.</p>
                                        </li>
                                        <li>
                                            <p>Payment of Service Fee.</p>
                                        </li>
                                        <li>
                                            <p>Appointment of Mediator under MWADR’s Rules.</p>
                                        </li>
                                        <li>
                                            <p>Online Opening Session between Disputant parties and Mediator at MWADR’s Online Platform.</p>
                                        </li>
                                        <li>
                                            <p>Reach on Resolution & execute the settlement Agreement.</p>
                                        </li>
                                        <li>
                                            <p>Dashboard for Disputants to overview his case activities.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}" alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div> --}}
                    </div>
                    <div class="pagination" id="mediation-pagination"></div>
                </div>

                <div id="conciliation-slider" class="slider-container">
                    <div class="owl-carousel conciliation-carousel">
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Conciliation</h3>
                                    <p>To settle disputes between parties, conciliation is a non-adjudicatory alternative dispute resolution (ADR) procedure. In order to promote communication, comprehension, and consensus among the parties, it is a voluntary and private dispute resolution process. A neutral third party, known as the conciliator, helps disputing parties come to a mutually agreeable settlement through the use of conciliation, an alternative dispute resolution (ADR) technique. A&C Act 1996</p>
                                    <p>These legislative measures underscore India's commitment to strengthening its alternative dispute resolution mechanisms, making conciliation a viable and effective option for resolving disputes outside the traditional court system.</p>
                                    {{-- <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul> --}}
                                </div>
                                <div class="col-md col-12">
                                    <h3>The process is structured by MWADR into the following stages</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Online request for Conciliation along with Prescribed claim form.</p>
                                        </li>
                                        <li>
                                            <p>Appointment of Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Issuance of Case Number.</p>
                                        </li>
                                        <li>
                                            <p>Invitation u/s 62 of the A & C Act 1996 to Opposite party.</p>
                                        </li>
                                        <li>
                                            <p>Payment of Service Fee.</p>
                                        </li>
                                        <li>
                                            <p>Appointment of Conciliator u/s 63 -65 of the A & C Act 1996 & under MWADR’s Rules.</p>
                                        </li>
                                        <li>
                                            <p>Online Opening Session between Disputant parties and Conciliator at MWADR’s Online Platform.</p>
                                        </li>
                                        <li>
                                            <p>    8. Reach on Resolution & execute the settlement Agreement 
                                                (Pertains to the settlement agreement, stating that if the parties reach an agreement, they may draw up and sign a written settlement agreement, which has the same status and effect as an arbitral award u/s 73 of the A & C Act 1996.).</p>
                                        </li>
                                        <li>
                                            <p>Dashboard for Disputants to overview his case activities.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div> --}}
                    </div>
                    <div class="pagination" id="conciliation-pagination"></div>
                </div>

                <div id="arbitration-slider" class="slider-container">
                    <div class="owl-carousel arbitration-carousel">
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Arbitration</h3>
                                    <p>
                                        The Arbitration and Conciliation Act of 1996, which offers a thorough legal framework for the settlement of disputes through arbitration, regulates arbitration in India. The Act defines the legislation pertaining to arbitration and attempts to revise and combine laws pertaining to domestic arbitration, international & commercial arbitration, and the enforcement of foreign arbitral rulings.
                                    </p>
                                    <p>
                                            The Act grants parties autonomy in determining the procedure for arbitration, subject to the provisions of the Act therefor MWADR is empowered to conduct proceedings in a manner it considers appropriate, ensuring equal treatment of parties and adherence to principles of natural justice.
                                    </p>
                                    {{-- <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul> --}}
                                </div>
                                <div class="col-md col-12">
                                    <h3>The process is structured by MWADR into the following stages:</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Online request for Arbitration along with Prescribed claim form.</p>
                                        </li>
                                        <li>
                                            <p>Appointment of Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Issuance of Case Number.</p>
                                        </li>
                                        <li>
                                            <p>Intimation to Opposite party.</p>
                                        </li>
                                        <li>
                                            <p>Payment of Service Fee.</p>
                                        </li>
                                        <li>
                                            <p>Process of appointment of Arbitrator under MWADR’s Rules.</p>
                                        </li>
                                        <li>
                                            <p>Online hearing process followed by multi level step’s trail at MWADR’s Online Platform.</p>
                                        </li>
                                        <li>
                                            <p>Execution of the Arbitral Award.</p>
                                        </li>
                                        <li>
                                            <p>Dashboard for Disputants to overview his case activities.</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="item">
                            <div class="row gx-3">
                                <div class="col-md col-10 mx-md-none mx-auto">
                                    <img class="img-fluid" src="{{ asset('assets/img/how-it-works.png') }}"
                                        alt="">
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (Pre-Existing)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Notice and list of Claims by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Scrutiny of Notice and Claim by Case Manager.</p>
                                        </li>
                                        <li>
                                            <p>Electronic intimation to the Respondent Online Payment.</p>
                                        </li>
                                        <li>
                                            <p>Automated Notifications and Reminders.</p>
                                        </li>
                                        <li>
                                            <p>Role-based dashboards.</p>
                                        </li>
                                        <li>
                                            <p>Multi-level actions for various documents received and sent.</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md col-12">
                                    <h3>Case Initiation (in case of non-existing agreement)</h3>
                                    <ul class="list-style">
                                        <li>
                                            <p>Drafting of Request for Arbitration by the Claimant.</p>
                                        </li>
                                        <li>
                                            <p>Online discussions between the parties.</p>
                                        </li>
                                        <li>
                                            <p>Building and execution of Arbitration Submission Agreement.</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div> --}}
                    </div>
                    <div class="pagination" id="arbitration-pagination"></div>
                </div>
            </div>
        </div>
    </section>
    {{-- ############### How it Works End #################### --}}


    {{-- ############### CMS About Us Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative mt-5 mb-2">
                    <h1 class="section-heading">About</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 position-relative">
                    <img class="img-fluid" src="{{ asset('storage/' . $frontHomecmsAbout->image) }}" alt="{{ $frontHomecmsAbout->title }}">
                </div>
                <div class="col-md-6 col-12 my-auto">
                    <h3 class="section-title">{{ $frontHomecmsAbout->title }}</h3>
                    <p class="section-content">
                        {{ $frontHomecmsAbout->description }}
                    </p>
                    <div class="d-flex justify-content-md-between justify-content-around">
                        <a href="#" class="btn btn-about-cms"> <i class="fa-solid fa-award"></i>&nbsp;&nbsp;&nbsp;
                            {{$site_settings['years_experience']}}</a>
                        <a href="#" class="btn btn-about-cms"> <i
                                class="fa-solid fa-user-tag"></i>&nbsp;&nbsp;&nbsp;
                            {{$site_settings['cases_count']}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ############### CMS About Us End #################### --}}


    {{-- ############### Features Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative my-xl-5 my-lg-4 my-3">
                    <h1 class="section-heading">Features</h1>
                </div>
            </div>
            <div class="row g-xl-5 g-lg-4 g-3">
                @foreach ($features as $feature)
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        <div class="feature-card mx-xl-3 mx-md-2 mx-1">
                            <div class="icon-circle">
                                <img src="{{ asset('storage/' . $feature['image']) }}" class="p-2"
                                            alt="{{ $feature['name'] }}">
                            </div>
                            <h5>{{ $feature['name'] }}</h5>
                            <p>{{ $feature['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- ############### Features End #################### --}}


    {{-- ############### Testimonial Slider Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative my-xl-5 my-lg-4 my-3">
                    <h1 class="section-heading">Testimonial</h1>
                </div>
            </div>
            <div class="row">
                <div class="owl-carousel testimonial-slider">
                    @foreach ($testimonials as $testimonial)
                        <div class="testimonial-item position-relative">
                            <div class="card testimonial-card mx-1">
                                <div class="card-body text-start">
                                    <img src="{{ asset('storage/' . $testimonial['image']) }}" class="rounded-circle"
                                        alt="{{ $testimonial['name'] }}">
                                    <p>{{ $testimonial['description'] }}</p>
                                    <h5>{{ $testimonial['name'] }}</h5>
                                    <div class="stars d-flex align-item-self">
                                        @for ($i = 1; $i <= $testimonial['rating']; $i++)
                                            <img src="{{ asset('assets/img/star.png') }}" class="img-fluid"
                                                style="height:20px; width:20px; margin-left:0px;margin-bottom:0px" />
                                        @endfor
                                    </div>
                                    <i class="quote-icon"> <i class="fas fa-quote-left"></i></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    {{-- ############### Testimonial Slider End #################### --}}


    {{-- ############### Our Best Clients Slider Start ################## --}}
    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative my-xl-5 my-lg-4 my-3">
                    <h1 class="section-heading">Our Best Clients</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="Client-slider">
                        @foreach ($clients as $client)
                            <div class="px-md-3 px-2 h-100">
                                <div class="card border-0 justify-content-center text-center">
                                    <a class="text-decoration-none" href="">
                                        <img class="card-type-img" src="{{ asset('storage/' . $client['image']) }}"
                                            alt="{{ $client['name'] }}">
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ############### Our Best Clients Slider End #################### --}}
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>   
    <script>
        $(document).ready(function() {
            // ########################Client-slider########################### 
            $('.Client-slider').slick({
                dots: false,
                arrows: true,
                slidesToShow: 5,
                infinite: true,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [{
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 5
                        }
                    },
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 800,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });

            // ########### process-slider ########### 
            $(".process-slider").owlCarousel({
                items: 4,
                margin: 20,
                loop: true,
                dots: false,
                nav: false,
                autoplay: true,
                responsive: {
                    0: {
                        items: 2
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 4
                    },
                    1200: {
                        items: 4
                    }
                }
            });

            // ########### testimonial-slider ########### 
            $(".testimonial-slider").owlCarousel({
                loop: true,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: true,
                autoplayTimeout: 3000,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    1024: {
                        items: 3
                    }
                }
            });
        });

        // ############### Tab-slider ################# 
        $(document).ready(function() {
            function initSlider(className, paginationId) {
                let owl = $("." + className);
                owl.owlCarousel({
                    items: 1,
                    loop: true, // Enable looping
                    nav: false,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 3000, // Adjust autoplay speed
                    autoplayHoverPause: true, // Pause autoplay when hovered
                    onInitialized: function(event) {
                        createPagination(event.item.count, paginationId, owl);
                    }
                });

                // Update pagination when the slide changes (including looping back to the first)
                owl.on("changed.owl.carousel", function(event) {
                    let currentIndex = event.item.index % event.item.count; // Ensure index loops correctly
                    let pagination = $("#" + paginationId);
                    pagination.find("span").removeClass("active-page");
                    pagination.find("span").eq(currentIndex).addClass("active-page");
                });
            }

            function createPagination(slideCount, paginationId, owlInstance) {
                let pagination = $("#" + paginationId);
                pagination.empty();
                for (let i = 0; i < slideCount; i++) {
                    let span = $("<span>").text(i + 1);
                    if (i === 0) span.addClass("active-page");
                    span.on("click", function() {
                        owlInstance.trigger("to.owl.carousel", [i, 300]);
                        pagination.find("span").removeClass("active-page");
                        $(this).addClass("active-page");
                    });
                    pagination.append(span);
                }
            }

            // Initialize Sliders
            initSlider("mediation-carousel", "mediation-pagination");
            initSlider("conciliation-carousel", "conciliation-pagination");
            initSlider("arbitration-carousel", "arbitration-pagination");

            // Tab Switching
            $(".tab").click(function() {
                let sliderId = $(this).data("slider") + "-slider";
                $(".tab").removeClass("active-tab");
                $(this).addClass("active-tab");
                $(".slider-container").removeClass("active-slider").hide();
                $("#" + sliderId).addClass("active-slider").fadeIn();
            });
        });
    </script>
@endsection
