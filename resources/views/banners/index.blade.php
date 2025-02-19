@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">

                    <div class="row flex-between-end">
                        <div class="col-auto align-self-center">
                            <h5 class="mb-0" id="table-example">Banner :: Banners List </h5>
                        </div>
                        <div class="col-auto ms-auto">
                            @if (Helper::userCan(112, 'can_add'))
                                <div class="nav nav-pills nav-pills-falcon">
                                    <button class="btn btn-outline-secondary add">
                                        <i class="fa fa-plus me-1"></i> Add Banner</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive scrollbar">
                        <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                            style="width:100%">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th>Image</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" State="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content position-relative">
                <form id="addForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Add Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="image">Image :</label>
                                <input class="form-control" name="image" id="image" type="file" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="url">URL</label>
                                <input class="form-control" id="url" placeholder="Url" name="url" type="text"
                                    value="" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Discard</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content position-relative">
                <form id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Edit Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="image">Image :</label>
                                <input type="hidden" name="id" value="">
                                <input class="form-control" name="image" id="image" type="file" />
                                <img class="viewImg img-thumbnail" src="" alt=""
                                    style="width: 150px; max-height: 50px;">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="url">URL</label>
                                <input class="form-control" id="url" placeholder="Url" name="url"
                                    type="text" value="" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Discard</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            var base_url = "{{ asset('storage') }}/";
            var table = $('.table-datatable').DataTable({
                ajax: "{{ request()->url() }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                    }
                ]
            });

            $('.add').on('click', function() {
                $('#addModal').modal('show');
            })

            $("#addForm").validate({
                debug: false,
                errorClass: "text-danger fs--1",
                errorElement: "span",
                rules: {
                    image: {
                        required: true,
                        extension: "jpg|jpeg|png|mp4",
                        filesize: 2048
                    },
                    url: {
                        url: true
                    },
                    status: {
                        required: true,
                    },
                },
                messages: {
                    image: {
                        required: "Please select image file.",
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: "{{ request()->url() }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data.message);
                                $('#addModal').modal('hide');
                                $(form).trigger("reset")
                                table.draw();
                            } else {
                                $(form).validate().showErrors(data.data);
                                toastr.error(data.message);
                            }
                        }
                    });
                },
            });

            $(document).on('click', ".edit", function() {
                var data = $(this).data('all')
                $('[name="id"]').val(data.id)
                $('#editForm .viewImg').attr('src', base_url + data.image)
                document.forms['editForm']['url'].value = data.url;
                document.forms['editForm']['status'].value = data.status;
                $('#editModal').modal('show');
            })

            $("#editForm").validate({
                debug: false,
                errorClass: "text-danger fs--1",
                errorElement: "span",
                rules: {
                    image: {
                        extension: "jpg|jpeg|png|mp4",
                        filesize: 2048
                    },
                    status: {
                        required: true,
                    },
                    url: {
                        url: true
                    }
                },
                messages: {
                    image: {
                        required: "Please select image file.",
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $("#overlay").show();
                    formData.append('_method', 'PUT')
                    $.ajax({
                        url: "{{ request()->url() }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data.message);
                                $('#editModal').modal('hide');
                                table.draw();
                            } else {
                                $(form).validate().showErrors(data.data);
                                toastr.error(data.message);
                            }
                        }
                    });
                },
            });

            $(document).on('click', ".delete", function() {
                var id = $(this).data('id')
                Swal.fire(deleteMessageSwalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ request()->url() }}",
                            data: {
                                'id': id
                            },
                            type: 'DELETE',
                            success: function(data) {
                                if (data.status) {
                                    Swal.fire('', data?.message, "success")
                                    table.draw();
                                } else {
                                    toastr.error(data.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
