<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rent Receipt – {{ $rentEntry->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc; }
        .page { max-width: 620px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 32px 30px 24px; text-align: center; color: white; }
        .logo-text { font-size: 26px; font-weight: 800; letter-spacing: -1px; }
        .header-sub { font-size: 12px; opacity: .8; margin-top: 2px; }
        .badge { display: inline-block; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); color: white; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 10px; letter-spacing: .5px; }
        .receipt-box { background: #f0f9ff; border: 2px dashed #bae6fd; margin: 24px 24px 0; border-radius: 12px; text-align: center; padding: 14px; }
        .receipt-label { font-size: 10px; color: #0369a1; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .receipt-number { font-size: 20px; font-weight: 800; color: #0284c7; font-family: monospace; margin-top: 3px; }
        .body { padding: 20px 24px 24px; }
        .section-title { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 18px 0 8px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .info-item { background: #f8fafc; border-radius: 10px; padding: 10px 12px; }
        .info-item .label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .info-item .value { font-size: 13px; font-weight: 600; color: #1e293b; }
        .amount-card { background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 14px; padding: 22px; text-align: center; margin: 18px 0; color: white; }
        .amount-label { font-size: 12px; opacity: .85; margin-bottom: 5px; }
        .amount-value { font-size: 36px; font-weight: 800; }
        .balance-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; }
        .balance-card { border-radius: 10px; padding: 12px; text-align: center; }
        .balance-card .label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .balance-card .val { font-size: 16px; font-weight: 700; }
        .bg-amber { background: #fef9c3; }
        .text-amber { color: #92400e; }
        .bg-red { background: #fee2e2; }
        .text-red { color: #b91c1c; }
        .bg-green { background: #dcfce7; }
        .text-green { color: #15803d; }
        .notes-box { background: #fef9c3; border-left: 3px solid #fbbf24; border-radius: 0 8px 8px 0; padding: 10px 12px; margin: 14px 0; }
        .notes-box .label { font-size: 10px; color: #92400e; font-weight: 600; margin-bottom: 3px; }
        .footer { border-top: 1px dashed #e2e8f0; padding: 16px 24px; text-align: center; color: #94a3b8; font-size: 11px; }
        .print-btn { display: block; margin: 20px auto 30px; padding: 10px 28px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media print {
            body { background: white; }
            .page { box-shadow: none; margin: 0; border-radius: 0; }
            .print-btn, .back-link { display: none !important; }
        }
    </style>
</head>
<body>

<a href="javascript:history.back()" class="back-link" style="display:block;text-align:center;margin:20px;color:#0284c7;font-size:12px;text-decoration:none;">← Back</a>

<div class="page">
    <div class="header">
        <div class="logo-text">PropManager</div>
        <p class="header-sub">Property Management System</p>
        <span class="badge">✓ RENT PAYMENT RECEIVED</span>
    </div>

    <div class="receipt-box">
        <div class="receipt-label">Receipt Number</div>
        <div class="receipt-number">{{ $rentEntry->receipt_number ?? 'RNT-' . str_pad($rentEntry->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="body">

        <div class="section-title">Property Details</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Market</div>
                <div class="value">{{ $rentEntry->rentShop->rentMarket->name ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Shop Number</div>
                <div class="value"># {{ $rentEntry->shop_number }}</div>
            </div>
        </div>

        <div class="section-title">Tenant / Payment Info</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Tenant</div>
                <div class="value">{{ $rentEntry->customer->name ?? $rentEntry->rentShop->tenant_name ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Payment Date</div>
                <div class="value">{{ $rentEntry->date->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Received By</div>
                <div class="value">{{ $rentEntry->received_by ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Generated</div>
                <div class="value">{{ now()->format('d M Y') }}</div>
            </div>
        </div>

        <div class="amount-card">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">Rs {{ number_format($rentEntry->amount_paid ?? 0, 0) }}</div>
        </div>

        @if($rentEntry->rent > 0)
        <div class="balance-row">
            <div class="balance-card bg-amber">
                <div class="label text-amber">Monthly Rent</div>
                <div class="val text-amber">Rs {{ number_format($rentEntry->rent, 0) }}</div>
            </div>
            @php $diff = ($rentEntry->rent) - ($rentEntry->amount_paid ?? 0); @endphp
            <div class="balance-card {{ $diff > 0 ? 'bg-red' : 'bg-green' }}">
                <div class="label {{ $diff > 0 ? 'text-red' : 'text-green' }}">{{ $diff > 0 ? 'Balance Due' : 'Overpaid' }}</div>
                <div class="val {{ $diff > 0 ? 'text-red' : 'text-green' }}">Rs {{ number_format(abs($diff), 0) }}</div>
            </div>
        </div>
        @endif

        @if($rentEntry->notes)
        <div class="notes-box">
            <div class="label">Notes</div>
            <div style="font-size:12px;color:#78350f;">{{ $rentEntry->notes }}</div>
        </div>
        @endif

    </div>

    <div class="footer">
        <p>This is a computer-generated receipt. No physical signature required.</p>
        <p style="margin-top:4px;">Generated on {{ now()->format('d M Y, h:i A') }} · PropManager</p>
    </div>
</div>

<button class="print-btn" onclick="window.print()">🖨 Print Receipt</button>

</body>
</html>
