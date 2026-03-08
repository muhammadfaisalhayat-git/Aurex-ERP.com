<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Visiting Card - Elegant Dark</title>
    @if(isset($data['is_preview']) && $data['is_preview'])
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @endif
    <style>
        @page { margin: 0; size: 252pt 144pt; }
        html, body { 
            margin: 0; padding: 0; width: 3.5in; height: 2in; 
            overflow: hidden;
            background-color: #1a1c20; 
        }
        body {
            font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "'dejavu sans', sans-serif" }} !important; 
            color: #ffffff; 
        }
        .card-container { 
            width: 3.5in; height: 2in; position: relative; 
            background: #1a1c20; border: 4px solid #2a2d34; 
            box-sizing: border-box; overflow: hidden;
        }
        
        .main-content { padding: 0.15in; padding-top: 0.2in; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: middle; }
        
        .header-table { border-bottom: 1px solid #333; padding-bottom: 5px; margin-bottom: 0.1in; }
        .logo { max-width: 0.9in; max-height: 0.35in; filter: brightness(0) invert(1); }
        .company-name-en { font-size: 9.5pt; font-weight: bold; color: #dba858; margin: 0; line-height: 1.1; text-transform: uppercase;}
        .company-name-ar { font-size: 10.5pt; font-weight: bold; color: #dba858; margin: 1px 0 0 0; line-height: 1.2; direction: rtl; }
        
        .employee-info { margin-top: 0.1in; padding-left: 0.05in; }
        .employee-name { font-size: 13pt; font-weight: bold; color: #ffffff; margin: 0; line-height: 1.1; text-transform: uppercase; }
        .employee-name-ar { font-size: 12pt; font-weight: bold; color: #ffffff; margin: 0; line-height: 1.2; direction: rtl; }
        .designation { font-size: 8pt; color: #a0a5b0; margin: 2px 0 0 0; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .designation-ar { font-size: 8pt; color: #a0a5b0; margin: 1px 0 0 0; font-weight: bold; direction: rtl; }
        
        .contact-table { position: absolute; bottom: 0.15in; left: 0.15in; width: 2.5in; font-size: 7pt; color: #cccccc; }
        .contact-table td { padding: 1.5px 0; }
        .icon { color: #dba858; font-family: {{ (isset($data['is_preview']) && $data['is_preview']) ? "'Cairo', sans-serif" : "sans-serif" }}; font-weight: bold; padding-right: 4px; }

        /* Interactive Barcode Positioning */
        .qr-code-interactive { 
            position: absolute; 
            top: {{ $data['barcode_y'] ?? 0.6 }}in; 
            left: {{ $data['barcode_x'] ?? 2.5 }}in; 
            width: {{ $data['barcode_width'] ?? 0.8 }}in; 
            height: {{ $data['barcode_width'] ?? 0.8 }}in; 
            z-index: 999;
            background: #ffffff;
            padding: 2px;
            border-radius: 2px;
            box-sizing: border-box;
            touch-action: none;
            @if(isset($data['is_preview']) && $data['is_preview'])
                border: 1px dashed #dba858;
            @endif
        }
        .qr-code { width: 100%; height: 100%; display: block; }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="main-content">
            <table class="header-table">
                <tr>
                    <td style="width: 1in; vertical-align: top;">
                        @if(!empty($data['logoBase64'])) <img src="{{ $data['logoBase64'] }}" class="logo"> @endif
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                        @if(!empty($data['company_name_en'])) <div class="company-name-en">{{ $data['company_name_en'] }}</div> @endif
                        @if(!empty($data['company_name_ar'])) 
                            <div class="company-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['company_name_ar'] : $data['company_name_ar_reshaped'] }}</div> 
                        @endif
                    </td>
                </tr>
            </table>
            
            <div class="employee-info">
                @if(app()->getLocale() === 'ar' && !empty($data['name_ar']))
                    <div class="employee-name-ar">{{ (isset($data['is_preview']) && $data['is_preview']) ? $data['name_ar'] : $data['name_ar_reshaped'] }}</div>
                    @if(!empty($data['name_en'])) <div style="font-size: 9pt; color: #a0a5b0;">{{ $data['name_en'] }}</div> @endif
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
            </div>
        </div>

        <table class="contact-table">
            <tr>
                <td style="width: 50%;">@if(!empty($data['phone'])) <span class="icon">P:</span> {{ $data['phone'] }} @endif</td>
                <td style="width: 50%;">@if(!empty($data['email'])) <span class="icon">E:</span> {{ $data['email'] }} @endif</td>
            </tr>
            <tr>
                <td>@if(!empty($data['mobile'])) <span class="icon">M:</span> {{ $data['mobile'] }} @endif</td>
                <td>@if(!empty($data['website'])) <span class="icon">W:</span> {{ $data['website'] }} @endif</td>
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
