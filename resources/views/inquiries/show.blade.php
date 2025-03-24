<div>
    <p><strong>Name:</strong> {{ $inquiry->first_name }}</p>
    <p><strong>Mobile:</strong> {{ $inquiry->mobile }}</p>
    <p><strong>Email:</strong> {{ $inquiry->email }}</p>
    <p><strong>Message:</strong> {{ $inquiry->message }}</p>
    <p><strong>Inquiry Date:</strong> {{ $inquiry->created_at->format('d M Y, h:i A') }}</p>
</div>
