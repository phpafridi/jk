<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #1e293b; background: white; }
        .page { max-width: 600px; margin: 0 auto; padding: 40px 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
        .logo { width: 50px; height: 50px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .title { font-size: 22px; font-weight: 700; color: #1e293b; }
        .subtitle { font-size: 12px; color: #64748b; margin-top: 3px; }
        .badge { display: inline-block; background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 8px; }
        .receipt-no { background: #f1f5f9; border-radius: 8px; padding: 12px; text-align: center; margin: 20px 0; }
        .receipt-no .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .receipt-no .number { font-size: 18px; font-weight: 700; color: #6366f1; font-family: monospace; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
        .info-item { background: #f8fafc; border-radius: 8px; padding: 12px; }
        .info-item .label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-item .value { font-size: 13px; font-weight: 600; color: #1e293b; }
        .amount-box { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border-radius: 12px; padding: 20px; text-align: center; margin: 25px 0; }
        .amount-box .label { font-size: 12px; opacity: 0.8; margin-bottom: 5px; }
        .amount-box .amount { font-size: 32px; font-weight: 700; }
        .footer { text-align: center; border-top: 1px dashed #e2e8f0; padding-top: 20px; margin-top: 30px; color: #94a3b8; font-size: 11px; }
        .watermark { position: fixed; bottom: 40px; right: 40px; opacity: 0.05; font-size: 80px; font-weight: 900; color: #6366f1; transform: rotate(-30deg); }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div style="font-size: 28px; font-weight: 800; color: #6366f1; letter-spacing: -1px;">PropManager</div>
        <p class="subtitle">Property Management System</p>
        <span class="badge">✓ PAYMENT RECEIVED</span>
    </div>

    <div class="receipt-no">
        <div class="label">Receipt Number</div>
        <div class="number">{{ $payment->receipt_number }}</div>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="label">Market</div>
            <div class="value">{{ $payment->shop->market->name ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="label">Shop Number</div>
            <div class="value"># {{ $payment->shop->shop_number ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="label">Owner / Customer</div>
            <div class="value">{{ $payment->shop->owner->name ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="label">Payment Date</div>
            <div class="value">{{ $payment->payment_date->format('d M Y') }}</div>
        </div>
        <div class="info-item">
            <div class="label">Payment Method</div>
            <div class="value">{{ ucfirst($payment->payment_method) }}</div>
        </div>
        <div class="info-item">
            <div class="label">Recorded By</div>
            <div class="value">{{ $payment->recorder->name ?? 'N/A' }}</div>
        </div>
    </div>

    @if($payment->notes)
    <div class="info-item" style="margin-bottom: 15px;">
        <div class="label">Notes</div>
        <div class="value">{{ $payment->notes }}</div>
    </div>
    @endif

    <div class="amount-box">
        <div class="label">Amount Paid</div>
        <div class="amount">Rs {{ number_format($payment->amount, 2) }}</div>
    </div>

    @if($payment->shop->total_amount > 0)
    <div style="background: #fef9c3; border-radius: 8px; padding: 12px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <div style="text-align: center;">
            <div style="font-size: 10px; color: #92400e; margin-bottom: 3px;">TOTAL AMOUNT</div>
            <div style="font-size: 15px; font-weight: 700; color: #92400e;">Rs {{ number_format($payment->shop->total_amount, 0) }}</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 10px; color: #92400e; margin-bottom: 3px;">BALANCE DUE</div>
            <div style="font-size: 15px; font-weight: 700; color: #92400e;">Rs {{ number_format(max(0, $payment->shop->total_amount - $payment->shop->paid_amount), 0) }}</div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a physical signature.</p>
        <p style="margin-top: 5px;">Generated on {{ now()->format('d M Y, h:i A') }} · PropManager</p>
    </div>
</div>
<div class="watermark">PAID</div>
</body>
</html>
