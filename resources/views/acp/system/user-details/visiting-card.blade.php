<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Visiting Card</title>
    <style>
        @page {
            margin: 0;
            size: 255.12pt 153.07pt;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            width: 90mm;
            height: 54mm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
            overflow: hidden;
        }

        .card-container {
            width: 90mm;
            height: 54mm;
            position: relative;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 6mm;
            overflow: hidden;
        }

        /* Decorative Accent */
        .accent-stripe {
            position: absolute;
            top: 0;
            left: 0;
            width: 1.5mm;
            height: 100%;
            background-color: #3b82f6;
        }

        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.05);
        }

        .circle-1 {
            width: 40mm;
            height: 40mm;
            top: -15mm;
            right: -10mm;
        }

        .circle-2 {
            width: 20mm;
            height: 20mm;
            bottom: -5mm;
            left: 20mm;
        }

        /* Table Layout for maximum DomPDF compatibility */
        .layout-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        .left-panel {
            width: 38mm;
            vertical-align: top;
            padding-right: 4mm;
        }

        .right-panel {
            vertical-align: top;
            padding-left: 4mm;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Avatar / Logo Section */
        .avatar-box {
            margin-bottom: 4mm;
        }

        .avatar-img {
            width: 14mm;
            height: 14mm;
            border-radius: 50%;
            border: 2px solid #3b82f6;
            display: block;
        }

        .avatar-placeholder {
            width: 14mm;
            height: 14mm;
            border-radius: 50%;
            background-color: #3b82f6;
            color: #ffffff;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 14mm;
            display: block;
        }

        .user-name {
            font-size: 11pt;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 1mm;
            line-height: 1.2;
        }

        .user-role {
            font-size: 8pt;
            color: #94a3b8;
            margin-bottom: 2mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Contact Details Section */
        .contact-item {
            margin-bottom: 2.5mm;
        }

        .contact-label {
            font-size: 6.5pt;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
            display: block;
        }

        .contact-value {
            font-size: 8pt;
            color: #e2e8f0;
            display: block;
        }

        /* Footer / Company Section */
        .company-section {
            position: absolute;
            bottom: 6mm;
            left: 6mm;
        }

        .company-name {
            font-size: 7.5pt;
            font-weight: bold;
            color: #3b82f6;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .branch-name {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 0.5mm;
        }

        /* RTL adjustments if needed, though this template is horizontal */
        [dir="rtl"] .accent-stripe {
            left: auto;
            right: 0;
        }

        [dir="rtl"] .left-panel {
            padding-right: 0;
            padding-left: 4mm;
            text-align: right;
        }

        [dir="rtl"] .right-panel {
            padding-left: 0;
            padding-right: 4mm;
            border-left: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="accent-stripe"></div>
        <div class="deco-circle circle-1"></div>
        <div class="deco-circle circle-2"></div>

        <table class="layout-table">
            <tr>
                <td class="left-panel">
                    <div class="avatar-box">
                        @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                            <img src="{{ public_path('storage/' . $user->avatar) }}" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ $user->name }}</div>
                        @if($user->roles->isNotEmpty())
                            <div class="user-role">{{ $user->roles->first()->name }}</div>
                        @endif
                    </div>
                </td>
                <td class="right-panel">
                    <div class="contact-item">
                        <span class="contact-label">Email Address</span>
                        <span class="contact-value">{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                        <div class="contact-item">
                            <span class="contact-label">Phone Number</span>
                            <span class="contact-value">{{ $user->phone }}</span>
                        </div>
                    @endif
                    @if($user->employee_code)
                        <div class="contact-item">
                            <span class="contact-label">Employee ID</span>
                            <span class="contact-value">{{ $user->employee_code }}</span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="company-section">
            <div class="company-name">{{ $user->company->name ?? 'Aurex ERP' }}</div>
            @if($user->branch)
                <div class="branch-name">{{ $user->branch->name }}</div>
            @endif
        </div>
    </div>
</body>

</html>