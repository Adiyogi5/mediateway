@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row flex-between-end">
                        <div class="col-auto align-self-center">
                            <h5 class="mb-0" id="table-example">DRP :: DRP List </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive scrollbar">
                        <table id="zero-config" class="table custom-table table-striped fs--1 mb-0 table-datatable"
                            style="width:100%">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th>Drp Type</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
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

    <!-- View DRP Detail Modal -->
    <div class="modal fade" id="drpDetailModal" tabindex="-1" aria-labelledby="drpDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="drpDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" id="drp-loading" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <!-- Dynamic content will be injected here -->
                </div>
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
                ajax: "{{ route('drplist.index') }}",
                order: [
                    [4, 'desc']
                ],
                columns: [
                    { data: 'drp_type', name: 'drp_type' },
                    { data: 'name', name: 'name' },
                    { data: 'mobile', name: 'mobile' }, 
                    { data: 'email', name: 'email' }, 
                    { data: 'created_at', name: 'created_at' },
                    { data: 'approve_status', name: 'approve_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
    <script>
        $(document).on('click', '.view-detail', function() {
        let drpId = $(this).data('id');
        let $modal = $('#drpDetailModal');
        let $content = $('#drpDetailContent');
        let $loading = $('#drp-loading');

        const storageBaseUrl = "{{ asset('storage') }}/";
        const caseTypes = @json(config('constant.case_type'));

        $content.html('');
        $loading.show();
        $modal.modal('show');

        $.ajax({
           url: "{{ url('drplist/drp-detail') }}/" + drpId,
            type: "GET",
            success: function(response) {
                $loading.hide();
                if (response.status) {
                    let drp = response.data.drp;
                    let drpDetails = response.data.drp_detail;

                    let html = `
                        <h6 class="text-white bg-dark text-center py-1">DRP Info</h6>
                        <div class="row mb-3">
                            <div class="col-md-6 col-12"><span><strong>DRP Type : </strong> </span> <span>${ caseTypes[drp.drp_type] || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Name : </strong> </span> <span> ${drp.name || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Mobile : </strong> ${drp.mobile || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Email : </strong> ${drp.email || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Approve Status : </strong> 
                                ${drp.approve_status == 1 
                                    ? 'Approved' 
                                    : (drp.approve_status == 2 
                                        ? 'Rejected' 
                                        : 'Pending')}
                            </span></div>
                            <div class="col-md-6 col-12"><span><strong>DOB : </strong> ${drp.dob || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Nationality : </strong> ${drp.nationality || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Gender : </strong> ${drp.gender || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>State : </strong> ${drp.state_name || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>City : </strong> ${drp.city_name || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Pincode : </strong> ${drp.pincode || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Address Line 1 : </strong> ${drp.address1 || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Address Line 2 : </strong> ${drp.address2 || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Profession : </strong> ${drp.profession || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Specialization : </strong> ${drp.specialization || '--' }</span></div>
                            <div class="col-md-6 col-12">
                                <span><strong>Profile image : </strong>
                                    ${drp.image 
                                    ? `<img src="${storageBaseUrl + drp.image}" style="height:50px; width:50px">`
                                    : '--'}
                                </span>
                            </div>
                            <div class="col-md-6 col-12">
                                <span><strong>Signature : </strong> 
                                    ${drp.signature_drp 
                                    ? `<img src="${storageBaseUrl + drp.signature_drp}" style="height:50px; width:120px">`
                                    : '--'}
                                </span>
                            </div>
                        </div>
                        <h6 class="text-white bg-dark text-center py-1">DRP Detail</h6>
                        <div class="row">`;
                    if (drpDetails) {
                        html += `
                            <div class="col-md-6 col-12"><span><strong>University : </strong> </span> <span> ${drpDetails.university || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Field of Study : </strong> </span> <span> ${drpDetails.field_of_study || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Degree : </strong> </span> <span> ${drpDetails.degree || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Year : </strong> </span> <span> ${drpDetails.year || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Description : </strong> </span> <span> ${drpDetails.description || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Achievement_of Socities : </strong> </span> <span> ${drpDetails.achievement_od_socities || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Designation : </strong> </span> <span> ${drpDetails.designation || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Organization : </strong> </span> <span> ${drpDetails.organization || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Professional Degree : </strong> </span> <span> ${drpDetails.professional_degree || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Registration No : </strong> </span> <span> ${drpDetails.registration_no || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Job Description : </strong> </span> <span> ${drpDetails.job_description || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Currently Working Here : </strong> </span> <span> ${drpDetails.currently_working_here || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Years of Experience : </strong> </span> <span> ${drpDetails.years_of_experience || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Registration Certificate : </strong> </span> <span> ${drpDetails.registration_certificate || '--' }</span></div>
                            <div class="col-md-6 col-12">
                                <span><strong>Attach Registration Certificate : </strong> 
                                    ${drpDetails.attach_registration_certificate 
                                    ? `<img src="${storageBaseUrl + drpDetails.attach_registration_certificate}" style="height:50px; width:120px">`
                                    : '--'}
                                </span>
                            </div>
                            <div class="col-md-6 col-12"><span><strong>Experience in the field of Drp : </strong> </span> <span> ${drpDetails.experience_in_the_field_of_drp || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Areas of Expertise : </strong> </span> <span> ${drpDetails.areas_of_expertise || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Membership of Professional Organisation : </strong> </span> <span> ${drpDetails.membership_of_professional_organisation || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>No of Awards as Arbitrator : </strong> </span> <span> ${drpDetails.no_of_awards_as_arbitrator || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Total Years of working as Drp : </strong> </span> <span> ${drpDetails.total_years_of_working_as_drp || '--' }</span></div>
                            <div class="col-md-6 col-12"><span><strong>Functional Area of Drp : </strong> </span> <span> ${drpDetails.functional_area_of_drp || '--' }</span></div>
                        `;
                    } else {
                        html += `<span>No DRP detail available.</span>`;
                    }

                    html += `</div>`;

                    $content.html(html);
                } else {
                    $content.html('<div class="alert alert-warning">Data not found</div>');
                }
            },
            error: function() {
                $loading.hide();
                $content.html('<div class="alert alert-danger">Failed to load data.</div>');
            }
        });
    });
    </script>
    <script>
        $(document).on('click', '.approve', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to approve this DRP.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('drplist/approve') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Approved!', response.message, 'success');
                                $('.table-datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.reject', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to reject this DRP.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('drplist/reject') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Rejected!', response.message, 'success');
                                $('.table-datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
