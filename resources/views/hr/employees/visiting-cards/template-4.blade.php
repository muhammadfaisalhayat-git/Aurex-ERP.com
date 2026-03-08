<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Visiting Card - Creative Accent</title>
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
            color: #333; 
        }
        .card-container { 
            width: 3.5in; 
            height: 2in; 
            position: relative; 
            border-left: 10px solid #2ea169; 
            background: #ffffff; 
            box-sizing: border-box; 
            overflow: hidden; 
        }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 0.15in; }
        .content-cell { padding-left: 0.2in; padding-right: 0.15in; vertical-align: top; }
        
        .employee-name { font-size: 15pt; font-weight: bold; color: #2ea169; margin: 0; line-height: 1.2; }
        .employee-name-ar { font-size: 14pt; font-weight: bold; color: #2ea169; margin: 0; line-height: 1.3; direction: rtl; }
        .designation { font-size: 8.5pt; color: #64748b; margin: 2px 0 0 0; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .designation-ar { font-size: 8.5pt; color: #64748b; margin: 2px 0 0 0; font-weight: bold; direction: rtl; }
        
        .bottom-table { width: 3.15in; position: absolute; bottom: 0.15in; left: 0.15in; border-collapse: collapse; }
        .contact-cell { width: 55%; font-size: 7.5pt; color: #475569; vertical-align: bottom; }
        .company-cell { width: 45%; text-align: right; vertical-align: bottom; }
        
        .icon-text { color: #2ea169; font-weight: bold; padding-right: 2px; font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "sans-serif" }}; }
        
        .company-name { font-size: 9pt; font-weight: bold; color: #1e293b; margin: 0; line-height: 1.1; }
        .company-name-ar { font-size: 9.5pt; font-weight: bold; color: #1e293b; margin: 0; line-height: 1.2; direction: rtl; }
        .logo { max-width: 0.8in; max-height: 0.35in; margin-bottom: 5px; }

        /* Interactive Barcode Positioning */
        .qr-code-interactive { 
            position: absolute; 
            top: {{ $data['barcode_y'] ?? 0.6 }}in; 
            left: {{ $data['barcode_x'] ?? 2.4 }}in; 
            width: {{ $data['barcode_width'] ?? 0.7 }}in; 
            height: {{ $data['barcode_width'] ?? 0.7 }}in; 
            z-index: 999;
            background: #ffffff;
            box-sizing: border-box;
            touch-action: none;
            @if(isset($data['is_preview']) && $data['is_preview'])
                border: 1px dashed #2ea169;
            @endif
        }
        .qr-code { width: 100%; height: 100%; display: block; }
    </style>
</head>
<body>
    <div class="card-container">
        <table class="main-table">
            <tr>
                <td class="content-cell">
                    @if(app()->getLocale() === 'ar' && !empty($data['name_ar']))
                        <div class="employee-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div>
                        @if(!empty($data['name_en'])) <div style="font-size: 9pt; color: #64748b; margin-top: 2px;">{{ $data['name_en'] }}</div> @endif
                    @else
                        <div class="employee-name">{{ $data['name_en'] }}</div>
                        @if(!empty($data['name_ar'])) 
                            <div class="employee-name-ar" style="margin-top: 2px;">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div> 
                        @endif
                    @endif
                    
                    @if(app()->getLocale() === 'ar' && (!empty($data['designation_ar_reshaped']) || !empty($data['designation_ar'])))
                        <div class="designation-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? ($data['designation_ar'] ?? '') : ($data['designation_ar_reshaped'] ?? '') }}</div>
                    @else
                        <div class="designation">{{ $data['designation_en'] }}</div>
                    @endif
                </td>
                <td style="width: 1in; padding-right: 0.15in; text-align: right; vertical-align: top;">
                    @if(!empty($data['logoBase64'])) <img src="{{ $data['logoBase64'] }}" class="logo"> @endif
                </td>
            </tr>
        </table>

        <table class="bottom-table">
            <tr>
                <td class="contact-cell">
                    @if(!empty($data['phone'])) <div style="margin-bottom: 2px;"><span class="icon-text">P.</span> {{ $data['phone'] }}</div> @endif
                    @if(!empty($data['email'])) <div><span class="icon-text">E.</span> {{ $data['email'] }}</div> @endif
                </td>
                <td class="company-cell">
                    @if(!empty($data['company_name_en'])) <div class="company-name">{{ $data['company_name_en'] }}</div> @endif
                    @if(!empty($data['company_name_ar'])) 
                        <div class="company-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['company_name_ar'] : $data['company_name_ar_reshaped'] }}</div> 
                    @endif
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
