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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            width: 210mm;
            height: 296mm;
            overflow: hidden;
            box-sizing: border-box;
        }

        .page-border {
            border: 15mm solid #2563eb;
            height: 296mm;
            padding: 2px;
            box-sizing: border-box;
        }

        .inner-border {
            border: 2px solid #1e3a8a;
            height: 100%;
            padding: 15mm;
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: right;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 5mm;
            margin-bottom: 10mm;
        }

        .top-company {
            font-size: 24px;
            font-weight: 900;
            color: #1e3a8a;
            text-align: left;
            margin-bottom: 5mm;
        }

        .logo-box {
            float: left;
        }

        .company-info {
            font-size: 11px;
            color: #666;
        }

        .title-box {
            text-align: center;
            margin: 10mm 0;
        }

        .title {
            font-size: 38px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 16px;
            color: #2563eb;
            margin-top: 2mm;
            letter-spacing: 2px;
        }

        .content {
            font-size: 18px;
            line-height: 1.8;
            margin: 10mm 0;
            text-align: justify;
        }

        .highlight {
            color: #1e3a8a;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }

        .signature-section {
            float: right;
            text-align: center;
            width: 220px;
        }

        .signature-line {
            border-top: 2px solid #1e3a8a;
            margin-bottom: 5mm;
        }

        .date-section {
            float: left;
            margin-top: 15mm;
            font-weight: bold;
            color: #2563eb;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="page-border">
        <div class="inner-border">
            <div class="top-company">
                {{ strtoupper($employee->company?->name_en ?? config('app.name')) }}
                @if($employee->company_name_ar_reshaped)
                    <div style="font-size: 18px; margin-top: 5px;">{{ $employee->company_name_ar_reshaped }}</div>
                @endif
            </div>
            <div class="header">
                <div class="logo-box">
                    @if($employee->company?->logo)
                        <img src="{{ public_path('storage/' . $employee->company->logo) }}" alt="Logo"
                            style="max-height: 50px;">
                    @endif
                </div>
                <div class="company-info">
                    @if($employee->company?->address) {{ $employee->company->address }} <br> @endif
                    @if($employee->company?->contact_phone) Tel: {{ $employee->company->contact_phone }} @endif
                    @if($employee->company?->contact_email) | {{ $employee->company->contact_email }} @endif
                </div>
                <div class="clear"></div>
            </div>

            <div class="title-box">
                <div class="title">Certificate</div>
                <div class="sub-title">OF EXPERIENCE</div>
            </div>

            <div class="content">
                <p>This document serves as an official confirmation that <span
                        class="highlight">{{ strtoupper($employee->name ?: ($employee->first_name_en . ' ' . $employee->last_name_en)) }}</span>
                    (Code: {{ $employee->employee_code ?: 'N/A' }}),
                    @if($employee->national_id) ID: {{ $employee->national_id }} @elseif($employee->passport_number)
                    Passport: {{ $employee->passport_number }} @endif,
                    has been a valued member of <span
                        class="highlight">{{ $employee->company?->name ?? config('app.name') }}</span>.
                </p>

                <p>Serving as <span class="highlight">{{ $employee->designation?->name ?? 'Employee' }}</span> within
                    the
                    <span class="highlight">{{ $employee->department?->name ?? 'Department' }}</span> department, their
                    tenure commenced on <span
                        class="highlight">{{ $employee->joining_date ? $employee->joining_date->format('d F Y') : 'N/A' }}</span>
                    and continued with distinction to the present date.
                </p>

                <p>Throughout their time with us, {{ $employee->gender === 'female' ? 'she' : 'he' }} proved to be a
                    dedicated, resourceful, and highly professional individual.
                    {{ $employee->gender === 'female' ? 'Her' : 'His' }}
                    contributions have significantly benefited our operations and team culture.
                </p>
                <p>We wish them all the very best for their future endeavors.</p>
            </div>

            <div class="footer">
                <div class="date-section">Issued on: {{ date('d-m-Y') }}</div>
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="fw-bold">HR Director</div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</body>

</html>