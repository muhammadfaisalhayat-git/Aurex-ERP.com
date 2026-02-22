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
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #2c2c2c;
            width: 210mm;
            height: 296mm;
            overflow: hidden;
            box-sizing: border-box;
        }

        .gold-frame {
            border: 12mm solid #d4af37;
            border-image: linear-gradient(to bottom right, #b8860b, #d4af37, #f7e1a0, #d4af37, #b8860b) 1;
            padding: 2mm;
            height: 296mm;
            box-sizing: border-box;
        }

        .inner-frame {
            border: 1px solid #d4af37;
            padding: 15mm;
            height: 100%;
            background-image: radial-gradient(circle, #ffffff 0%, #f9f9f9 100%);
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 12mm;
        }

        .top-company {
            font-size: 26px;
            color: #b8860b;
            font-weight: bold;
            margin-bottom: 5mm;
            text-transform: uppercase;
        }

        .logo-container {
            margin-bottom: 6mm;
        }

        .cert-title-box {
            font-size: 18px;
            color: #333;
            letter-spacing: 8px;
            margin-top: 5mm;
            border-top: 1px solid #d4af37;
            border-bottom: 1px solid #d4af37;
            display: inline-block;
            padding: 2mm 8mm;
        }

        .main-title {
            font-size: 38px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 15mm 0 8mm 0;
            text-align: center;
        }

        .recipient-name {
            font-size: 30px;
            font-style: italic;
            color: #b8860b;
            margin: 8mm 0;
            text-decoration: underline;
        }

        .content {
            font-size: 18px;
            line-height: 1.8;
            text-align: center;
            margin: 15mm auto;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }

        .date-box {
            float: left;
            text-align: left;
            font-size: 14px;
        }

        .sig-box {
            float: right;
            text-align: right;
        }

        .sig-name {
            font-size: 20px;
            font-weight: bold;
            border-top: 1px solid #d4af37;
            padding-top: 5mm;
            display: inline-block;
            min-width: 220px;
            text-align: center;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="gold-frame">
        <div class="inner-frame">
            <div class="header">
                <div class="top-company">
                    {{ strtoupper($employee->company?->name_en ?? config('app.name')) }}
                    @if($employee->company_name_ar_reshaped)
                        <div style="font-size: 18px; margin-top: 5px; font-family: 'DejaVu Sans', sans-serif;">
                            {{ $employee->company_name_ar_reshaped }}</div>
                    @endif
                </div>
                <div class="logo-container">
                    @if(isset($logoBase64) && $logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 70px;">
                    @elseif($employee->company?->logo)
                        <img src="{{ public_path('storage/' . $employee->company->logo) }}" alt="Logo"
                            style="max-height: 70px;">
                    @endif
                </div>
                <div class="cert-title-box">EXPERIENCE CERTIFICATE</div>
            </div>

            <div class="main-title">To Whom It May Concern</div>

            <div class="content">
                This prestigious award is presented to
                <div class="recipient-name">
                    {{ strtoupper($employee->name ?: ($employee->first_name_en . ' ' . $employee->last_name_en)) }}
                </div>
                in recognition of their exceptional service as
                <strong>{{ $employee->designation?->name ?? 'Employee' }}</strong>
                at <strong>{{ $employee->company?->name ?? config('app.name') }}</strong>.<br>
                They dedicated their expertise from
                <strong>{{ $employee->joining_date ? $employee->joining_date->format('M Y') : 'N/A' }}</strong> to the
                present,
                leaving a legacy of excellence within the
                <strong>{{ $employee->department?->name ?? 'Department' }}</strong>.
                We wish them all the very best for their future endeavors.
            </div>

            <div class="footer">
                <div class="date-box">Dated: {{ date('dS F, Y') }}<br>ID: {{ $employee->employee_code }}</div>
                <div class="sig-box">
                    <div class="sig-name">HR Manager<br><span style="font-size: 11px; font-weight: normal;">Authorized
                            Signature</span></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</body>

</html>