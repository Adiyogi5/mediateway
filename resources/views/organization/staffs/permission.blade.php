@extends('layouts.front')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/light/forms/switches.css') }}">
    <style>
        .custom-table tbody td {
            text-align: center;
        }

        .custom-table tbody td .form-group {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table thead tr th {
            text-align: center;
            padding-top: 15px;
            padding-bottom: 15px;
        }
    </style>
@endsection

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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row flex-between-end">
                                    <div class="col-auto align-self-center">
                                        <h5 class="mb-0">Staffs :: Permission -
                                            <span class="text-primary">{{ $organization['name'] }}</span>
                                        </h5>
                                    </div>
                                    <div class="col-auto ms-auto">
                                        <div class="nav nav-pills nav-pills-falcon">
                                            <a class="btn btn-outline-secondary add"
                                                href="{{ route('organization.staffs') }}"> <i
                                                    class="fa fa-arrow-left me-1"></i> Go Back</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive scrollbar">
                                    <table class="table custom-table table-striped fs--1 mb-0" style="width:100%">
                                        <thead class="bg-200 text-900">
                                            <tr class="py-2">
                                                <th class="text-start">Module Name</th>
                                                <th class="my-2">Allow All</th>
                                                <th>Can View</th>
                                                <th>Can Add</th>
                                                <th>Can Edit</th>
                                                <th>Can Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($permissions as $permission)
                                                <tr>
                                                    <td class="text-start">
                                                        <b class="fs--1">{{ $permission['name'] }}</b>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div
                                                                class="switch form-switch-custom switch-inline form-switch-primary">
                                                                <input data-type="allow_all"
                                                                    data-module-id="{{ $permission['module_id'] }}"
                                                                    class="switch-input" type="checkbox" role="switch"
                                                                    {{ $permission['allow_all'] == 1 ? 'checked' : '' }} />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div
                                                                class="switch form-switch-custom switch-inline form-switch-secondary">
                                                                <input data-type="can_view"
                                                                    data-module-id="{{ $permission['module_id'] }}"
                                                                    class="switch-input" type="checkbox" role="switch"
                                                                    {{ $permission['can_view'] == 1 ? 'checked' : '' }} />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div
                                                                class="switch form-switch-custom switch-inline form-switch-success">
                                                                <input data-type="can_add"
                                                                    data-module-id="{{ $permission['module_id'] }}"
                                                                    class="switch-input" type="checkbox" role="switch"
                                                                    {{ $permission['can_add'] == 1 ? 'checked' : '' }} />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div
                                                                class="switch form-switch-custom switch-inline form-switch-warning">
                                                                <input data-type="can_edit"
                                                                    data-module-id="{{ $permission['module_id'] }}"
                                                                    class="switch-input" type="checkbox" role="switch"
                                                                    {{ $permission['can_edit'] == 1 ? 'checked' : '' }} />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div
                                                                class="switch form-switch-custom switch-inline form-switch-danger">
                                                                <input data-type="can_delete"
                                                                    data-module-id="{{ $permission['module_id'] }}"
                                                                    class="switch-input" type="checkbox" role="switch"
                                                                    {{ $permission['can_delete'] == 1 ? 'checked' : '' }} />
                                                            </div>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script type="text/javascript">
        $(function() {
            $(document).on('click', ".switch-input", function() {
                let checkBoxes = $(this);
                let type = checkBoxes.data('type');
                let module_id = checkBoxes.data('module-id');
                let organization_id = "{{ $organization['id'] }}";

                $.ajax({
                    url: "{{ route('organization.staffs.permission.update') }}",
                    method: "PUT",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        type: type,
                        module_id: module_id,
                        organization_id: organization_id,
                    },
                    success: function(response) {
                        if (response) {
                            toastr.success("Permission Updated Successfully");
                        } else {
                            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                            toastr.error("Oops.. There is some error.");
                        }
                    },
                    error: function() {
                        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                        toastr.error("Failed to update permission.");
                    }
                });
            });
        });
    </script>
@endsection
