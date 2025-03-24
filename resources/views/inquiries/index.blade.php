@extends('layouts.app')

@section('content')
	<div class="card mb-3">
		<div class="card-header">
			<h5 class="mb-0">Contact Us :: Contact Us List</h5>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped fs--1 mb-0 table-datatable" style="width:100%">
					<thead class="bg-200 text-900">
						<tr>
							<th>Name</th>
							<th>Mobile</th>
							<th>Email</th>
							<th>Inquiry On</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Inquiry Modal -->
	<div class="modal fade" id="inquiryModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title mb-0 fw-bold">Inquiry Details</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" id="inquiryContent"></div>
			</div>
		</div>
	</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var tableObj = $('.table-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('inquiries') }}",
            order: [[3, 'desc']],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'mobile', name: 'mobile' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', searchable: false, orderable: false }
            ]
        });

        // View Inquiry Modal
        $('body').on('click', '.view_inquiry', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('inquiries') }}/" + id,
                method: "GET",
                success: function(result) {
                    $('#inquiryContent').html(result);
                    $('#inquiryModal').modal('show');
                }
            });
        });

        // Delete Inquiry with SweetAlert Confirmation
        $('body').on('click', '.delete_record', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('inquiries') }}/" + id,
                        method: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Deleted!", "Data has been deleted successfully", "success");
                                tableObj.ajax.reload(null, false);
                            } else {
                                Swal.fire("Error!", "Failed to delete data", "error");
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
