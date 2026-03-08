<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Visiting Card - Corporate Split</title>
    @if(isset($data['is_preview']) && $data['is_preview'])
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <style>
        @page { margin: 0; size: 252pt 144pt; }
        html, body { 
            margin: 0; padding: 0; width: 3.5in; height: 2in; 
            overflow: hidden;
            background-color: #ffffff; 
        }
        body {
            font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "'dejavu sans', sans-serif" }} !important; 
        }
        .card-container { 
            width: 3.5in; height: 2in; position: relative; overflow: hidden;
        }
        
        table.outer-table { width: 100%; height: 100%; border-collapse: collapse; table-layout: fixed; }
        .left-col { width: 1.25in; background: #2b3a4a; color: white; border-right: 3px solid #e74c3c; vertical-align: middle; text-align: center; padding: 0.1in; }
        .right-col { width: 2.25in; vertical-align: top; padding: 0.2in 0.15in; }
        
        .logo { max-width: 0.85in; max-height: 0.4in; background: #fff; padding: 3px; border-radius: 2px; margin-bottom: 8px; }
        .company-name-en { font-size: 8.5pt; font-weight: bold; color: #ffffff; margin: 0; line-height: 1.1; text-transform: uppercase; }
        .company-name-ar { font-size: 9.5pt; font-weight: bold; color: #ffffff; margin: 2px 0 0 0; line-height: 1.2; direction: rtl; }
        
        .employee-name { font-size: 13pt; font-weight: bold; color: #2b3a4a; margin: 0; line-height: 1.1; }
        .employee-name-ar { font-size: 12pt; font-weight: bold; color: #2b3a4a; margin: 0; line-height: 1.2; direction: rtl; }
        .designation { font-size: 8.5pt; color: #e74c3c; font-weight: bold; margin: 2px 0 8px 0; text-transform: uppercase; border-bottom: 1px solid #e74c3c; display: inline-block; padding-bottom: 2px; }
        .designation-ar { font-size: 8.5pt; color: #e74c3c; font-weight: bold; margin: 2px 0 8px 0; direction: rtl; border-bottom: 1px solid #e74c3c; display: inline-block; padding-bottom: 2px; }
        
        .contact-table { width: 100%; font-size: 7.5pt; color: #555; border-collapse: collapse; margin-top: 5px; }
        .contact-table td { padding: 1.5px 0; }
        .icon { color: #e74c3c; padding-right: 3px; font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "sans-serif" }}; font-weight: bold; }

        /* Interactive Barcode Positioning */
        .qr-code-interactive { 
            position: absolute; 
            top: {{ $data['barcode_y'] ?? 0.65 }}in; 
            left: {{ $data['barcode_x'] ?? 2.65 }}in; 
            width: {{ $data['barcode_width'] ?? 0.65 }}in; 
            height: {{ $data['barcode_width'] ?? 0.65 }}in; 
            z-index: 999;
            background: #ffffff;
            padding: 1px;
            box-sizing: border-box;
            touch-action: none;
            @if(isset($data['is_preview']) && $data['is_preview'])
                border: 1px dashed #e74c3c;
            @endif
        }
        .qr-code { width: 100%; height: 100%; display: block; }
    </style>
</head>
<body>
    <div class="card-container">
        <table class="outer-table">
            <tr>
                <td class="left-col">
                    @if(!empty($data['logoBase64'])) <img src="{{ $data['logoBase64'] }}" class="logo"> @endif
                    @if(!empty($data['company_name_en'])) <div class="company-name-en">{{ $data['company_name_en'] }}</div> @endif
                    @if(!empty($data['company_name_ar'])) 
                        <div class="company-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['company_name_ar'] : $data['company_name_ar_reshaped'] }}</div> 
                    @endif
                </td>
                <td class="right-col">
                    @if(app()->getLocale() === 'ar' && !empty($data['name_ar']))
                        <div class="employee-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div>
                        @if(!empty($data['name_en'])) <div style="font-size: 9pt; color: #777;">{{ $data['name_en'] }}</div> @endif
                    @else
                        <div class="employee-name">{{ $data['name_en'] }}</div>
                        @if(!empty($data['name_ar'])) 
                            <div class="employee-name-ar" style="margin-top: 1px;">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div> 
                        @endif
                    @endif
                    
                    @if(app()->getLocale() === 'ar' && (!empty($data['designation_ar_reshaped']) || !empty($data['designation_ar'])))
                        <div class="designation-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? ($data['designation_ar'] ?? '') : ($data['designation_ar_reshaped'] ?? '') }}</div>
                    @else
                        <div class="designation">{{ $data['designation_en'] }}</div>
                    @endif
                    
                    <table class="contact-table">
                        @if(!empty($data['phone'])) <tr><td><span class="icon">P:</span> {{ $data['phone'] }}</td></tr> @endif
                        @if(!empty($data['email'])) <tr><td><span class="icon">E:</span> {{ $data['email'] }}</td></tr> @endif
                        @if(!empty($data['website'])) <tr><td><span class="icon">W:</span> {{ $data['website'] }}</td></tr> @endif
                    </table>
                </td>
            </tr>
        </table>

        @if(!empty($data['qrCodeBase64']))
            <div class="qr-code-interactive">
                <img src="{{ $data['qrCodeBase64'] }}" class="qr-code">
            </div>
        @endif
    </div>
</body>
</html>
