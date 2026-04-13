<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rent Receipt – {{ $rentEntry->receipt_number ?? 'RNT-'.str_pad($rentEntry->id,6,'0',STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; background: #f0f4f8; }
        .page-wrapper { max-width: 680px; margin: 20px auto; padding: 0 10px 30px; }
        .action-bar { display: flex; gap: 10px; justify-content: center; margin-bottom: 16px; flex-wrap: wrap; }
        .btn { padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; }
        .btn-print { background: #0ea5e9; color: white; }
        .btn-whatsapp { background: #25D366; color: white; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .receipt { background: white; border: 2px solid #334155; border-radius: 4px; overflow: hidden; position: relative; }
        .office-copy-band { background: #e0f2fe; text-align: center; padding: 4px 0; font-size: 13px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; border-bottom: 2px solid #334155; color: #0c4a6e; }
        .receipt-body { padding: 18px 22px; }
        .top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; border-bottom: 1px solid #cbd5e1; padding-bottom: 12px; }
        .top-row .label { font-size: 11px; color: #64748b; margin-bottom: 2px; }
        .top-row .value { font-size: 14px; font-weight: 700; color: #1e293b; }
        .receipt-no-box { text-align: right; }
        .receipt-no-box .rno { font-size: 22px; font-weight: 800; color: #0ea5e9; font-family: 'Courier New', monospace; }
        .party-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .field-row { margin-bottom: 10px; }
        .field-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        .field-value { font-size: 14px; font-weight: 600; color: #1e293b; border-bottom: 1px dotted #94a3b8; padding-bottom: 3px; min-height: 22px; margin-top: 2px; }
        .shop-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; padding: 10px 12px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; }
        .finance-box { border: 2px solid #334155; border-radius: 6px; overflow: hidden; margin-bottom: 14px; }
        .finance-box table { width: 100%; border-collapse: collapse; }
        .finance-box td { padding: 8px 14px; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
        .finance-box td:last-child { text-align: right; font-weight: 700; color: #1e293b; }
        .finance-box tr:last-child td { border-bottom: none; }
        .finance-box .total-row td { background: #0c4a6e; color: white !important; font-size: 15px; font-weight: 800; }
        .finance-box .total-row td:first-child { color: #7dd3fc !important; font-size: 12px; }
        .sig-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 18px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1.5px solid #334155; margin-top: 32px; }
        .sig-label { font-size: 11px; color: #64748b; margin-top: 4px; font-weight: 600; letter-spacing: 0.5px; }
        .rent-badge { display: inline-flex; align-items: center; gap: 5px; background: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid #93c5fd; }
        .watermark { position: absolute; bottom: 30px; right: 20px; font-size: 60px; font-weight: 900; color: #0ea5e915; transform: rotate(-25deg); letter-spacing: 2px; pointer-events: none; }
        @media print {
            body { background: white; }
            .page-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .action-bar { display: none !important; }
            .receipt { border-radius: 0; border: 1px solid #000; }
        }
    </style>
</head>
<body>
@php
    $rno = $rentEntry->receipt_number ?? 'RNT-'.str_pad($rentEntry->id,6,'0',STR_PAD_LEFT);
    $shopNum = $rentEntry->shop_number ?? ($rentEntry->rentShop->shop_number ?? '—');
    $marketName = $rentEntry->rentShop->rentMarket->name ?? '—';
    $tenant = $rentEntry->customer->name ?? ($rentEntry->rentShop->tenant_name ?? '—');
    $tenantPhone = $rentEntry->rentShop->tenant_phone ?? '—';
    $tenantCnic  = $rentEntry->rentShop->tenant_cnic  ?? '—';
    $rentAmt     = $rentEntry->rent ?? $rentEntry->rentShop->rent_amount ?? 0;
    $paidAmt     = $rentEntry->amount_paid ?? 0;
    $balance     = max(0, $rentAmt - $paidAmt);
    $payDate     = $rentEntry->date ?? now();
@endphp
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
        <div class="office-copy-band">Office Copy – Rent Receipt</div>
        <div class="receipt-body">
            <div class="top-row">
                <div>
                    <div class="label">تاریخ / Date</div>
                    <div class="value">{{ \Carbon\Carbon::parse($payDate)->format('d-m-Y') }}</div>
                    <div style="margin-top:8px;"><span class="rent-badge">🔑 RENT RECEIVED</span></div>
                </div>
                <div class="receipt-no-box">
                    <div class="label">رسید نمبر / Receipt No.</div>
                    <div class="rno">{{ $rno }}</div>
                    @if($rentEntry->received_by)
                    <div class="label" style="margin-top:6px;">موصول کیا / By: {{ $rentEntry->received_by }}</div>
                    @endif
                </div>
            </div>

            <div class="party-row">
                <div class="field-row">
                    <div class="field-label">کرایہ دار / Tenant Name</div>
                    <div class="field-value">{{ $tenant }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">موبائل نمبر / Mobile</div>
                    <div class="field-value">{{ $tenantPhone }}</div>
                </div>
            </div>

            <div class="party-row">
                <div class="field-row">
                    <div class="field-label">شناختی کارڈ / CNIC</div>
                    <div class="field-value">{{ $tenantCnic }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">ماہ / For Month</div>
                    <div class="field-value">{{ \Carbon\Carbon::parse($payDate)->format('F Y') }}</div>
                </div>
            </div>

            <div class="shop-row">
                <div>
                    <div class="field-label">دوکان نمبر / Shop No.</div>
                    <div class="field-value" style="font-size:18px;font-weight:800;color:#0ea5e9;">{{ $shopNum }}</div>
                </div>
                <div>
                    <div class="field-label">مارکیٹ / Market</div>
                    <div class="field-value">{{ $marketName }}</div>
                </div>
            </div>

            @if($rentEntry->notes)
            <div class="field-row" style="margin-bottom:14px;">
                <div class="field-label">نوٹس / Notes</div>
                <div class="field-value">{{ $rentEntry->notes }}</div>
            </div>
            @endif

            <div class="finance-box">
                <table>
                    <tr>
                        <td>ماہانہ کرایہ / Monthly Rent</td>
                        <td>Rs {{ number_format($rentAmt, 0) }}</td>
                    </tr>
                    <tr>
                        <td>بقیہ / Balance After Payment</td>
                        <td>Rs {{ number_format($balance, 0) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>رقم ادا کی / Amount Paid</td>
                        <td>Rs {{ number_format($paidAmt, 0) }}</td>
                    </tr>
                </table>
            </div>

            <div class="sig-row">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div class="sig-label">دستخط منیجر / Manager Signature</div>
                </div>
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div class="sig-label">دستخط کرایہ دار / Tenant Signature</div>
                </div>
            </div>

            <p style="text-align:center;font-size:10px;color:#94a3b8;margin-top:14px;">
                Generated {{ now()->format('d M Y, h:i A') }} · PropManager Property Management
            </p>
        </div>
        <div class="watermark">RENT</div>
    </div>
</div>

<script>
(function(){
    var shop   = "{{ addslashes($shopNum) }}";
    var market = "{{ addslashes($marketName) }}";
    var tnant  = "{{ addslashes($tenant) }}";
    var paid   = "{{ number_format($paidAmt, 0) }}";
    var rent   = "{{ number_format($rentAmt, 0) }}";
    var bal    = "{{ number_format($balance, 0) }}";
    var date   = "{{ \Carbon\Carbon::parse($payDate)->format('d M Y') }}";
    var rno    = "{{ $rno }}";
    var forMonth = "{{ \Carbon\Carbon::parse($payDate)->format('F Y') }}";
    var msg = '🔑 *PropManager - Rent Receipt*\n\n'
            + '📋 Receipt No: ' + rno + '\n'
            + '📅 Date: ' + date + '\n'
            + '📆 For Month: ' + forMonth + '\n'
            + '👤 Tenant: ' + tnant + '\n'
            + '🏬 Market: ' + market + ', Shop No: ' + shop + '\n'
            + '💵 Monthly Rent: Rs ' + rent + '\n'
            + '💰 Amount Paid: Rs ' + paid + '\n'
            + '📊 Balance: Rs ' + bal + '\n\n'
            + '✅ Rent received. Thank you!';
    document.getElementById('wa-btn').href = 'https://wa.me/?text=' + encodeURIComponent(msg);
})();
</script>
</body>
</html>
