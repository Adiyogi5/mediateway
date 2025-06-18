@extends('layouts.front')

<link href="{{ asset('assets/css/light/main.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/light/waves.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/fontawesome-pro/css/all.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/dt-global_style.min.css') }}" rel="stylesheet" type="text/css" />
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
                <div class="card mb-3 card-inner">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="row flex-between-end">
                                        <div class="col-auto align-self-center">
                                            <h5 class="mb-0" id="table-example">Staff Roles :: Staff Roles List </h5>
                                        </div>
                                        <div class="col-auto ms-auto">

                                            <div class="nav nav-pills nav-pills-falcon">
                                                <button class="btn btn-outline-secondary add"> <i
                                                        class="fa fa-plus me-1"></i> Add
                                                    Staff Role</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body table-padding">
                                    <div class="table-responsive scrollbar">
                                        <table
                                            class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                                            style="width:100%">
                                            <thead class="bg-200 text-900">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th width="100px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content position-relative">
                                <form id="addForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tabsModalLabel">Add Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="d-none">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="p-3">
                                            <div class="mb-3">
                                                <label class="col-form-label" for="name">Role Name :</label>
                                                <input class="form-control" name="name" id="name" type="text" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="status">Status</label>
                                                <select name="status" class="form-select" id="status">
                                                    <option value="1"> Active</option>
                                                    <option value="0"> Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-light-dark" type="button"
                                            data-bs-dismiss="modal">Discard</button>
                                        <button class="btn btn-secondary" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content position-relative">
                                <form id="editForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tabsModalLabel">Edit Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="d-none">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="p-3">

                                            <div class="mb-3">
                                                <label class="col-form-label" for="name">Role Name :</label>
                                                <input class="form-control" name="name" id="name"
                                                    type="text" />
                                                <input class="form-control" name="id" id=""
                                                    type="hidden" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="status">Status</label>
                                                <select name="status" class="form-select" id="status">
                                                    <option value="1"> Active</option>
                                                    <option value="0"> Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-light-dark" type="button"
                                            data-bs-dismiss="modal">Discard</button>
                                        <button class="btn btn-secondary" type="submit">Submit</button>
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
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/js/custom-methods.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/waves.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    
    @if (session('showProfilePopup') || isset($showProfilePopup))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Profile Incomplete!",
                    text: "Please complete your profile before proceeding.",
                    icon: "warning",
                    confirmButtonText: "Update Now",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showCancelButton: false,
                    showCloseButton: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('organization.profile') }}";
                    }
                });
            });
        </script>
    @endif
    <script type="text/javascript">
        $(function() {
            var table = $('.table-datatable').DataTable({
                ajax: "{{ route('organization.staffroles') }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


            $('.add').on('click', function() {
                $('#addModal').modal('show');
            })

            $("#addForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    }
                },
                messages: {
                    name: {
                        required: "Please enter name",
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: "{{ route('organization.staffroles') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data?.message);
                                $('#addModal').modal('hide');
                                $(form).trigger("reset")
                                table.draw();
                            } else {
                                $(form).validate().showErrors(data.data);
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });


            $(document).on('click', ".edit", function() {
                var data = $(this).data('all')
                $('[name="id"]').val(data.id)
                document.forms['editForm']['name'].value = data.name;
                document.forms['editForm']['status'].value = data.status;
                $('#editModal').modal('show');
            })

            $("#editForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    }
                },
                messages: {
                    name: {
                        required: "Please enter name",
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    const formDataObj = {};
                    formData.forEach((value, key) => (formDataObj[key] = value));
                    $.ajax({
                        url: "{{ route('organization.staffroles') }}",
                        data: formDataObj,
                        type: 'PUT',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data?.message);
                                $('#editModal').modal('hide');
                                table.draw();
                            } else {
                                $(form).validate().showErrors(data.data);
                                toastr.error(data?.message);
                            }
                        }
                    });
                }
            });

            $(document).on('click', ".delete", function() {
                var id = $(this).data('id')
                Swal.fire(deleteMessageSwalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('organization.staffroles') }} ",
                            data: {
                                'id': id
                            },
                            type: 'DELETE',
                            success: function(data) {
                                if (data.status) {
                                    Swal.fire('', data?.message, "success")
                                    table.draw();
                                } else {
                                    toastr.error(data?.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
