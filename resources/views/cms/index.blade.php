@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Cms :: Cms List </h5>
            </div>
            {{-- <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon">
                    @if(Helper::userCan(104, 'can_add'))
                    <a href="{{ route('cms.add') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-plus me-1"></i>
                        Add Cms
                    </a>
                    @endif
                </div>
            </div> --}}
        </div>
    </div>
    <div class="card-body table-padding">
        <div class="table-responsive scrollbar">
            <table class="table custom-table table-striped dt-table-hover fs--1 mb-0 table-datatable"
                style="width:100%">
                <thead class="bg-200 text-900">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created Date</th>
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
        var table = $('.table-datatable').DataTable({
            ajax: "{{ route('cms') }}",
            order: [
                [3, 'desc']
            ],
            columns: [
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });
        $(document).on('click', ".delete", function () {
            var id = $(this).data('id')
            Swal.fire(deleteMessageSwalConfig).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('cms') }} ",
                        data: { 'id': id },
                        type: 'DELETE',
                        success: function (data) {
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