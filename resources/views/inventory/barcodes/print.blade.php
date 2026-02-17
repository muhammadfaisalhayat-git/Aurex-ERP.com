<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcodes') }}</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet">
    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @page {
            size:
                {{ ($settings->page_size ?? 'A4') == 'A4' ? 'A4' : number_format($settings->label_width ?? 50, 2, '.', '') . 'mm ' . number_format($settings->label_height ?? 30, 2, '.', '') . 'mm' }}
            ;
            margin:
                {{ number_format($settings->margin_top ?? 0, 2, '.', '') }}
                mm
                {{ number_format($settings->margin_right ?? 0, 2, '.', '') }}
                mm
                {{ number_format($settings->margin_bottom ?? 0, 2, '.', '') }}
                mm
                {{ number_format($settings->margin_left ?? 0, 2, '.', '') }}
                mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #111827;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Nav Header */
        .preview-header {
            background-color: #111827;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header-brand {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .header-meta {
            font-size: 0.875rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-dark {
            background: #374151;
            color: #fff;
        }

        .btn-dark:hover {
            background: #4b5563;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        /* Labels Container */
        .labels-container {
            padding: 3rem;
            display: flex;
            flex-wrap: wrap;
            gap: 15mm;
            justify-content: center;
            background-color: #f3f4f6;
            transform: scale(2);
            /* 2x zoom for realistic preview */
            transform-origin: top center;
            margin-top: 50px;
            margin-bottom: 200px;
        }

        /* Individual Label Box */
        .label-item {
            background-color: #ffffff !important;
            position: relative;
            overflow: visible;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            page-break-inside: avoid;
            width:
                {{ number_format(max(1, $settings->label_width ?? 50), 2, '.', '') }}
                mm;
            height:
                {{ number_format(max(1, $settings->label_height ?? 30), 2, '.', '') }}
                mm;
        }

        /* Elements inside Label */
        .element {
            position: absolute;
            transform: translateX(-50%);
            white-space: nowrap;
            text-align: center;
            color:
                {{ $settings->text_color ?? '#000000' }}
            ;
            pointer-events: none;
        }

        .el-name {
            font-weight: 600;
            font-size:
                {{ $settings->font_size_name ?? 10 }}
                px;
        }

        .el-barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size:
                {{ $settings->font_size_barcode ?? 45 }}
                px;
            color:
                {{ $settings->barcode_color ?? '#000000' }}
            ;
            line-height: 1;
        }

        .el-code {
            font-size:
                {{ $settings->font_size_code ?? 8 }}
                px;
            opacity: 0.8;
        }

        .el-price {
            font-weight: 700;
            font-size:
                {{ $settings->font_size_price ?? 12 }}
                px;
        }

        .el-custom {
            font-size:
                {{ $settings->font_size_custom ?? 8 }}
                px;
            font-style: italic;
            opacity: 0.7;
        }

        @media print {
            body {
                background: #fff !important;
            }

            .preview-header {
                display: none !important;
            }

            .labels-container {
                padding: 0 !important;
                gap: 0 !important;
                display: block !important;
                background-color: #fff !important;
                transform: none !important;
                /* Remove zoom for print */
                margin: 0 !important;
            }

            .label-item {
                box-shadow: none !important;
                margin: 0 !important;
                border: 0.1mm solid transparent;
                page-break-after: always;
            }

            .label-item:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>
    <div class="preview-header no-print">
        <div>
            <div class="header-brand"><i class="fas fa-barcode me-2"></i>{{ __('messages.print_preview') }}</div>
            <div class="header-meta">
                {{ count($barcodeData) }} {{ __('messages.labels') }} |
                {{ $settings->label_width }}x{{ $settings->label_height }}mm ({{ $settings->page_size }})
            </div>
        </div>
        <div class="btn-group">
            <a href="{{ route('inventory.barcodes.settings.edit') }}" class="btn btn-dark">
                <i class="fas fa-cog"></i> {{ __('messages.settings') }}
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> {{ __('messages.print_now') }}
            </button>
        </div>
    </div>

    <div class="labels-container">
        @foreach($barcodeData as $data)
            <div class="label-item">
                {{-- Product Name --}}
                @if($settings->show_product_name)
                    <div class="element el-name"
                        style="top: {{ number_format($settings->pos_y_name, 2, '.', '') }}mm; left: {{ number_format($settings->pos_x_name, 2, '.', '') }}mm;">
                        {{ $data['name'] }}
                    </div>
                @endif

                {{-- Barcode --}}
                <div class="element el-barcode"
                    style="top: {{ number_format($settings->pos_y_barcode, 2, '.', '') }}mm; left: {{ number_format($settings->pos_x_barcode, 2, '.', '') }}mm; font-family: 'Libre Barcode 128', cursive; font-size: {{ ($settings->font_size_barcode ?? 40) }}px; line-height: 1;">
                    {{ $data['code'] }}
                </div>

                {{-- Product Code --}}
                @if($settings->show_product_code)
                    <div class="element el-code"
                        style="top: {{ number_format($settings->pos_y_code, 2, '.', '') }}mm; left: {{ number_format($settings->pos_x_code, 2, '.', '') }}mm;">
                        {{ $data['code'] }}
                    </div>
                @endif

                {{-- Product Price --}}
                @if($settings->show_product_price)
                    <div class="element el-price"
                        style="top: {{ number_format($settings->pos_y_price, 2, '.', '') }}mm; left: {{ number_format($settings->pos_x_price, 2, '.', '') }}mm;">
                        {{ number_format($data['price'], 2) }} {{ __('messages.sar') }}
                    </div>
                @endif

                {{-- Custom Text --}}
                @if($settings->show_custom_text && $settings->custom_text)
                    <div class="element el-custom"
                        style="top: {{ number_format($settings->pos_y_custom, 2, '.', '') }}mm; left: {{ number_format($settings->pos_x_custom, 2, '.', '') }}mm;">
                        {{ $settings->custom_text }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</body>

</html>