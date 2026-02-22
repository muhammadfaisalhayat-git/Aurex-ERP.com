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
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
            width: 210mm;
            height: 296mm;
            overflow: hidden;
            box-sizing: border-box;
        }

        .certificate-container {
            margin: 10mm;
            padding: 15mm;
            border: 8px double #2c3e50;
            height: 276mm;
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 20mm;
        }

        .top-company {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5mm;
            text-transform: uppercase;
        }

        .logo-img {
            max-height: 60px;
            margin-bottom: 8mm;
        }

        .cert-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            border-top: 2px solid #2c3e50;
            border-bottom: 2px solid #2c3e50;
            display: inline-block;
            padding: 5px 20px;
        }

        .title-whom {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20mm 0 15mm 0;
            text-decoration: underline;
        }

        .content {
            font-size: 18px;
            margin-bottom: 20mm;
            text-align: justify;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }

        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 5mm;
        }

        .date {
            float: left;
            margin-top: 5mm;
        }

        .company-contact {
            font-size: 12px;
            color: #666;
            margin-top: 5mm;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="header">
            <div class="top-company">
                {{ strtoupper($employee->company?->name_en ?? config('app.name')) }}
                @if($employee->company_name_ar_reshaped)
                    <div style="font-size: 18px; margin-top: 5px; font-family: 'DejaVu Sans', sans-serif;">
                        {{ $employee->company_name_ar_reshaped }}</div>
                @endif
            </div>
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img">
            @elseif($employee->company?->logo)
                <img src="{{ public_path('storage/' . $employee->company->logo) }}" alt="Logo" class="logo-img">
            @endif
            <div class="clear"></div>
            <div class="cert-title">EXPERIENCE CERTIFICATE</div>
            <div class="company-contact">
                @if($employee->company?->address) {{ $employee->company->address }} <br> @endif
                @if($employee->company?->contact_phone) Phone: {{ $employee->company->contact_phone }} @endif
                @if($employee->company?->contact_email) | Email: {{ $employee->company->contact_email }} @endif
                @if($employee->company?->registration_number || $employee->company?->tax_number)
                    <br> @if($employee->company->registration_number) CR: {{ $employee->company->registration_number }}
                    @endif
                    @if($employee->company->tax_number) | VAT: {{ $employee->company->tax_number }} @endif
                @endif
            </div>
        </div>

        <div class="title-whom">TO WHOM IT MAY CONCERN</div>

        <div class="content">
            <p>This is to certify that
                <strong>{{ strtoupper($employee->name ?: ($employee->first_name_en . ' ' . $employee->last_name_en)) }}</strong>
                (Employee Code: <strong>{{ $employee->employee_code }}</strong>),
                @if($employee->national_id)
                    holding National ID: <strong>{{ $employee->national_id }}</strong>,
                @elseif($employee->passport_number)
                    holding Passport No: <strong>{{ $employee->passport_number }}</strong>,
                @endif
                has been employed with us as a
                <strong>{{ $employee->designation?->name ?? 'Employee' }}</strong> in the
                <strong>{{ $employee->department?->name ?? 'Department' }}</strong> department from
                <strong>{{ $employee->joining_date ? $employee->joining_date->format('d-M-Y') : '' }}</strong> to
                date.
            </p>

            <p>During {{ $employee->gender === 'female' ? 'her' : 'his' }} tenure with
                <strong>{{ $employee->company?->name ?? config('app.name') }}</strong>,
                we found {{ $employee->gender === 'female' ? 'her' : 'him' }} to be hardworking, dedicated, and a
                regular professional.
                {{ $employee->gender === 'female' ? 'She' : 'He' }} has consistently demonstrated excellent performance
                and has been an asset to our organization.
            </p>

            <p>We wish {{ $employee->gender === 'female' ? 'her' : 'him' }} all the very best for
                {{ $employee->gender === 'female' ? 'her' : 'his' }} future endeavors.
            </p>
        </div>

        <div class="footer">
            <div class="date">{{ __('messages.date') }}: {{ date('d-m-Y') }}</div>
            <div class="signature-box">Authorized Signatory <br><strong>Human Resources</strong></div>
            <div class="clear"></div>
        </div>
    </div>
</body>

</html>