<table>
    <thead>
        <tr>
            <th>Case Number</th>
            <th>Loan Number</th>
            <th>Organization Name</th>
            <th>Case Type</th>
            <th>Product Type</th>
            <th>Notice Type</th>
            <th>Notice Date</th>
            <th>Status</th>
            <th>PDF Link</th>

            <th>Type Of Communication</th>
            <th>Email Address</th>
            <th>Email Send Status</th>
            <th>Email Send Date/Time</th>
            <th>Email Delivery Status</th>
            <th>Email Bounce Time</th>

            <th>Type Of Communication</th>
            <th>WhatsApp Mobile no</th>
            <th>WhatsApp Send Status</th>
            <th>WhatsApp Send Date/Time</th>
            <th>WhatsApp Delivery Status</th>
            <th>WhatsApp Status</th>

            <th>Type Of Communication</th>
            <th>SMS Mobile no</th>
            <th>SMS Send Status</th>
            <th>SMS Send Date/Time</th>
            <th>SMS Delivery Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $emailstatusText  = ['Pending', 'Send', 'Invalid-Email'];
            $mobilestatusText  = ['Pending', 'Send', 'Invalid-Mobile'];
        @endphp
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->case_number }}</td>
                <td>{{ $row->loan_number }}</td>
                <td>{{ $row->claimant_first_name }}</td>
                <td>{{ config('constant.case_type')[$row->case_type] ?? 'Unknown' }}</td>
                <td>{{ config('constant.product_type')[$row->product_type] ?? 'Unknown' }}</td>
                <td>{{ $row->conciliation_notice_type == 1 ? 'Pre-Conciliation' : 'Conciliation' }}</td>
                <td>{{ \Carbon\Carbon::parse($row->notice_date)->format('d M Y') }}</td>
                <td>{{ $row->status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                    @if ($row->notice_copy)
                        <a href="{{ url(str_replace('\\', '/', 'storage/' . $row->notice_copy)) }}" target="_blank">
                           Click to View
                        </a>
                    @else
                        N/A
                    @endif
                </td>

                {{-- Email Section --}}
                <td>Email</td>
                <td>{{ $row->respondent_email }}</td>
                <td>{{ $emailstatusText[$row->email_status ?? 0] }}</td>
                <td>
                    {{ $row->notice_send_date ? \Carbon\Carbon::parse($row->notice_send_date)->format('d M Y h:i A') : '' }}
                </td>
                <td>{{ $row->email_status == 1 ? 'Delivered' : 'Un-Delivered' }}</td>
                <td>{{ $row->email_bounce_datetime ? \Carbon\Carbon::parse($row->email_bounce_datetime)->format('d M Y h:i A') : '' }}</td>

                {{-- WhatsApp Section --}}
                <td>WhatsApp</td>
                <td>{{ $row->respondent_mobile }}</td>
                <td>{{ $mobilestatusText[$row->whatsapp_notice_status ?? 0] }}</td>
                <td>
                    {{ $row->whatsapp_dispatch_datetime ? \Carbon\Carbon::parse($row->whatsapp_dispatch_datetime)->format('d M Y h:i A') : '' }}
                </td>
                <td>{{ $row->whatsapp_bounce_datetime ? \Carbon\Carbon::parse($row->whatsapp_bounce_datetime)->format('d M Y h:i A') : '' }}</td>
                <td>{{ $row->whatsapp_notice_status == 1 ? 'Seen' : 'UnSeen' }}</td>

                {{-- SMS Section --}}
                <td>SMS</td>
                <td>{{ $row->respondent_mobile }}</td>
                <td>{{ $mobilestatusText[$row->sms_status ?? 0] }}</td>
                <td>
                    {{ $row->sms_send_date ? \Carbon\Carbon::parse($row->sms_send_date)->format('d M Y h:i A') : '' }}
                </td>
                <td>{{ $row->sms_status == 1 ? 'Delivered' : 'Un-Delivered' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
