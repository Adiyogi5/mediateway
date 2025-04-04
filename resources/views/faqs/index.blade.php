@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">

                    <div class="row flex-between-end">
                        <div class="col-auto align-self-center">
                            <h5 class="mb-0" id="table-example">FAQs :: FAQs List </h5>
                        </div>
                        <div class="col-auto ms-auto">
                            @if (Helper::userCan(110, 'can_add'))
                                <div class="nav nav-pills nav-pills-falcon">
                                    <button class="btn btn-outline-secondary add">
                                        <i class="fa fa-plus me-1"></i> Add FAQ
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body table-padding">
                    <div class="table-responsive scrollbar">
                        <table id="zero-config"
                            class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                            style="width:100%">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th>Question</th>
                                    <th>Answer</th>
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

    <div class="modal fade" id="addModal" tabindex="-1" State="dialog" aria-hidden="true">
        <div class="modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content position-relative">
                <form id="addForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Add FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3">
                            <div class="mb-2">
                                <label class="col-form-label" for="question">Question :</label>
                                <textarea class="form-control" name="question" id="question"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="col-form-label" for="answer">Answer :</label>
                                <textarea class="form-control" name="answer" id="answer"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" class="form-select" id="status">
                                    <option value="1"> Active</option>
                                    <option value="0"> Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Discard</button>
                        <button class="btn btn-secondary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" State="dialog" aria-hidden="true">
        <div class="modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content position-relative">
                <form id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tabsModalLabel">Edit FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="d-none">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3">
                            <div class="mb-2">
                                <label class="col-form-label" for="question">Question :</label>
                                <textarea class="form-control" name="question" id="question"></textarea>
                                <input class="form-control" name="id" id="" type="hidden" />
                            </div>
                            <div class="mb-2">
                                <label class="col-form-label" for="answer">Answer :</label>
                                <textarea class="form-control" name="answer" id="answer"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" class="form-select" id="status">
                                    <option value="1"> Active</option>
                                    <option value="0"> Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light-dark" type="button" data-bs-dismiss="modal">Discard</button>
                        <button class="btn btn-secondary" type="submit">Submit</button>
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
            var table = $('.table-datatable').DataTable({
                ajax: "{{ request()->url() }}",
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'question',
                        name: 'question'
                    },
                    {
                        data: 'answer',
                        name: 'answer'
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
                    question: {
                        required: true,
                        minlength: 2,
                        maxlength: 200
                    },
                    answer: {
                        required: true,
                        minlength: 2,
                        maxlength: 200
                    },
                },
                messages: {
                    question: {
                        required: "Please enter question.",
                    },
                    answer: {
                        required: "Please enter answer.",
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
                document.forms['editForm']['question'].value = data.question;
                document.forms['editForm']['answer'].value = data.answer;
                document.forms['editForm']['status'].value = data.status;
                $('#editModal').modal('show');
            })

            $("#editForm").validate({
                rules: {
                    question: {
                        required: true,
                        minlength: 2,
                        maxlength: 200
                    },
                    answer: {
                        required: true,
                        minlength: 2,
                        maxlength: 200
                    },
                },
                messages: {
                    question: {
                        required: "Please enter question.",
                    },
                    answer: {
                        required: "Please enter answer.",
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    const formDataObj = {};
                    formData.forEach((value, key) => (formDataObj[key] = value));
                    $.ajax({
                        url: "{{ request()->url() }}",
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
                            url: "{{ request()->url() }} ",
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
