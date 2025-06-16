@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Notices :: Notices List </h5>
            </div>
            {{-- <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(Helper::userCan(111, 'can_add'))
                    <a href="{{ route('noticetemplate.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Notice
                    </a>
                    @endif
                </div>
            </div> --}}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped fs--1 mb-0 table-datatable" style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>#</th>
                        <th>Notice Name</th>
                        <th>Status</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        // Set up global AJAX CSRF token
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
        });

        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('noticetemplate') }}",
            order: [[0, 'asc']],
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Delete Action with SweetAlert2
        $(document).on('click', ".delete", function () {
            var id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('noticetemplate.delete') }}",
                        type: "DELETE",
                        data: { id: id },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire("Deleted!", response.message, "success");
                                table.ajax.reload(); // Refresh DataTable
                            } else {
                                Swal.fire("Error!", response.message, "error");
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText); // Debugging
                            Swal.fire("Oops!", "Something went wrong!", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
