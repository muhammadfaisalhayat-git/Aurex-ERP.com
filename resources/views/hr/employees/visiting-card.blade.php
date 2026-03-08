<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <title>Visiting Card - {{ $data['name_en'] ?? 'Employee' }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            width: 3.5in;
            height: 2in;
            background-color: #ffffff;
            color: #333333;
            box-sizing: border-box;
            position: relative;
        }

        /* Modern minimalist layout */
        .card-container {
            width: 100%;
            height: 100%;
            padding: 0.15in 0.2in;
            box-sizing: border-box;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e0e0e0; /* Optional: for preview/cutting */
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 0.1in;
        }

        .logo-container {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }

        .company-info {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
        }

        .logo {
            max-width: 1in;
            max-height: 0.4in;
        }

        .company-name-en {
            font-size: 10pt;
            font-weight: bold;
            color: #0d6efd; /* Primary color */
            margin: 0;
            line-height: 1.1;
        }

        .company-name-ar {
            font-size: 10pt;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
            line-height: 1.1;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .body-section {
            margin-top: 0.1in;
        }

        .employee-name {
            font-size: 14pt;
            font-weight: bold;
            color: #212529;
            margin: 0;
            line-height: 1.2;
        }

        .employee-name-ar {
            font-size: 12pt;
            font-weight: bold;
            color: #212529;
            margin: 0;
            line-height: 1.2;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .designation {
            font-size: 9pt;
            font-weight: normal;
            color: #6c757d;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-info {
            position: absolute;
            bottom: 0.15in;
            left: 0.2in;
            right: 0.2in;
            font-size: 7pt;
            color: #495057;
        }

        .contact-table {
            width: 100%;
            border-collapse: collapse;
        }

        .contact-table td {
            padding: 1px 0;
            vertical-align: middle;
        }

        .icon {
            width: 10px;
            color: #0d6efd;
            padding-right: 5px;
        }
        
        /* Decorative element */
        .accent-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #0d6efd;
        }

    </style>
</head>

<body>
    <div class="card-container">
        <div class="accent-bar"></div>
        
        <div class="header">
            <div class="logo-container">
                @if(!empty($data['logoBase64']))
                    <img src="{{ $data['logoBase64'] }}" class="logo" alt="Company Logo">
                @endif
            </div>
            <div class="company-info">
                @if(!empty($data['company_name_en']))
                    <p class="company-name-en">{{ $data['company_name_en'] }}</p>
                @endif
                @if(!empty($data['company_name_ar']))
                    <p class="company-name-ar">{{ $data['company_name_ar_reshaped'] }}</p>
                @endif
            </div>
        </div>

        <div class="body-section">
            @if(app()->getLocale() === 'ar' && !empty($data['name_ar']))
                <p class="employee-name-ar">{{ $data['name_ar_reshaped'] }}</p>
                @if(!empty($data['name_en']))
                    <p class="employee-name" style="font-size: 10pt;">{{ $data['name_en'] }}</p>
                @endif
            @else
                <p class="employee-name">{{ $data['name_en'] }}</p>
                @if(!empty($data['name_ar']))
                    <p class="employee-name-ar" style="font-size: 10pt;">{{ $data['name_ar_reshaped'] }}</p>
                @endif
            @endif
            
            <p class="designation">
                {{ app()->getLocale() === 'ar' && !empty($data['designation_ar_reshaped']) ? $data['designation_ar_reshaped'] : $data['designation_en'] }}
            </p>
        </div>

        <div class="contact-info">
            <table class="contact-table">
                <tr>
                    <td style="width: 50%;">
                        @if(!empty($data['phone']))
                            <span style="font-family: DejaVu Sans;">&#9742;</span> {{ $data['phone'] }}
                        @endif
                    </td>
                    <td style="width: 50%;">
                        @if(!empty($data['email']))
                            <span style="font-family: DejaVu Sans;">&#9993;</span> {{ $data['email'] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        @if(!empty($data['mobile']))
                            <span style="font-family: DejaVu Sans;">&#128241;</span> {{ $data['mobile'] }}
                        @endif
                    </td>
                    <td>
                        @if(!empty($data['website']))
                            <span style="font-family: DejaVu Sans;">&#127760;</span> {{ $data['website'] }}
                        @endif
                    </td>
                </tr>
                @if(!empty($data['address_en']) || !empty($data['address_ar']))
                <tr>
                    <td colspan="2" style="padding-top: 3px;">
                        <span style="font-family: DejaVu Sans;">&#128205;</span> 
                        {{ app()->getLocale() === 'ar' && !empty($data['address_ar']) ? $data['address_ar_reshaped'] : $data['address_en'] }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</body>

</html>
