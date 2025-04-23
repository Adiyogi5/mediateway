@extends('layouts.front')
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
                <div class="card mb-3 card-inner form-validate">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Court Room - Live</h5>
                            </div>
                            <div class="col-auto ms-auto">
                                <div class="nav nav-pills nav-pills-falcon">
                                    {{-- @if (Helper::userCan(111, 'can_add')) --}}
                                    <a href="{{ route('drp.courtroom.courtroomlist') }}" class="btn btn-outline-secondary">
                                        <i class="fa fa-list me-1"></i>
                                        Court Lists
                                    </a>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0 table-meetinglist">
                        <div class="row gy-3">
                            <div class="col-12">
                                <div class="livemeeting-card">
                                    <div class="w-100" style="height: 500px"></div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 order-lg-1 order-2">
                                <div class="livemeeting-card h-100">
                                    <h4 class="livemeetingcard-heading">UPDATES</h4>
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div
                                                    class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">06-apr-2025 03:50pm</h4>
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small ">
                                                        1st Hearing MOM : The Respodent failed to attend the 1st Hearing.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-3 border-1 active overflow-hidden">
                                        <div class="card-body py-2 px-md-3 px-2">
                                            <div class="row">
                                                <div
                                                    class="col-12 border-bottom d-md-flex justify-content-md-between d-flex justify-content-around text-center item-align-self">
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">Registrar</h4>
                                                    <h4 class="livemeetingcard-title">06-apr-2025 03:50pm</h4>
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </div>
                                                <div class="col">
                                                    <p class="livemeetingcard-text text-muted small ">
                                                        1st Hearing MOM : The Respodent failed to attend the 1st Hearing.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12 order-lg-2 order-1">
                                <form id="sendnoticeForm" action="{{ route('drp.courtroom.savenotice') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="file_case_id" value="{{ $caseData->id }}">
                                    <div class="livemeeting-card h-100">
                                        <div class="form-group">
                                            <textarea class="form-control" rows="5" id="livemeetingdata" name="livemeetingdata">{{ old('livemeetingdata') }}</textarea>

                                            @error('livemeetingdata')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <!-- Document Type and File Upload -->
                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Select Document Type and Attach</label>
                                            <div class="row g-2">
                                                <div class="col-xl-6 col-12">
                                                    <select class="form-select" id="docType" name="docType">
                                                        <option selected disabled>Document Type</option>
                                                        <option value="ordersheet">Case OrderSheet</option>
                                                        <option value="settlementletter">Settlement Agreement</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-6 col-12">
                                                    <select class="form-select" id="tempType" name="tempType">
                                                        <option selected disabled>Template Type</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload Button -->
                                        <div class="text-center mt-3">
                                            <button class="btn btn-secondary w-100" id="uploadBtn" type="submit">UPLOAD /
                                                SAVE</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>  

    <script type="text/javascript">
        const allTemplates = {
            ordersheet: @json($orderSheetTemplates),
            settlementletter: @json($settlementLetterTemplates),
        };
        const flattenedCaseData = @json($flattenedCaseData); // Make sure this is correct

        $(document).ready(function() {
            $('#livemeetingdata').summernote({
                height: 200,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['paragraph']],
                ]
            });

            $('#docType').on('change', function() {
                const docType = $(this).val();
                const templates = allTemplates[docType] || [];

                let options = '<option selected disabled>Template Type</option>';
                templates.forEach(template => {
                    const format = encodeURIComponent(template.notice_format || '');
                    options +=
                        `<option value="${template.id}" data-format="${format}">${template.name}</option>`;
                });

                $('#tempType').html(options);
            });

            $('#tempType').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const rawFormat = decodeURIComponent(selectedOption.data('format') || '');

                try {
                    if (!flattenedCaseData) {
                        console.error("flattenedCaseData is not available");
                        return;
                    }

                    // Replace placeholders in the template
                    const content = rawFormat.replace(/\{\{([\s\S]+?)\}\}/g, function(match, key) {
                        // Create a temporary element to strip all HTML formatting
                        const temp = document.createElement('div');
                        temp.innerHTML = key;

                        // Get plain text content (removes bold, font, size, etc.)
                        let plainKey = temp.textContent || temp.innerText || '';

                        // Normalize: convert to lowercase, trim, replace spaces/special characters
                        const normalizedKey = plainKey
                            .replace(/&nbsp;/gi, ' ') // decode HTML spaces
                            .replace(/\s+/g, ' ') // collapse multiple spaces
                            .trim() // trim edges
                            .toLowerCase() // lowercase for matching
                            .replace(/[^a-z0-9]/g, '_'); // remove non-alphanumeric, use _

                        const value = flattenedCaseData[normalizedKey];

                        if (value !== undefined && value !== null) {
                            return value;
                        } else {
                            console.warn(`Placeholder {${plainKey}} is missing a value.`);
                            return match; // leave original placeholder
                        }
                    });

                    $('#livemeetingdata').summernote('code', content);

                } catch (err) {
                    console.error("Template parse error:", err);
                }
            });
        });
    </script>

    <script type="text/javascript">
        $("#sendnoticeForm").validate({
            rules: {
                docType: {
                    required: true,
                },
                tempType: {
                    required: true,
                },
            },
            messages: {
                docType: {
                    required: "Please select Document Type",
                },
                tempType: {
                    required: "Please select Template Type",
                },
            },
            errorElement: 'span',
            errorClass: 'invalid-feedback',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
    {{-- <script>
        // $('#tempType').on('change', function() {
                //     const selectedOption = $(this).find('option:selected');
                //     const rawFormat = decodeURIComponent(selectedOption.data('format') || '');
                //     const cleanedFormat = rawFormat.replace(/<(\/?)(b|font|span)[^>]*>/gi, '');

                //     try {
                //         if (!flattenedCaseData) {
                //             console.error("flattenedCaseData is not available");
                //             return;
                //         }

                //         // Replace placeholders in the template
                //         const content = cleanedFormat.replace(/\{\{([^}]+)\}\}/g, function(match, key) {
                //             // Normalize key
                //             let normalizedKey = key
                //                 .replace(/&nbsp;/gi, ' ')
                //                 .replace(/\s+/g, ' ')
                //                 .trim()
                //                 .toLowerCase()
                //                 .replace(/[^a-z0-9]/g, '_');

                //             const value = flattenedCaseData[normalizedKey];

                //             if (value !== undefined && value !== null) {
                //                 return value;
                //             } else {
                //                 console.warn(`Placeholder {${key}} is missing a value.`);
                //                 return `[Missing: ${key}]`;
                //             }
                //         }); 

                //         // Set the content in the Summernote editor
                //         $('#livemeetingdata').summernote('code', content);

                //     } catch (err) {
                //         console.error("Template parse error:", err);
                //     }
                // });
    </script> --}}
@endsection
