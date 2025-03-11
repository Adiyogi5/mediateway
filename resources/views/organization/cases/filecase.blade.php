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
                            Case File Sample file for download
                        </div>

                        <div>
                            <a href="{{ route('organization.cases.filecase.sample') }}" class="btn btn-warning py-1">Download</a>
                        </div>
                    </div>


                    <div class="col-12 col-md-12 mt-3 p-lg-3 p-2 border border-2 d-grid item-align-self">
                        <div class="my-auto">
                            <span style="border-radius: 50px; background-color:#ffb53f; padding:5px 10px;">2.</span>
                            If You Want to File Multiple Cases Using Excel SpreadSheet File (Allowed Type .xlxx Only)
                        </div>
                        <form id="submitfileCases" action="{{ route('organization.cases.filecases.import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex justify-content-around item-align-self mt-3">
                                <input type="file" name="file" id="fileInput">
                                <button class="btn btn-dark py-1 px-4" type="submit" id="submitBtn">Submit Cases</button>
                            </div>
                        </form>
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
            let fileInput = document.getElementById('fileInput');

            if (!fileInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No file selected!',
                    text: 'Please select a file before submitting.',
                });
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
                    document.getElementById('filecasesForm').submit();
                }
            });
        });
    </script>
    <script>
        $("#submitfileCases").validate({
            rules: {
                file: {
                    required: true
                },
            },
            messages: {
                file: {
                    required: "Please Upload Excel File",
                },
            },
        });
    </script>
@endsection
