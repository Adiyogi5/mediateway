@extends('layouts.front')

<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">

@section('content')
    {{-- ===============Breadcrumb Start============= --}}
    @include('front.includes.profile_header')
    {{-- ===============Breadcrumb End============= --}}

    <div class="container my-xl-5 my-lg-4 my-3">
        <div class="row">
            <div class="col-md-3 col-12">

                @include('front.includes.sidebar_inner')

            </div>

            <div class="col-md-9 col-12">
                <div class="card mb-3 card-inner">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Settlement Agreements :: Edit </h5>
                            </div>
                            <div class="col-auto d-flex item-align-self ms-auto">
                                <div class="form-group my-3">
                                    <a type="button" class="btn btn-warning py-1 px-2" data-bs-toggle="modal"
                                        data-bs-target="#myModal">
                                        <i class="fa fa-star"></i> View Variables</a>
                                </div>
                                <div class="nav nav-pills nav-pills-falcon flex-grow-1 my-auto ms-2" role="tablist">
                                    <a href="{{ route('drp.settlementletter') }}" class="btn btn-outline-secondary">
                                        <i class="fa fa-arrow-left me-1"></i>
                                        Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="row" id="edit" method="POST"
                            action="{{ route('drp.settlementletter.edit', $settlementletter['id']) }}"
                            enctype='multipart/form-data'>
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="drp_type">DRP Type <span
                                                class="required">*</span></label>
                                        <select name="drp_type" class="form-select" id="drp_type">
                                            @if ($drp->drp_type == 4)
                                            <option value="4" {{ old('drp_type', $settlementletter->drp_type) == 4 ? 'selected' : '' }}>
                                                Mediator
                                            </option>
                                            @elseif ($drp->drp_type == 5)
                                            <option value="5" {{ old('drp_type', $settlementletter->drp_type) == 5 ? 'selected' : '' }}>
                                                Conciliator
                                            </option>
                                            @endif
                                        </select>
                                        @error('drp_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Name <span
                                                class="required">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $settlementletter['name']) }}" id="name"
                                            placeholder="Enter Name">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="subject">Subject <span
                                                class="required">*</span></label>
                                        <input type="text" name="subject" class="form-control"
                                            value="{{ old('subject', $settlementletter['subject']) }}" id="subject"
                                            placeholder="Enter Subject">
                                        @error('subject')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="email_content">Email Content <span
                                                class="required">*</span></label>
                                        <textarea id="email_content" name="email_content" class="form-control" id="email_content">{{ old('email_content', $settlementletter['email_content']) }}</textarea>
                                        @error('email_content')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="notice_format">Settlement Agreement Format <span
                                                class="required">*</span></label>
                                        <textarea class="form-control" id="notice_format" name="notice_format">{{ old('notice_format', $settlementletter['notice_format']) }}</textarea>
                                        @error('notice_format')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                                <button class="btn btn-secondary submitbtn" type="submit">Update</button>
                            </div>
                        </form>


                        <div class="row">
                            <div class="col-md-12">
                                <!-- Modal -->
                                <div class="modal fade" id="myModal" role="dialog">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header text-center justify-content-center">
                                                <h4 class="modal-title text-dark fw-bold">Variable for Settlement
                                                    Agreements</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row list-styled px-3" id="variableList"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-dark"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
    <script>
        $(document).on("click", "[data-bs-target='#myModal']", function() {
            $.ajax({
                url: "{{ route('drp.getsettlementletterVariables') }}", // Correct for OrderSheet
                type: "GET",
                success: function(data) {
                    let variableList = $("#variableList");
                    variableList.empty(); // Clear previous data

                    if (data.length > 0) {
                        data.forEach(function(item, index) {
                            const variableText = `@{{ $ {item.name }}}`;
                            const id = `copyText${index}`;
                            variableList.append(`
                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                    <div class="w-100 border rounded p-1 d-flex justify-content-between align-items-center">
                                        <span class="text-dark fw-bold">${variableText}</span>
                                        <button class="btn btn-sm btn-outline-secondary ms-2 copy-btn" data-variable="${variableText}">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                            `);
                        });

                        // Attach copy functionality (no toastr)
                        document.querySelectorAll('.copy-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const textToCopy = this.getAttribute('data-variable');
                                navigator.clipboard.writeText(textToCopy).then(() => {
                                    this.innerText = 'Copied!';
                                    setTimeout(() => {
                                        this.innerText = 'Copy';
                                    }, 1500);
                                }).catch(err => {
                                    console.error('Failed to copy:', err);
                                });
                            });
                        });

                    } else {
                        variableList.append('<p class="text-muted">No Variables Found</p>');
                    }
                },
                error: function(xhr) {
                    console.error("Error:", xhr.status, xhr.responseText);
                    toastr.error("Failed to fetch variables!");
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#notice_format').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['codeview', 'help']],
                ]
            });
            let buttons = $('.note-editor button[data-toggle="dropdown"]');
            buttons.each((key, value) => {
                $(value).on('click', function(e) {
                    $(this).attr('data-bs-toggle', 'dropdown')
                })
            })
        })
        $("#edit").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                subject: {
                    required: true,
                    minlength: 2,
                    maxlength: 200
                },
                email_content: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },
                notice_format: {
                    required: true,
                    minlength: 2,
                    maxlength: 1000
                },

            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                subject: {
                    required: "Please enter subject",
                },
                email_content: {
                    required: "Please enter Email Content",
                },
                notice_format: {
                    required: "Please enter Settlement Agreement",
                },
            },
        });
    </script>
@endsection
