@extends('layouts.front')
<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" id="user-style-default" />
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
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row flex-between-end">
                            <div class="col-auto align-self-center">
                                <h5 class="mb-0" data-anchor="data-anchor">Cases :: Bulk Update </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body form-validate">
                        <div class="d-grid mb-3">
                            <span class="d-flex justify-content-between align-item-center">
                                <b class="text-success my-auto">Import from a XLS, XLSX spreadsheet file</b>
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
                            class="col-12 col-md-12 my-4 p-lg-3 p-2 border border-2 d-flex justify-content-between item-align-self">
                            <div class="my-auto">
                                <span style="border-radius: 50px; background-color:#ffb53f; padding:5px 10px;">1.</span>
                                Case File Sample file for download
                            </div>

                            <div>
                                <a href="{{ route('drp.cases.casebulkupdate.sample') }}"
                                    class="btn btn-warning py-1">Download</a>
                            </div>
                        </div>


                        <div class="col-12 col-md-12 mt-5 p-lg-3 p-2 border border-2 d-grid item-align-self">
                            <div class="my-auto">
                                <span style="border-radius: 50px; background-color:#ffb53f; padding:5px 10px;">2.</span>
                                If You Want to File Multiple Cases Using Excel SpreadSheet File (Allowed Type .xlxx Only)
                            </div>
                            <form id="submitfileCases" action="{{ route('drp.cases.casebulkupdate.import') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex justify-content-around align-items-center mt-3">
                                    <input type="file" name="file" id="fileInput">
                                    <button class="btn btn-dark py-1 px-4" type="submit" id="submitBtn">Submit
                                        Cases</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent immediate form submission

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
                    document.getElementById('submitfileCases').submit();
                }
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
