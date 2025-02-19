@extends('layouts.front')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/flexslider/flexslider.css') }}">
@endsection

@section('content')
    {{-- <div class="bg-purple py-5">
        <div class="container">
            <h1 class="text-gray fw-semi-bold text-uppercase">Library Details</h1>
            <ol class="breadcrumb" style="--bs-breadcrumb-divider : '/">
                <li class="breadcrumb-item">
                    <a class="text-gray text-decoration-none" href="{{ route('front.home') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="text-gray text-decoration-none" href="{{ route('front.libraries') }}">Find Library</a>
                </li>
                <li class="breadcrumb-item text-gray active" aria-current="page">Library Details</li>
            </ol>
        </div>
    </div> --}}

    <div class="container library-details">
        <div class="row my-5">
            <div class="col-12 mb-3">
                <h1 class="text-first fw-bold">{{ $library->name }}</h1>
                <div class="d-flex align-items-center flex-wrap">
                    <i class="fa-solid fa-location-dot fs-5 me-2 text-second"></i>
                    <p class="mb-0 me-2">
                        {{ @$library->area->name }}, {{ @$library->city->name }}, {{ @$library->state->name }}
                        {{ $library->pincode }}
                    </p>

                    <div class="rating small">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="i fa fa-star {{ $i < $library->rating ? 'text-warning' : 'text-muted' }}"></div>
                        @endfor
                        <small class="text-muted ms-2 fw-semi-bold">( {{ $library->rating_count }} Review )</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-3">
                @if ($library->images->count())
                    <div id="slider" class="flexslider rounded-5 over-hidden">
                        <ul class="slides">
                            @foreach ($library->images as $image)
                                <li><img src="{{ $image->image }}" /></li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="carousel" class="flexslider thumbnail">
                        <ul class="slides">
                            @foreach ($library->images as $image)
                                <li><img src="{{ $image->image }}" /></li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <img src="{{ asset('assets/img/img-not-found.png') }}" alt="" class="w-100">
                @endif
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-first fw-semi-bold mb-2">Pricing</h4>
                        <div class="d-flex gap-2 sharing-radio flex-wrap">
                            @foreach ($library->prices as $price)
                                <div>
                                    <input class="d-none" type="radio" name="price" data-price="{{ $price->price }}"
                                        id="{{ 'share_' . $price->id }}" @checked($loop->index === 0)>
                                    <label for="{{ 'share_' . $price->id }}">
                                        {{ \Carbon\Carbon::parse($price->shift_start)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($price->shift_end)->format('g:i A') }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <h2 class="text-muted my-3 fw-semi-bold">
                            <i class="fa fa-inr"></i>
                            <span id="showPrice"> {{ $library->prices->value('price', 0.0) }}</span>
                        </h2>
                        <h6 class="text-muted my-3 fw-semi-bold">
                            <span id="showPrice"> {{ $library->description }}</span>
                        </h6>
                        <div class="text-start">
                            <button class="btn btn-md bg-second text-white my-2 px-4" id="contactUs">Contact Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-5">
            <div class="@if ($library->google_location) col-lg-7 @else col-lg-12 @endif  mb-3 ">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-first fw-semi-bold">Amazing Amenities</h2>
                        <div class="d-flex flex-wrap mt-3 amazing-amenities">
                            @foreach ($library->amenities_list as $amenity)
                                <div class="tile d-flex gap-2 mb-2 align-items-center">
                                    <div class="icon">
                                        <img src="{{ asset('storage/' . $amenity->image) }}" alt="" class="w-100">
                                    </div>
                                    <span>{{ $amenity->name }}</span>
                                </div>
                            @endforeach

                            @if ($library->amenities_list->count() === 0)
                                <h5 class="text-danger">No Data Available</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (@$library->google_location)
                <div class="col-lg-5 mb-3">
                    <div class="card overflow-hidden">
                        <div class="card-body p-0">
                            <iframe src="{{ $library->google_location }}" width="1680" height="300" style="border:0;"
                                allowfullscreen="" loading="lazy" class="w-100"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-lg-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex p-3 justify-content-between">
                            <h2 class="text-first fw-semi-bold">Reviews & Ratings</h2>
                            <button type="button" class="btn btn-outline-second px-3" data-bs-toggle="modal"
                                data-bs-target="#reviewModal">
                                Your Review
                            </button>
                        </div>
                        <div class="row">
                            @if ($reviews->count())
                                @foreach ($reviews as $review)
                                    <div class="col-12 col-lg-12 border-bottom">
                                        <div class="px-3 py-2">
                                            <div class="rating">
                                                @for ($i = 0; $i < 5; $i++)
                                                    <i
                                                        class="fa fa-star {{ $i < $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>

                                            <blockquote class="bsb-blockquote-icon mb-2">
                                                {{ $review->comment }}
                                            </blockquote>
                                            <h4 class="mb-1 h5 text-second">{{ @$review->customer->name }}</h4>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="pt-3 custom">
                                    {!! $reviews->links() !!}
                                </div>
                            @else
                                <h5 class="mb-2 text-center text-danger py-3">No Reviews Yet.</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($libraries->count())
                <div class="col-lg-12 my-5">
                    <h2 class="text-first fw-semi-bold">Recommended Libraries</h2>
                    <div class="row mt-3 most-popular">
                        @foreach ($libraries as $vlibrary)
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="card">
                                    <img src="{{ $vlibrary->main_image->image }}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            {{ str($vlibrary->name)->limit(50) }}
                                        </h5>
                                        <p class="card-text text-justify">
                                            {{ str($vlibrary->description)->limit(100) }}
                                        </p>
                                        <div class="text-center">
                                            <a href="{{ route('front.library-details', $vlibrary->slug) }}"
                                                class="btn btn-sm book-now">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="reviewModalLabel">Share Your Review</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="reviewForm">
                        <div class="row">
                            <div class="col-lg-6 mb-2">
                                <input class="form-control" type="text" name="name" placeholder="Your Name">
                                <input type="hidden" name="library_id" value="{{ $library->id }}">
                            </div>
                            <div class="col-lg-6 mb-2">
                                <input class="form-control" type="text" name="mobile" placeholder="Your Mobile">
                            </div>
                            <div class="col-lg-6 mb-2">
                                {{-- <input type="hidden" name="rating" min="1" max="5"> --}}
                                <div class="rating-hotel"></div>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-control" id="otp" type="text" name="otp"
                                        placeholder="OTP">
                                    <button class="btn btn-outline-second" type="button" id="sendOtp"><i
                                            class="fa fa-send"></i></button>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <textarea class="form-control" name="comment" id="comment" placeholder="Your Comment"></textarea>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.star-rating.js') }}"></script>
    <script src="{{ asset('assets/plugins/flexslider/jquery.flexslider.js') }}"></script>

    <script>
        $(function() {
            $('#reset').on('click', function() {
                $('#sideFilter')[0].reset()
            });

            $('#carousel').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: true,
                slideshow: true,
                itemWidth: 210,
                itemMargin: 5,
                asNavFor: '#slider'
            });

            $('#slider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: true,
                slideshow: true,
                sync: "#carousel"
            });

            $('[name="price"]').on('click', function() {
                $('#showPrice').text($(this).data('price'));
            });

            $('#contactUs').on('click', function() {
                Swal.fire({
                    title: "Contact Details.",
                    html: `<div>
                        <p class='fs-4 mb-2 text-second'>Mobile Number - <span class="fw-semi-bold">{{ $library->mobile }}</span></p>
                        <p class='fs-5 mb-2 text-second'>Email - <span class="fw-semi-bold">{{ $library->email }}</span></p>
                    </div>`,
                    icon: "success"
                });
            });

            const validator = $("#reviewForm").validate({
                errorClass: "text-danger fs--1",
                errorElement: "small",
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 5,
                        maxlength: 50
                    },
                    mobile: {
                        required: true,
                        number: true,
                        indiaMobile: true,
                        digits: 10,
                    },
                    comment: {
                        required: true,
                        minlength: 10,
                        maxlength: 100
                    },
                    rating: {
                        required: true,
                        number: true,
                        min: 1,
                        max: 5
                    },
                    otp: {
                        required: true,
                        number: true,
                        digits: 6,
                    }
                },
                messages: {
                    name: {
                        required: "Please enter your name",
                    },
                    mobile: {
                        required: "Please enter your mobile.",
                    },
                    comment: {
                        required: "Please enter comment.",
                    },
                    rating: {
                        required: "Please select rating start.",
                    },
                    otp: {
                        required: "Please enter OTP.",
                    }
                },
                errorPlacement: function(error, element) {
                    if ($(element).parent().hasClass('input-group')) {
                        error.appendTo(element.parents().eq(1));
                    } else if ($(element).parent().parent().hasClass('rating-hotel')) {
                        error.appendTo(element.parents().eq(2));
                    } else if ($(element).parent().hasClass('d-flex')) {
                        error.appendTo(element.parents().eq(1));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: "{{ route('front.library-rating') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                Swal.fire('', data?.message, "success")
                                $('#reviewModal').modal('hide');
                                $(form).trigger("reset")
                            } else {
                                $(form).validate().showErrors(data.data);
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });

            $('#sendOtp').on('click', function() {
                var mobile = $('[name="mobile"]').val();
                if (!mobile) {
                    return validator.showErrors({
                        mobile: 'Please enter mobile number first..!!'
                    });
                }

                $(this).prop('disabled', true);
                $(this).find('i').addClass('fa-spin');
                var button = this;
                $.ajax({
                    url: "{{ url('api/send-otp') }}",
                    type: 'post',
                    data: {
                        mobile,
                    },
                    headers: {
                        'x-api-key': "{{ config('constant.secret_token') }}"
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            toastr.success(data.message);
                            setTimeout(() => {
                                $(button).prop('disabled', false)
                                $(button).find('i').removeClass('fa-spin');
                            }, 30000);
                        } else {
                            toastr.error(data.message);
                            validator.showErrors(data.data);
                            $(button).prop('disabled', false)
                            $(button).find('i').removeClass('fa-spin');
                        }
                    },
                    error: function(data) {
                        alert("Outlet Creation Failed, please try again.");
                    }
                });
            });

            $('.rating-hotel').starRating({
                starsSize: 1.5,
                wrapperClasses: '',
                showInfo: false,
                inputName: 'rating'
            });

            $(document).on('change', '.rating-hotel', function(e, stars, index) {
                $('[name="rating"]').val(stars);
            });
        })
    </script>
@endsection
