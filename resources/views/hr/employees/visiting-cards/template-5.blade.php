<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Visiting Card - Classic Centered</title>
    @if(isset($data['is_preview']) && $data['is_preview'])
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <style>
        @page { margin: 0; size: 252pt 144pt; }
        html, body { 
            margin: 0; padding: 0; width: 3.5in; height: 2in; 
            overflow: hidden;
            background-color: #ffffff;
            text-align: center; 
        }
        body {
            font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "'dejavu sans', sans-serif" }} !important; 
            color: #222; 
        }
        .card-container { 
            width: 3.5in; height: 2in; position: relative; 
            border: 1px solid #dcdcdc; background: #fafafa;
            box-sizing: border-box; overflow: hidden;
            display: table;
        }
        .inner-content { display: table-cell; vertical-align: middle; padding: 0.15in; }
        
        .logo { max-width: 0.8in; max-height: 0.3in; margin: 0 auto 5px auto; display: block; }
        .company-header { border-bottom: 1px solid #dcdcdc; padding-bottom: 5px; margin-bottom: 8px; }
        .company-name { font-size: 10pt; font-weight: bold; color: #4a4a4a; margin: 0; letter-spacing: 1px; text-transform: uppercase; }
        .company-name-ar { direction: rtl; font-size: 10.5pt; font-weight: bold; }
        
        .employee-name { font-size: 14pt; font-weight: bold; color: #111; margin: 0; line-height: 1.2; }
        .employee-name-ar { font-size: 13pt; font-weight: bold; color: #111; margin: 0; line-height: 1.2; direction: rtl; }
        .designation { font-size: 8.5pt; color: #666; font-style: italic; margin: 2px 0 8px 0; }
        
        table.contact-info { width: 100%; font-size: 7pt; color: #444; border-collapse: collapse; margin-top: 8px; }
        table.contact-info td { padding: 1px 4px; }
        .pipe { color: #ccc; padding: 0 4px; }

        /* Interactive Barcode Positioning */
        .qr-code-interactive { 
            position: absolute; 
            top: {{ $data['barcode_y'] ?? 1.3 }}in; 
            left: {{ $data['barcode_x'] ?? 1.5 }}in; 
            width: {{ $data['barcode_width'] ?? 0.5 }}in; 
            height: {{ $data['barcode_width'] ?? 0.5 }}in; 
            z-index: 999;
            box-sizing: border-box;
            touch-action: none;
            @if(isset($data['is_preview']) && $data['is_preview'])
                border: 1px dashed #666;
            @endif
        }
        .qr-code { width: 100%; height: 100%; display: block; }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="inner-content">
            @if(!empty($data['logoBase64'])) 
                <img src="{{ $data['logoBase64'] }}" class="logo"> 
            @endif
            
            <div class="company-header">
                <span class="company-name">{{ !empty($data['company_name_en']) ? $data['company_name_en'] : '' }}</span>
                @if(!empty($data['company_name_ar']) && !empty($data['company_name_en'])) <span class="pipe">|</span> @endif
                @if(!empty($data['company_name_ar'])) 
                    <span class="company-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['company_name_ar'] : $data['company_name_ar_reshaped'] }}</span> 
                @endif
            </div>

            @if(app()->getLocale() === 'ar' && !empty($data['name_ar']))
                <div class="employee-name-ar">
                    {{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}
                    @if(!empty($data['name_en'])) <span style="font-size: 9pt; color: #666; font-weight: normal; margin-left: 5px;">({{ $data['name_en'] }})</span> @endif
                </div>
            @else
                <div class="employee-name">{{ $data['name_en'] }}</div>
                @if(!empty($data['name_ar'])) 
                    <div class="employee-name-ar" style="margin-top: 2px;">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div> 
                @endif
            @endif
            
            <div class="designation">
                @if(app()->getLocale() === 'ar' && (!empty($data['designation_ar_reshaped']) || !empty($data['designation_ar'])))
                    {{ (isset($data['is_preview']) && $data['is_preview']) ? ($data['designation_ar'] ?? '') : ($data['designation_ar_reshaped'] ?? '') }}
                @else
                    {{ $data['designation_en'] }}
                @endif
            </div>
            
            <table class="contact-info">
                @if(!empty($data['phone']) || !empty($data['email']))
                <tr>
                    <td style="text-align: right; width: 48%;">{{ $data['phone'] }}</td>
                    <td style="width: 4%;" class="pipe">|</td>
                    <td style="text-align: left; width: 48%;">{{ $data['email'] }}</td>
                </tr>
                @endif
            </table>

            @if(!empty($data['qrCodeBase64']))
                <div class="qr-code-interactive">
                    <img src="{{ $data['qrCodeBase64'] }}" class="qr-code">
                </div>
            @endif
        </div>
    </div>
</body>
</html>
