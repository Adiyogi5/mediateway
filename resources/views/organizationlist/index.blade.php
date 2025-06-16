@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">

                    <div class="row flex-between-end">
                        <div class="col-auto align-self-center">
                            <h5 class="mb-0" id="table-example">Organizations :: Organizations List </h5>
                        </div>
                        <div class="col-auto ms-auto">
                            @if (Helper::userCan(112, 'can_add'))
                                <div class="nav nav-pills nav-pills-falcon">
                                    <button class="btn btn-outline-secondary add">
                                        <i class="fa fa-plus me-1"></i> Add Organization</button>
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
                                    <th>Name</th>
                                    <th>Code</th>
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
                     @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Add Organization</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="name">Organization Name</label>
                                <input class="form-control" id="name" placeholder="name" name="name" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="code">Organization Code</label>
                                <input class="form-control" id="code" placeholder="code" name="code" type="text"
                                    value="" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                            </div>
                            
                            <div class="col-12 text-center justify-content-center">
                                <h5 class="border-top mt-2 pt-2">Notice Send Timeline</h5>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1">Notice 1</label>
                                <input class="form-control" id="notice_1" placeholder="notice_1" name="notice_1" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1a">Notice 1A</label>
                                <input class="form-control" id="notice_1a" placeholder="notice_1a" name="notice_1a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1b">Notice 1B</label>
                                <input class="form-control" id="notice_1b" placeholder="notice_1b" name="notice_1b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_2b">Notice 2B</label>
                                <input class="form-control" id="notice_2b" placeholder="notice_2b" name="notice_2b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3a">Notice 3A</label>
                                <input class="form-control" id="notice_3a" placeholder="notice_3a" name="notice_3a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3b">Notice 3B</label>
                                <input class="form-control" id="notice_3b" placeholder="notice_3b" name="notice_3b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3c">Notice 3C</label>
                                <input class="form-control" id="notice_3c" placeholder="notice_3c" name="notice_3c" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3d">Notice 3D</label>
                                <input class="form-control" id="notice_3d" placeholder="notice_3d" name="notice_3d" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_4a">Notice 4A</label>
                                <input class="form-control" id="notice_4a" placeholder="notice_4a" name="notice_4a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_5a">Notice 5A</label>
                                <input class="form-control" id="notice_5a" placeholder="notice_5a" name="notice_5a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_second_hearing">Notice Second Hearing</label>
                                <input class="form-control" id="notice_second_hearing" placeholder="notice_second_hearing" name="notice_second_hearing" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_final_hearing">Notice Final Hearing</label>
                                <input class="form-control" id="notice_final_hearing" placeholder="notice_final_hearing" name="notice_final_hearing" type="text"
                                    value="" />
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
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Edit Organization</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" />
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="name">Organization Name</label>
                                <input class="form-control" id="name" placeholder="name" name="name" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="code">Organization Code</label>
                                <input class="form-control" id="code" placeholder="code" name="code" type="text"
                                    value="" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                            </div>
                            
                            <div class="col-12 text-center justify-content-center">
                                <h5 class="border-top mt-2 pt-2">Notice Send Timeline</h5>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1">Notice 1</label>
                                <input class="form-control" id="notice_1" placeholder="notice_1" name="notice_1" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1a">Notice 1A</label>
                                <input class="form-control" id="notice_1a" placeholder="notice_1a" name="notice_1a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_1b">Notice 1B</label>
                                <input class="form-control" id="notice_1b" placeholder="notice_1b" name="notice_1b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_2b">Notice 2B</label>
                                <input class="form-control" id="notice_2b" placeholder="notice_2b" name="notice_2b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3a">Notice 3A</label>
                                <input class="form-control" id="notice_3a" placeholder="notice_3a" name="notice_3a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3b">Notice 3B</label>
                                <input class="form-control" id="notice_3b" placeholder="notice_3b" name="notice_3b" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3c">Notice 3C</label>
                                <input class="form-control" id="notice_3c" placeholder="notice_3c" name="notice_3c" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_3d">Notice 3D</label>
                                <input class="form-control" id="notice_3d" placeholder="notice_3d" name="notice_3d" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_4a">Notice 4A</label>
                                <input class="form-control" id="notice_4a" placeholder="notice_4a" name="notice_4a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_5a">Notice 5A</label>
                                <input class="form-control" id="notice_5a" placeholder="notice_5a" name="notice_5a" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_second_hearing">Notice Second Hearing</label>
                                <input class="form-control" id="notice_second_hearing" placeholder="notice_second_hearing" name="notice_second_hearing" type="text"
                                    value="" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="notice_final_hearing">Notice Final Hearing</label>
                                <input class="form-control" id="notice_final_hearing" placeholder="notice_final_hearing" name="notice_final_hearing" type="text"
                                    value="" />
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
                ajax: "{{ route('organizationlist.index') }}",
                order: [
                    [2, 'desc']
                ],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' }, 
                    { data: 'created_at', name: 'created_at' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
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
                    name: {
                        required: true,
                    },
                    code: {
                        required: true,
                    },
                    notice_1: { required: true },
                    notice_1a: { required: true },
                    notice_1b: { required: true },
                    notice_2b: { required: true },
                    notice_3a: { required: true },
                    notice_3b: { required: true },
                    notice_3c: { required: true },
                    notice_3d: { required: true },
                    notice_4a: { required: true },
                    notice_5a: { required: true },
                    notice_second_hearing: { required: true },
                    notice_final_hearing: { required: true },
                },
                messages: {
                    name: { required: "Please enter organization name" },
                    code: { required: "Please enter organization code" },
                    notice_1: { required: "Please enter notice 1 timeline" },
                    notice_1a: { required: "Please enter notice 1A timeline" },
                    notice_1b: { required: "Please enter notice 1B timeline" },
                    notice_2b: { required: "Please enter notice 2B timeline" },
                    notice_3a: { required: "Please enter notice 3A timeline" },
                    notice_3b: { required: "Please enter notice 3B timeline" },
                    notice_3c: { required: "Please enter notice 3C timeline" },
                    notice_3d: { required: "Please enter notice 3D timeline" },
                    notice_4a: { required: "Please enter notice 4A timeline" },
                    notice_5a: { required: "Please enter notice 5A timeline" },
                    notice_second_hearing: { required: "Please enter notice Second hearing timeline" },
                    notice_final_hearing: { required: "Please enter notice final hearing timeline" },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('organizationlist.store') }}",
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

            $(document).on('click', ".edit", function () {
                var data = $(this).data('all');
                // Set values in the edit form
                $('#editForm [name="id"]').val(data.id);
                $('#editForm [name="name"]').val(data.name);
                $('#editForm [name="code"]').val(data.code);
                $('#editForm [name="status"]').val(data.status);
                 // Notice timeline fields
                $('#editForm [name="notice_1"]').val(data.notice_1);
                $('#editForm [name="notice_1a"]').val(data.notice_1a);
                $('#editForm [name="notice_1b"]').val(data.notice_1b);
                $('#editForm [name="notice_2b"]').val(data.notice_2b);
                $('#editForm [name="notice_3a"]').val(data.notice_3a);
                $('#editForm [name="notice_3b"]').val(data.notice_3b);
                $('#editForm [name="notice_3c"]').val(data.notice_3c);
                $('#editForm [name="notice_3d"]').val(data.notice_3d);
                $('#editForm [name="notice_4a"]').val(data.notice_4a);
                $('#editForm [name="notice_5a"]').val(data.notice_5a);
                $('#editForm [name="notice_second_hearing"]').val(data.notice_second_hearing);
                $('#editForm [name="notice_final_hearing"]').val(data.notice_final_hearing);
                // Show the modal
                $('#editModal').modal('show');
            });

            $("#editForm").validate({
                debug: false,
                errorClass: "text-danger fs--1",
                errorElement: "span",
                rules: {
                    name: {
                        required: true
                    },
                    code: {
                        required: true
                    },
                    status: {
                        required: true,
                    },
                    notice_1: { required: true },
                    notice_1a: { required: true },
                    notice_1b: { required: true },
                    notice_2b: { required: true },
                    notice_3a: { required: true },
                    notice_3b: { required: true },
                    notice_3c: { required: true },
                    notice_3d: { required: true },
                    notice_4a: { required: true },
                    notice_5a: { required: true },
                    notice_second_hearing: { required: true },
                    notice_final_hearing: { required: true },
                },
                messages: {
                    name: { required: "Please enter organization name" },
                    code: { required: "Please enter organization code" },
                    notice_1: { required: "Please enter notice 1 timeline" },
                    notice_1a: { required: "Please enter notice 1A timeline" },
                    notice_1b: { required: "Please enter notice 1B timeline" },
                    notice_2b: { required: "Please enter notice 2B timeline" },
                    notice_3a: { required: "Please enter notice 3A timeline" },
                    notice_3b: { required: "Please enter notice 3B timeline" },
                    notice_3c: { required: "Please enter notice 3C timeline" },
                    notice_3d: { required: "Please enter notice 3D timeline" },
                    notice_4a: { required: "Please enter notice 4A timeline" },
                    notice_5a: { required: "Please enter notice 5A timeline" },
                    notice_second_hearing: { required: "Please enter notice Second hearing timeline" },
                    notice_final_hearing: { required: "Please enter notice final hearing timeline" },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $("#overlay").show();
                    formData.append('_method', 'PUT')
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('organizationlist.update') }}",
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
                            url: "{{ route('organizationlist.destroy') }]",
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
