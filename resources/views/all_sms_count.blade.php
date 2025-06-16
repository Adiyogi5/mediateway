@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <table id="smsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Credited</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($total_sms_count as $index => $sms_count)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sms_count->created_at)->format('d M Y') }}</td>
                                    <td>{{ $sms_count->credited }}</td>
                                    <td>{{ $sms_count->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
