<div>
    <p><strong>Name:</strong> {{ $bookappointment->name }}</p>
    <p><strong>Mobile:</strong> {{ $bookappointment->mobile }}</p>
    <p><strong>Email:</strong> {{ $bookappointment->email }}</p>
    <p><strong>Appointment Booking Start Date:</strong> {{ $bookappointment->datestart }}</p>
    <p><strong>Appointment Booking End Date:</strong> {{ $bookappointment->dateend }}</p>
    <p><strong>Inquiry On:</strong> {{ $bookappointment->created_at->format('d M Y, h:i A') }}</p>
</div>
