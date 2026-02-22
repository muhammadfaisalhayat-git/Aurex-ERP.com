<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Verdana', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            width: 210mm;
            height: 296mm;
            overflow: hidden;
            box-sizing: border-box;
        }

        .page {
            background-color: #fff;
            width: 210mm;
            height: 296mm;
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
        }

        .side-accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 10mm;
            background-color: #333;
        }

        .top-accent {
            height: 3mm;
            background: linear-gradient(to right, #333, #666);
        }

        .main-container {
            padding: 20mm 20mm 20mm 30mm;
            height: 100%;
            box-sizing: border-box;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 20mm;
            border-bottom: 2px solid #333;
            padding-bottom: 5mm;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            color: #666;
            font-size: 12px;
        }

        .top-company {
            font-size: 30px;
            font-weight: bold;
            color: #333;
            letter-spacing: -1px;
            margin-bottom: 5mm;
        }

        .cert-title {
            font-size: 42px;
            font-weight: bold;
            color: #333;
            margin: 15mm 0;
            text-transform: uppercase;
        }

        .content {
            font-size: 18px;
            line-height: 2;
            color: #444;
            margin: 20mm 0;
        }

        .data-row {
            margin-bottom: 5mm;
        }

        .label {
            display: inline-block;
            width: 50mm;
            color: #888;
            font-size: 14px;
            text-transform: uppercase;
        }

        .value {
            font-weight: bold;
            color: #333;
        }

        .footer {
            position: absolute;
            bottom: 20mm;
            left: 30mm;
            right: 20mm;
        }

        .signature-box {
            float: right;
            text-align: center;
        }

        .seal {
            float: left;
            width: 25mm;
            height: 25mm;
            border: 2px dashed #999;
            border-radius: 50%;
            line-height: 25mm;
            text-align: center;
            color: #999;
            font-size: 10px;
            transform: rotate(-15deg);
        }

        .clear {
            clear: both;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 55%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: #fcfcfc;
            z-index: -1;
            white-space: nowrap;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="side-accent"></div>
        <div class="top-accent"></div>
        <div class="watermark">CERTIFIED</div>
        <div class="main-container">
            <div class="top-company">
                {{ strtoupper($employee->company?->name_en ?? config('app.name')) }}
                @if($employee->company_name_ar_reshaped)
                    <div style="font-size: 18px; margin-top: 5px; font-family: 'DejaVu Sans', sans-serif;">
                        {{ $employee->company_name_ar_reshaped }}</div>
                @endif
            </div>
            <div class="header">
                <div class="header-left">
                    @if(isset($logoBase64) && $logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 40px;">
                    @elseif($employee->company?->logo)
                        <img src="{{ public_path('storage/' . $employee->company->logo) }}" alt="Logo"
                            style="max-height: 40px;">
                    @else
                        <div style="font-size: 14px; font-weight: bold; color: #666;">EXPERIENCE RECORD</div>
                    @endif
                </div>
                <div class="header-right">REF: {{ $employee->employee_code }}<br>DATE: {{ date('Y/m/d') }}</div>
            </div>

            <div class="cert-title">Service Record</div>

            <div class="content">
                <div class="data-row"><span class="label">Employee Name</span><span
                        class="value">{{ strtoupper($employee->name) }}</span></div>
                <div class="data-row"><span class="label">Official Designation</span><span
                        class="value">{{ $employee->designation?->name ?? 'Staff' }}</span></div>
                <div class="data-row"><span class="label">Department</span><span
                        class="value">{{ $employee->department?->name ?? 'Operations' }}</span></div>
                <div class="data-row"><span class="label">Period of Service</span><span
                        class="value">{{ $employee->joining_date ? $employee->joining_date->format('M Y') : 'N/A' }} —
                        Present</span></div>
                <p style="margin-top: 10mm;">This executive summary confirms the employee's active status and
                    professional contributions to {{ $employee->company?->name ?? config('app.name') }}. During the
                    period of employment, substantial progress and dedication were demonstrated in all assigned
                    responsibilities.</p>
            </div>

            <div class="footer">
                <div class="seal">OFFICIAL SEAL</div>
                <div class="signature-box">
                    <div style="font-weight: bold; text-decoration: underline;">MANAGEMENT APPROVAL</div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">Human Resources Department</div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</body>

</html>