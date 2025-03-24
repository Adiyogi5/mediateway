<div>
    <p><strong>Name:</strong> {{ $callback->first_name }}</p>
    <p><strong>Mobile:</strong> {{ $callback->mobile }}</p>
    <p><strong>Call Back Date Time:</strong> {{ \Carbon\Carbon::parse($callback->datetime)->format('d M Y, h:i A') }}</p>
    <p><strong>Inquiry Date:</strong> {{ $callback->created_at->format('d M Y, h:i A') }}</p>
</div>
