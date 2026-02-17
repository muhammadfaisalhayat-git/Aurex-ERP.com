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
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20mm;
            color: #1a1a1a;
            background-color: #fff;
            width: 210mm;
            height: 297mm;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
        }

        .header {
            margin-bottom: 30mm;
        }

        .top-company {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5mm;
            text-transform: uppercase;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 300;
            letter-spacing: 5px;
            color: #999;
            margin-bottom: 5mm;
        }

        .main-title {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ccc;
            margin-bottom: 20mm;
        }

        .content {
            font-size: 22px;
            line-height: 1.6;
            font-weight: 300;
            margin-bottom: 40mm;
        }

        .highlight {
            font-weight: 500;
            border-bottom: 1px solid #1a1a1a;
        }

        .footer {
            position: absolute;
            bottom: 20mm;
            left: 20mm;
            right: 20mm;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            width: 33%;
            vertical-align: top;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5mm;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="top-company">{{ $employee->company?->name ?? config('app.name') }}</div>
        <div class="logo-text">OFFICIAL DOCUMENT</div>
        @if($employee->company?->logo)
            <img src="{{ public_path('storage/' . $employee->company->logo) }}" alt="Logo"
                style="max-height: 40px; margin-bottom: 5mm;">
        @endif
        <div style="font-size: 11px; color: #999;">{{ $employee->company?->address }} |
            {{ $employee->company?->contact_email }}</div>
    </div>

    <div class="main-title">Experience Confirmation</div>

    <div class="content">
        We hereby certify that <span class="highlight">{{ $employee->name }}</span> was employed as
        <span class="highlight">{{ $employee->designation?->name ?? 'Employee' }}</span>
        within our <span class="highlight">{{ $employee->department?->name ?? 'Department' }}</span> operations.
        Their tenure began on <span
            class="highlight">{{ $employee->joining_date ? $employee->joining_date->format('Y-m-d') : 'N/A' }}</span>
        and concluded with an exemplary record of service.
    </div>

    <div class="footer">
        <div class="info-grid">
            <div class="info-col">
                <div class="info-label">Issued On</div>
                <div class="info-value">{{ date('F d, Y') }}</div>
            </div>
            <div class="info-col">
                <div class="info-label">Internal Ref</div>
                <div class="info-value">{{ $employee->employee_code }}</div>
            </div>
            <div class="info-col" style="text-align: right;">
                <div class="info-label">Verification</div>
                <div class="info-value">Human Resources</div>
            </div>
        </div>
    </div>
</body>

</html>