<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; background: #f0f4f8; }
        .page-wrapper { max-width: 680px; margin: 20px auto; padding: 0 10px 30px; }
        .action-bar { display: flex; gap: 10px; justify-content: center; margin-bottom: 16px; flex-wrap: wrap; }
        .btn { padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; }
        .btn-print { background: #6366f1; color: white; }
        .btn-whatsapp { background: #25D366; color: white; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .receipt { background: white; border: 2px solid #334155; border-radius: 4px; overflow: hidden; position: relative; }
        .office-copy-band { background: #e2e8f0; text-align: center; padding: 4px 0; font-size: 13px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; border-bottom: 2px solid #334155; color: #1e293b; }
        .receipt-body { padding: 18px 22px; }
        .top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; border-bottom: 1px solid #cbd5e1; padding-bottom: 12px; }
        .top-row .label { font-size: 11px; color: #64748b; margin-bottom: 2px; }
        .top-row .value { font-size: 14px; font-weight: 700; color: #1e293b; }
        .receipt-no-box { text-align: right; }
        .receipt-no-box .rno { font-size: 22px; font-weight: 800; color: #6366f1; font-family: 'Courier New', monospace; }
        .party-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .field-row { margin-bottom: 10px; }
        .field-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        .field-value { font-size: 14px; font-weight: 600; color: #1e293b; border-bottom: 1px dotted #94a3b8; padding-bottom: 3px; min-height: 22px; margin-top: 2px; }
        .shop-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; }
        .finance-box { border: 2px solid #334155; border-radius: 6px; overflow: hidden; margin-bottom: 14px; }
        .finance-box table { width: 100%; border-collapse: collapse; }
        .finance-box td { padding: 8px 14px; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
        .finance-box td:last-child { text-align: right; font-weight: 700; color: #1e293b; }
        .finance-box tr:last-child td { border-bottom: none; }
        .finance-box .total-row td { background: #1e293b; color: white !important; font-size: 15px; font-weight: 800; }
        .finance-box .total-row td:first-child { color: #94a3b8 !important; font-size: 12px; }
        .sig-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 18px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1.5px solid #334155; margin-top: 32px; }
        .sig-label { font-size: 11px; color: #64748b; margin-top: 4px; font-weight: 600; letter-spacing: 0.5px; }
        .paid-badge { display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid #86efac; }
        .watermark { position: absolute; bottom: 30px; right: 20px; font-size: 72px; font-weight: 900; color: #6366f120; transform: rotate(-25deg); letter-spacing: 4px; pointer-events: none; }
        @media print {
            body { background: white; }
            .page-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .action-bar { display: none !important; }
            .receipt { border-radius: 0; border: 1px solid #000; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="action-bar">
        <a href="javascript:history.back()" class="btn btn-back">← Back</a>
        <button onclick="window.print()" class="btn btn-print">🖨 Print</button>
        <a id="wa-btn" href="#" target="_blank" class="btn btn-whatsapp">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Share on WhatsApp
        </a>
    </div>

    <div class="receipt">
        <div class="office-copy-band">Office Copy</div>
        <div class="receipt-body">
            <div class="top-row">
                <div>
                    <div class="label">تاریخ / Date</div>
                    <div class="value">{{ $payment->payment_date->format('d-m-Y') }}</div>
                    <div style="margin-top:8px;"><span class="paid-badge">✓ PAYMENT RECEIVED</span></div>
                    @if($payment->shop->property_dealer)
                    <div style="margin-top:6px;" class="label">بذریعہ ڈیلر / Via Dealer: <strong>{{ $payment->shop->property_dealer }}</strong></div>
                    @endif
                </div>
                <div class="receipt-no-box">
                    <div class="label">رسید نمبر / Receipt No.</div>
                    <div class="rno">{{ $payment->receipt_number }}</div>
                    <div class="label" style="margin-top:6px;">بذریعہ / Via: {{ ucfirst($payment->payment_method ?? 'Cash') }}</div>
                </div>
            </div>

            <div class="party-row">
                <div class="field-row">
                    <div class="field-label">خریدار کا نام / Buyer Name</div>
                    <div class="field-value">{{ $payment->shop->owner->name ?? ($payment->shop->customers->first()?->name ?? '—') }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">والدیت / Father or Husband</div>
                    <div class="field-value">{{ $payment->shop->owner->father_name ?? ($payment->shop->customers->first()?->father_name ?? '—') }}</div>
                </div>
            </div>

            <div class="party-row">
                <div class="field-row">
                    <div class="field-label">شناختی کارڈ / CNIC</div>
                    <div class="field-value">{{ $payment->shop->owner->cnic ?? ($payment->shop->customers->first()?->cnic ?? '—') }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">موبائل نمبر / Mobile</div>
                    <div class="field-value">{{ $payment->shop->owner->phone ?? ($payment->shop->customers->first()?->phone ?? '—') }}</div>
                </div>
            </div>

            <div class="shop-row">
                <div>
                    <div class="field-label">دوکان نمبر / Shop No.</div>
                    <div class="field-value" style="font-size:18px;font-weight:800;color:#6366f1;">{{ $payment->shop->shop_number }}</div>
                </div>
                <div>
                    <div class="field-label">مارکیٹ / Market</div>
                    <div class="field-value">{{ $payment->shop->market->name ?? '—' }}</div>
                </div>
            </div>

            @if($payment->notes)
            <div class="field-row" style="margin-bottom:14px;">
                <div class="field-label">نوٹس / Notes</div>
                <div class="field-value">{{ $payment->notes }}</div>
            </div>
            @endif

            <div class="finance-box">
                <table>
                    <tr>
                        <td>ماہانہ قسط / Monthly Instalment</td>
                        <td>Rs {{ number_format($payment->shop->monthly_instalment ?? 0, 0) }}</td>
                    </tr>
                    {{-- <tr>
                        <td>کل رقم / Total Price</td>
                        <td>Rs {{ number_format($payment->shop->total_amount ?? 0, 0) }}</td>
                    </tr> --}}
                    <tr>
                        <td>بقیہ رقم / Balance Due</td>
                        <td>Rs {{ number_format(max(0, ($payment->shop->total_amount ?? 0) - ($payment->shop->paid_amount ?? 0)), 0) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>رقم ادا کی / Amount Paid This Time</td>
                        <td>Rs {{ number_format($payment->amount, 0) }}</td>
                    </tr>
                </table>
            </div>

            <div class="sig-row">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div class="sig-label">دستخط منیجر / Manager Signature</div>
                    @if($payment->authorized_by)<div class="sig-label" style="margin-top:4px;color:#1e293b;">{{ $payment->authorized_by }}</div>@endif
                </div>
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div class="sig-label">دستخط خریدار / Buyer Signature</div>
                </div>
            </div>
            @if($payment->received_by || $payment->paid_to)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:10px;">
                @if($payment->received_by)
                <div class="field-row">
                    <div class="field-label">موصول کیا / Received By</div>
                    <div class="field-value">{{ $payment->received_by }}</div>
                </div>
                @endif
                @if($payment->paid_to)
                <div class="field-row">
                    <div class="field-label">ادا کیا / Paid To</div>
                    <div class="field-value">{{ $payment->paid_to }}</div>
                </div>
                @endif
            </div>
            @endif

            <p style="text-align:center;font-size:10px;color:#94a3b8;margin-top:14px;">
                Generated {{ now()->format('d M Y, h:i A') }} · JK
            </p>
        </div>
        <div class="watermark">PAID</div>
    </div>
</div>

<script>
(function(){
    var shop   = "{{ addslashes($payment->shop->shop_number ?? '') }}";
    var market = "{{ addslashes($payment->shop->market->name ?? '') }}";
    var buyer  = "{{ addslashes($payment->shop->owner->name ?? $payment->shop->customers->first()?->name ?? '') }}";
    var amount = "{{ number_format($payment->amount, 0) }}";
    var date   = "{{ $payment->payment_date->format('d M Y') }}";
    var rno    = "{{ $payment->receipt_number }}";
    var bal    = "{{ number_format(max(0,($payment->shop->total_amount??0)-($payment->shop->paid_amount??0)), 0) }}";
    var monthly= "{{ number_format($payment->shop->monthly_instalment ?? 0, 0) }}";
    var msg = '🏪 JK - Instalment Receipt*\n\n'
            + '📋 Receipt No: ' + rno + '\n'
            + '📅 Date: ' + date + '\n'
            + '👤 Buyer: ' + buyer + '\n'
            + '🏬 Market: ' + market + ', Shop No: ' + shop + '\n'
            + '📆 Monthly Instalment: Rs ' + monthly + '\n'
            + '💰 Amount Paid: Rs ' + amount + '\n'
            + '📊 Balance Due: Rs ' + bal + '\n\n'
            + '✅ Payment received. Thank you!';
    document.getElementById('wa-btn').href = 'https://wa.me/?text=' + encodeURIComponent(msg);
})();
</script>
</body>
</html>
