<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcodes') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }

        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .label-item {
            width: 250px;
            height: 140px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .product-name {
            font-size: 12px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 50px;
            margin: 5px 0;
            line-height: 1;
        }

        .product-code {
            font-size: 10px;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 14px;
            font-weight: 700;
            color: #000;
        }

        @media print {
            body {
                background: transparent;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .labels-container {
                gap: 10mm;
            }

            .label-item {
                border: 1px solid #eee;
            }
        }

        .no-print-header {
            background: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="no-print no-print-header">
        <div>
            <strong>{{ __('messages.print_preview') }}</strong> - {{ count($barcodeData) }} {{ __('messages.labels') }}
        </div>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> {{ __('messages.print_now') }}
        </button>
    </div>

    <div class="labels-container">
        @foreach($barcodeData as $data)
            <div class="label-item">
                <div class="product-name">{{ $data['name'] }}</div>
                <div class="barcode">{{ $data['code'] }}</div>
                <div class="product-code">{{ $data['code'] }}</div>
                <div class="product-price">
                    {{ number_format($data['price'], 2) }} {{ __('messages.sar') }}
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Auto-show print dialog if requested
        // window.onload = () => window.print();
    </script>
</body>

</html>