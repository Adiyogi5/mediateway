@extends('layouts.front')


@section('content')
    @include('front.includes.profile_header')


    <section>
        <div class="container my-xl-5 my-3">
            <div class="row">
                <div class="col-12 text-center position-relative my-xl-5 my-lg-4 my-3">
                    <h1 class="section-heading">ARBITRATION</h1>
                    <p class="section-para">An advocate is a legal professional who represents clients in courts, provides legal advice, drafts legal documents, and helps resolve disputes. They ensure justice by defending rights, handling cases in civil An advocate is a legal professional who represents clients in courts, provides legal advice, drafts legal documents, and helps resolve disputes. They ensure justice by defending rights, handling cases in civil </p>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container mb-xl-5 mb-3">
            <div class="row">
                <div class="col-12 text-center position-relative mb-xl-5 mb-lg-4 mb-3">
                    <h1 class="section-heading">How it Works</h1>
                    <p class="section-para">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem  Ipsum has been the industry's standard dummy Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's  standard dummy</p>
                </div>
            </div>
            <div class="row how-it-works-carousel">
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

                        <div class="item">
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
                        </div>
                    </div>
                    <div class="pagination" id="mediation-pagination"></div>
                </div>
            </div>
        </div>
    </section>

@endsection


@section('js')
<script>
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
