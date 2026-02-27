@extends('layouts.print')

@section('title', __('messages.appointment_details'))
@section('report_title', __('messages.appointment_details'))

@section('content')
<table>
    <tr>
        <th>{{ __('messages.appointment_code') }}</th>
        <td>{{ $appointment->code }}</td>
        <th>{{ __('messages.date') }}</th>
        <td>{{ $appointment->appointment_date }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.patient') }}</th>
        <td>{{ $appointment->patient->name_en }} ({{ $appointment->patient->code }})</td>
        <th>{{ __('messages.doctor') }}</th>
        <td>{{ $appointment->doctor->name_en }} ({{ $appointment->doctor->specialization }})</td>
    </tr>
    <tr>
        <th>{{ __('messages.service') }}</th>
        <td>{{ $appointment->service->name_en }}</td>
        <th>{{ __('messages.total_amount') }}</th>
        <td>{{ number_format($appointment->total_amount, 2) }}</td>
    </tr>
    <tr>
        <th>{{ __('messages.status') }}</th>
        <td>{{ strtoupper($appointment->status) }}</td>
        <th>{{ __('messages.billing_status') }}</th>
        <td>{{ strtoupper($appointment->billing_status) }}</td>
    </tr>
</table>

<div style="margin-top: 30px;">
    <strong>{{ __('messages.notes') }}:</strong>
    <p>{{ $appointment->notes ?? __('messages.no_notes') }}</p>
</div>
@endsection
