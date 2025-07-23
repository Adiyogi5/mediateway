@extends('layouts.front')

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
                <div class="card-inner form-validate">
                    <div class="col-md-12 col-12 mb-3">
                        <span class="d-flex justify-content-between align-item-center">
                            <b class="text-success my-auto">Import from a XLS, XLSX spreadsheet file</b>
                            <a href="{{ route('organization.cases.filecaseview') }}" class="btn btn-outline-secondary py-1">
                                <i class="fa fa-list me-1"></i>
                                File Case List
                            </a>
                        </span>

                        <span class="help">Spreadsheet can have Case File Data. </span>
                        <span class="help">Do an Export first to see the exact format of the
                            worksheets!</span>
                        <b class="text-danger">Please Read Instructions Before Uploading Sheet</b><br>
                        <b class="text-danger">Important in Sheet***</b>
                        <span class="help">1. First you have to download the sheet based on the Case File Data. Then
                            modified this according to your requriment and upload the sheet.</span>
                        <span class="help">2. When you write an entry in an Excel Sheet, always write the
                            <b>Date</b> in <b>Date Format</b>.</span>
                        <span class="help">3. All <b>HEADS</b> of <b>Excel Sheet</b> and All <b>HEADS</b>
                            of <b>Notice Master</b> should Always be Same.</span>
                        <span class="help">4. <b>Case Type</b> , <b>Language</b> and <b>Both Address Type</b> should
                            always
                            have the <b>ID</b> written in the Excel Sheet. (According to Given Instruction)</span>
                        <span class="help">5. <b>DATE FROMAT</b> should be in DD-MM-YYYY</span>
                    </div>


                    <div
                        class="col-12 col-md-12 my-3 p-lg-3 p-2 border border-2 d-flex justify-content-between item-align-self">
                        <div class="my-auto">
                            <span style="border-radius: 50px; background-color:#ffb53f; padding:5px 10px;">1.</span>
                            Case Sample Excel File Format for download
                        </div>

                        <div>
                            <a href="{{ route('organization.cases.filecase.sample') }}"
                                class="btn btn-warning py-1">Download</a>
                        </div>
                    </div>


                    <div class="col-12 col-md-12 mt-3 p-lg-3 p-2 border border-2 d-grid item-align-self">
                        <div class="my-auto">
                            <span style="border-radius: 50px; background-color:#ffb53f; padding:5px 10px;">2.</span>
                            If You Want to File Multiple Cases Using Excel File (Allowed Type .xlsx Only)
                        </div>
                        <form id="submitfileCases" action="{{ route('organization.cases.filecases.import') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex justify-content-around align-items-center mt-3">
                                <input type="file" name="file" id="fileInput">
                                <button class="btn btn-dark py-1 px-4" type="submit" id="submitBtn">Submit Cases</button>
                            </div>
                        </form>
                    </div>

                    <!-- Loader Overlay -->
                    <div id="loader-overlay">
                        <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-center mt-2 fw-bold">Importing... Please wait</div>
                    </div>

                    <style>
                        #loader-overlay {
                            display: none;
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.6); /* semi-transparent black */
                            z-index: 9999;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.2rem;
                            color: #fff; /* white text */
                            flex-direction: column;
                        }
                    </style>


                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();

                let fileInput = document.getElementById('fileInput');
                let filePath = fileInput.value;
                let allowedExtensions = /(\.xls|\.xlsx)$/i;

                if (!filePath) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No file selected!',
                        text: 'Please select an Excel file before submitting.',
                    });
                    return;
                }

                if (!allowedExtensions.exec(filePath)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type!',
                        text: 'Only .xls and .xlsx files are allowed.',
                    });
                    fileInput.value = ''; // Clear the input
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to submit the cases?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Submit!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loader-overlay').css('display', 'flex');
                        // ✅ Submit the form after showing loader
                        $('#submitfileCases').off(
                        'submit'); // Remove any previous handler to avoid duplicate call
                        $('#submitfileCases').submit();
                    }
                });
            });
        });

        // ✅ jQuery Validation for File Input
        $("#submitfileCases").validate({
            rules: {
                file: {
                    required: true,
                    extension: "xls|xlsx"
                },
            },
            messages: {
                file: {
                    required: "Please upload an Excel file.",
                    extension: "Only .xls and .xlsx files are allowed."
                },
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
                error.css("color", "red"); // Make error message red
            }
        });
    </script>
@endsection
