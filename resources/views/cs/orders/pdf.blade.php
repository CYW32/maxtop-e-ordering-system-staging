<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Order - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        /* Centered Header Styles (No Logo) */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        /* Table & Section Styles */
        .info-section {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-section td {
            vertical-align: top;
        }

        .customer-box {
            width: 60%;
            border: 1px solid #000;
            padding: 10px;
            border-radius: 4px;
        }

        .meta-box {
            width: 35%;
            padding-left: 20px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 0;
        }

        .meta-label {
            font-weight: bold;
            width: 40%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #000;
        }

        .items-table th {
            background-color: #f3f4f6;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer-section {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .terms-box {
            border: 1px solid #000;
            padding: 10px;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin-top: 50px;
            padding-top: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 class="company-name">MAX TOP Synery (M) Sdn Bhd <span
                style="font-size: 14px; font-weight: normal;">(1114513-H)</span></h1>
        <p style="margin: 5px 0 0 0;">
            NO. 30, JALAN PP 11/4, ALAM PERDANA INDUSTRIAL PARK,<br>
            TAMAN PUTRA PERDANA, 47130 PUCHONG, SELANGOR<br>
            Tel: 03-8322 8638, &nbsp; Fax: 03-8322 3386 &nbsp; <br>
            Email: neoh@maxtop.com.my &nbsp; Web-Site: www.maxtop.com.my
        </p>
    </div>

    <div class="document-title">STOCK ORDER</div>

    <table class="info-section">
        <tr>
            <td class="customer-box">
                <strong>{{ $order->user->company->company_name ?? $order->user->name }}</strong><br>

                @if ($order->user->company)
                    {{ $order->user->company->delivery_address ?? 'Address not provided' }}<br>
                    {{ $order->user->company->postal_code ?? '' }} {{ $order->user->company->city ?? '' }}<br>
                    {{ $order->user->company->state ?? '' }}
                @else
                    Address not provided
                @endif
                <br><br>

                <strong>Attn:</strong> {{ $order->user->name }}<br>
                <strong>Tel:</strong> {{ $order->user->company->pic_phone ?? 'N/A' }}
            </td>
            <td class="meta-box">
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">Stock Order No</td>
                        <td>: <strong>{{ $order->order_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Date</td>
                        <td>: {{ $order->created_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Terms</td>
                        <td>: {{ $order->payment_terms ?? 'C.O.D' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Page</td>
                        <td>: 1 of 1</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 20%;">Item Code</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 15%;">UOM</th>
                <th style="width: 15%;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->item->sku ?? $item->item_code }}</td>
                    <td>
                        <strong>{{ $item->item->name ?? 'Unknown Item' }}</strong>
                        @if (!empty($item->remark))
                            <br><span
                                style="font-style: italic; font-size: 10px; color: #555;">*{{ $item->remark }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->uom->uom_name ?? ($item->snapshot_uom_name ?? 'UNIT') }}</td>
                    <td class="text-center font-bold">{{ $item->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">No items found for this order.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="text-right" style="padding: 8px; font-weight: bold;">Total Qty:</td>
                <td class="text-center font-bold" style="padding: 8px; border: 1px solid #000;">
                    {{ $order->items->sum('quantity') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-section">
        <div class="terms-box">
            <strong>Terms & Conditions:</strong><br>
            1. All goods sold are non-returnable and non-refundable.<br>
            2. Please inspect all goods upon receiving. Any discrepancies must be reported within 3 days.<br>
            3. This is a computer-generated document. No signature is required.
        </div>

        <table class="signature-table">
            <tr>
                <td style="width: 50%; vertical-align: bottom;">
                    <div
                        style="text-align: center; width: 80%; font-size: 14px; margin-bottom: 5px; text-transform: uppercase;">
                        {{ $order->handler->name ?? 'System Generated' }}
                    </div>
                    <div class="signature-line" style="margin-top: 0;">Issued By</div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: bottom;">
                    <div class="signature-line" style="margin-left: auto;">Received By</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
