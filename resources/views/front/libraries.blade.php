@extends('layouts.front')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/jRange/jquery.range.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/switches.css') }}">
    <style>
        footer {
            display: none;
        }

        .topFormWrapper {
            position: -webkit-sticky;
            /* For Safari */
            background: #ecebfc;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .sticky-filter {
            position: -webkit-sticky;
            position: sticky;
            top: 110px;
            z-index: 999;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle {
            position: relative;
            width: 95px;
            height: 45px;
            background-color: #4361ee;
            border-radius: 25px;
            cursor: pointer;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .toggle:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 5%;
            width: 40%;
            height: 80%;
            background-color: white;
            border-radius: 50%;
            transform: translateY(-50%);
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .toggle-container input {
            display: none;
        }

        .toggle-container input:checked+.toggle:before {
            left: 55%;
        }

        .toggle-container input:checked+.toggle {
            background-color: #ff764b;
        }

        .boy,
        .girl {
            font-size: 20px;
        }

        .boy {
            color: black;
        }

        .girl {
            color: black;
        }

        @media (max-width: 576px) {
            .toggle {
                width: 64px;
                height: 32px;
            }

            .boy,
            .girl {
                font-size: 14px;
            }

            .toggle-container {
                gap: 4px;
            }
        }

        @media (max-width: 768px) {
            .toggle {
                width: 64px;
                height: 32px;
            }

            .boy,
            .girl {
                font-size: 14px;
            }

            .toggle-container {
                gap: 4px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid library-filter-page" style="">
        <div class="topFormWrapper mb-3">
            <form action="" id="topForm" method="get">
                <div class="row pt-4 pb-1 py-lg-4 shadow-sm d-flex justify-content-center align-items-center">
                    <div class="col-lg-6 search-input">
                        <div class="input-group mt-3 mt-lg-0">
                            <span class="icon bg-white">
                                <i class="fa-duotone fa-city"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" name="search"
                                value="{{ request('search') }}" placeholder="Search Libraries / Area">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class=" justify-content-end d-flex mt-2 d-lg-none mb-1">
                        <button class="btn btn-sm btn-outline-second" data-bs-toggle="modal"
                            data-bs-target="#filterModal">Filters</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row pb-5">
            <div class="col-lg-3 col-md-4 mb-3 mb-lg-0 d-none d-lg-block" id="w-filter">
                <div class="filter card sticky-filter shadow-sm">
                    <div class="card-header bg-white border-bottom-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4>Filters</h4>
                            <span role="button" id="reset" class="reset small text-second">Clear All</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <form id="sideFilter" class="sideFilter">
                            <div class="group mb-3">
                                <p class="text-second mb-1 px-3 fw-semi-bold">Budget</p>
                                <div class="p-4">
                                    <input type="hidden" class="budget-slider" value="1,20" />
                                </div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-end ">
                            <button type="button"
                                class="btn bg-second text-white mb-3 me-3 filter-btn d-none d-lg-block">Apply
                                Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-12 loadingData">
                <div id="libraryList" class="px-0 px-lg-3"></div>
                <div class="d-flex align-items-center justify-content-center mt-3">
                    <div id="page-loader"class="spinner-grow" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Place the filter form here -->
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Filters</h4>
                        <span role="button" class="small reset text-second">Clear All</span>
                    </div>
                    <form id="sideFilterModal">
                        <div class="group mb-3">
                            <p class="text-second mb-1 px-3 fw-semi-bold">Sharing type</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="sharing[]" value="1"
                                        id="modal_option_1_1">
                                    <label class="form-check-label" for="modal_option_1_1">Single</label>
                                </li>
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="sharing[]" value="2"
                                        id="modal_option_1_2">
                                    <label class="form-check-label" for="modal_option_1_2">Double</label>
                                </li>
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="sharing[]" value="3"
                                        id="modal_option_1_3">
                                    <label class="form-check-label" for="modal_option_1_3">Triple</label>
                                </li>
                            </ul>
                        </div>
                        <div class="group mb-3">
                            <p class="text-second mb-1 px-3 fw-semi-bold">Food type</p>
                            <ul class="list-group list-group-flush">
                                @foreach (@config('constant.food_type') as $fkey => $foodType)
                                    <li class="list-group-item">
                                        <input class="form-check-input me-1" type="checkbox" name="food_type[]"
                                            value="{{ $fkey }}" id="modal_food_type_{{ $fkey }}">
                                        <label class="form-check-label"
                                            for="modal_food_type_{{ $fkey }}">{{ $foodType }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="group mb-3">
                            <p class="text-second mb-1 px-3 fw-semi-bold">Budget</p>
                            <div class="p-4">
                                <input type="hidden" class="budget-slider" value="1,20" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn bg-second text-white filter-btn" data-bs-dismiss="modal"
                        aria-label="Close">Apply</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="{{ asset('assets/plugins/jRange/jquery.range-min.js') }}"></script>

    <script>
        $('#page-loader').hide();
        let pageEnd = false
        let loading = false;
        let morData = true;

        let filter = {
            page: 1,
            sharing: [],
            food_type: [],
            min_price: 0,
            max_price: 2000,
            city_id: "{{ request('city_id') }}",
            search: $('#searchInput').val()
        };


        $(document).ready(function() {
            $('#filterModal').on('shown.bs.modal', function() {
                initializeSlider(1, 2000);
            });

            // Reinitialize the slider on window resize
            $(window).resize(function() {
                initializeSlider(1, 2000);
                $('#filterModal').modal('hide');
            });

            // Ensure the slider is initialized when the page loads
            initializeSlider(1, 2000);
        });

        $(function() {

            $('.reset').on('click', function() {
                $('#topForm')[0].reset();
                $('#sideFilter')[0].reset();
                $('#sideFilterModal')[0].reset();
                filter = {
                    page: 1,
                    sharing: [],
                    city_id: "{{ request('city_id') }}",
                    search: $('#searchInput').val()
                };
                getDataHtml();
            });

            $('#topForm').on('submit', function(e) {
                e.preventDefault();
                var searchValue = $('#searchInput').val();
                filter = {
                    ...filter,
                    page: 1,
                    search: searchValue
                };
                getDataHtml();
            });

            $(document).on('click', '.filter-btn', function() {
                filter.page = 1;
                getDataHtml()
            });

            setTimeout(() => getDataHtml(), 100);

            async function handleScroll() {
                if ($(window).scrollTop() + $(window).height() >= $('.loadingData').height() -
                    100) {
                    if (!loading && morData) {
                        loading = true;
                        $('#page-loader').show();
                        try {
                            const res = await getData();
                            if (!res) {
                                $('#page-loader').hide();
                                morData = false;
                            }
                        } catch (error) {
                            $('#page-loader').hide();
                            morData = false;
                        } finally {
                            $('#page-loader').hide(); // Hide the loader
                            loading = false;
                        }
                    }
                }
            }

            $(window).scroll(handleScroll);
        })


        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this,
                    args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        function getDataHtml() {
            if (isNaN(filter.min_price)) filter.min_price = 0;
            if (isNaN(filter.max_price)) filter.max_price = 20;
            filter.page = 1;
            $.post("{{ request()->url() }}", filter, function(data) {
                $('#libraryList').html(data.html);
            })
        }

        function getData() {
            if (isNaN(filter.min_price)) filter.min_price = 0;
            if (isNaN(filter.max_price)) filter.max_price = 20;
            filter.page = filter.page + 1;

            return new Promise(function(resolve, reject) {
                $.post("{{ request()->url() }}", filter, function(data) {
                    if (data.status == true) {
                        $('#libraryList').append(data.html);
                        return resolve(true)
                    }
                    return resolve(false);
                })
            });
        }

        function initializeSlider(min, max) {
            $('.budget-slider').jRange({
                from: 1,
                to: 2000,
                step: 1,
                scale: ['100', '500', '1000', '1500', '2000'],
                width: '100%',
                showLabels: true,
                isRange: true,
                onstatechange: debounce(function(value) {
                    if (isNaN(filter.min_price)) filter.min_price = 0;
                    if (isNaN(filter.max_price)) filter.max_price = 20;
                    filter.min_price = value.split(',')[0];
                    filter.max_price = value.split(',')[1];
                    filter.page = 1;
                }, 300),
                // snap: true
            });

            // Set the initial values if provided
            if (min !== undefined && max !== undefined) {
                $('.budget-slider').jRange('setValue', min + ',' + max);
            }
        }
    </script>
@endsection
