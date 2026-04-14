<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucfirst($entry->transaction_type) }} Receipt – #{{ $entry->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc; }
        .page { max-width: 640px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,.08); }
        .header { padding: 32px 30px 24px; text-align: center; color: white; }
        .header.sell-header  { background: linear-gradient(135deg, #dc2626, #b91c1c); }
        .header.buy-header   { background: linear-gradient(135deg, #16a34a, #15803d); }
        .logo-text { font-size: 26px; font-weight: 800; letter-spacing: -1px; }
        .header-sub { font-size: 12px; opacity: .8; margin-top: 2px; }
        .badge { display: inline-block; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); color: white; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 10px; letter-spacing: .5px; }
        .ref-box { background: #f8fafc; border: 2px dashed #e2e8f0; margin: 20px 24px 0; border-radius: 12px; text-align: center; padding: 12px; }
        .ref-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .ref-number { font-size: 18px; font-weight: 800; color: #334155; font-family: monospace; margin-top: 3px; }
        .body { padding: 20px 24px 24px; }
        .section-title { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #f1f5f9; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .info-grid.three { grid-template-columns: 1fr 1fr 1fr; }
        .info-item { background: #f8fafc; border-radius: 10px; padding: 10px 12px; }
        .info-item .label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .info-item .value { font-size: 13px; font-weight: 600; color: #1e293b; }
        .amount-card { border-radius: 14px; padding: 22px; text-align: center; margin: 18px 0; color: white; }
        .amount-card.sell-card { background: linear-gradient(135deg, #dc2626, #b91c1c); }
        .amount-card.buy-card  { background: linear-gradient(135deg, #16a34a, #15803d); }
        .amount-label { font-size: 12px; opacity: .85; margin-bottom: 5px; }
        .amount-value { font-size: 38px; font-weight: 800; }
        .party-card { border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; }
        .seller-card { background: #fff7ed; border-left: 4px solid #f97316; }
        .buyer-card  { background: #f0fdf4; border-left: 4px solid #22c55e; }
        .party-role  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
        .seller-card .party-role { color: #c2410c; }
        .buyer-card  .party-role { color: #15803d; }
        .party-name  { font-size: 15px; font-weight: 700; color: #1e293b; }
        .party-meta  { font-size: 11px; color: #64748b; margin-top: 3px; }
        .notes-box { background: #fef9c3; border-left: 3px solid #fbbf24; border-radius: 0 8px 8px 0; padding: 10px 12px; margin: 14px 0; }
        .notes-box .label { font-size: 10px; color: #92400e; font-weight: 600; margin-bottom: 3px; }
        .footer { border-top: 1px dashed #e2e8f0; padding: 16px 24px; text-align: center; color: #94a3b8; font-size: 11px; }
        .print-btn { display: block; margin: 20px auto 30px; padding: 10px 28px; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: white; }
        .btn { padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; }
        .btn-whatsapp { background: #25D366; color: white; }
        .btn-back { background: #f1f5f9; color: #475569; }
        .action-bar { display: flex; gap: 10px; justify-content: center; margin: 20px 0 0; flex-wrap: wrap; }
        .divider { height: 1px; background: #f1f5f9; margin: 16px 0; }
        @media print {
            body { background: white; }
            .page { box-shadow: none; margin: 0; border-radius: 0; }
            .print-btn, .back-link { display: none !important; }
        }
    </style>
</head>
<body>


@php
    $isSell    = $entry->transaction_type === 'sell';
    $typeLabel = $isSell ? 'SOLD' : 'PURCHASED';
    $typeClass = $isSell ? 'sell' : 'buy';
    $entryIcon = ['shop' => '🏪', 'plot' => '🗺', 'car' => '🚗'][$entry->entry_type] ?? '📋';

    $sellerName  = $entry->sellerCustomer->name ?? $entry->sellerOwner->name ?? $entry->seller_name ?? null;
    $sellerCnic  = $entry->sellerCustomer->cnic ?? $entry->sellerOwner->cnic ?? $entry->seller_cnic ?? null;
    $sellerPhone = $entry->sellerCustomer->phone ?? $entry->sellerOwner->phone ?? $entry->seller_phone ?? null;

    $buyerName  = $entry->buyerCustomer->name ?? $entry->buyerOwner->name ?? $entry->buyer_name ?? null;
    $buyerCnic  = $entry->buyerCustomer->cnic ?? $entry->buyerOwner->cnic ?? $entry->buyer_cnic ?? null;
    $buyerPhone = $entry->buyerCustomer->phone ?? $entry->buyerOwner->phone ?? $entry->buyer_phone ?? null;
@endphp

<div class="page">
    <div class="header {{ $typeClass }}-header">
        <div class="logo-text">JK</div>

        <span class="badge">{{ $entryIcon }} {{ $typeLabel }} · {{ strtoupper($entry->entry_type) }}</span>
    </div>

    <div class="ref-box">
        <div class="ref-label">Transaction Reference</div>
        <div class="ref-number">TXN-{{ str_pad($entry->id, 7, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="body">

        <div class="section-title">Transaction Details</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Date</div>
                <div class="value">{{ $entry->date->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Type</div>
                <div class="value">{{ ucfirst($entry->entry_type) }} {{ ucfirst($entry->transaction_type) }}</div>
            </div>
            @if($entry->sellMarket)
            <div class="info-item">
                <div class="label">Market</div>
                <div class="value">{{ $entry->sellMarket->name }}</div>
            </div>
            @endif
            @if($entry->shop_or_item_number)
            <div class="info-item">
                <div class="label">Item / Shop #</div>
                <div class="value">{{ $entry->shop_or_item_number }}</div>
            </div>
            @endif
        </div>

        {{-- Car specific --}}
        @if($entry->entry_type === 'car')
        <div class="section-title">Vehicle Details</div>
        <div class="info-grid three">
            @if($entry->car_make)
            <div class="info-item">
                <div class="label">Make</div>
                <div class="value">{{ $entry->car_make }}</div>
            </div>
            @endif
            @if($entry->car_model)
            <div class="info-item">
                <div class="label">Model</div>
                <div class="value">{{ $entry->car_model }}</div>
            </div>
            @endif
            @if($entry->car_year)
            <div class="info-item">
                <div class="label">Year</div>
                <div class="value">{{ $entry->car_year }}</div>
            </div>
            @endif
            @if($entry->car_registration)
            <div class="info-item" style="grid-column: 1/-1;">
                <div class="label">Registration</div>
                <div class="value">{{ $entry->car_registration }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Plot sqft --}}
        @if($entry->entry_type === 'plot' && ($entry->sqft || $entry->per_sqft_rate))
        <div class="section-title">Area Details</div>
        <div class="info-grid">
            @if($entry->sqft)
            <div class="info-item">
                <div class="label">Area (sqft)</div>
                <div class="value">{{ number_format($entry->sqft, 0) }} sqft</div>
            </div>
            @endif
            @if($entry->per_sqft_rate)
            <div class="info-item">
                <div class="label">Rate / sqft</div>
                <div class="value">Rs {{ number_format($entry->per_sqft_rate, 0) }}</div>
            </div>
            @endif
        </div>
        @endif

        <div class="amount-card {{ $typeClass }}-card">
            <div class="amount-label">Total Transaction Amount</div>
            <div class="amount-value">Rs {{ number_format($entry->total, 0) }}</div>
        </div>

        {{-- Seller --}}
        @if($sellerName)
        <div class="section-title">Parties Involved</div>
        <div class="party-card seller-card">
            <div class="party-role">🔴 Seller</div>
            <div class="party-name">{{ $sellerName }}</div>
            <div class="party-meta">
                @if($sellerCnic) CNIC: {{ $sellerCnic }} @endif
                @if($sellerPhone) &nbsp;· 📞 {{ $sellerPhone }} @endif
            </div>
        </div>
        @endif

        {{-- Buyer --}}
        @if($buyerName)
        <div class="party-card buyer-card">
            <div class="party-role">🟢 Buyer</div>
            <div class="party-name">{{ $buyerName }}</div>
            <div class="party-meta">
                @if($buyerCnic) CNIC: {{ $buyerCnic }} @endif
                @if($buyerPhone) &nbsp;· 📞 {{ $buyerPhone }} @endif
            </div>
        </div>
        @endif

        @if($entry->notes)
        <div class="notes-box">
            <div class="label">Notes / Remarks</div>
            <div style="font-size:12px;color:#78350f;margin-top:2px;">{{ $entry->notes }}</div>
        </div>
        @endif

        @if($entry->received_by || $entry->paid_to || $entry->authorized_by)
        <div class="section-title">Payment Personnel</div>
        <div class="info-grid">
            @if($entry->received_by)
            <div class="info-item">
                <div class="label">Received By</div>
                <div class="value">{{ $entry->received_by }}</div>
            </div>
            @endif
            @if($entry->paid_to)
            <div class="info-item">
                <div class="label">Paid To / Vendor</div>
                <div class="value">{{ $entry->paid_to }}</div>
            </div>
            @endif
            @if($entry->authorized_by)
            <div class="info-item">
                <div class="label">Authorized By</div>
                <div class="value">{{ $entry->authorized_by }}</div>
            </div>
            @endif
        </div>
        @endif

    </div>

    <div class="footer">
        <p>This is a computer-generated receipt. No physical signature required.</p>
        <p style="margin-top:4px;">Generated on {{ now()->format('d M Y, h:i A') }} · JK</p>
    </div>
</div>

<div class="action-bar">
    <a href="javascript:history.back()" class="btn btn-back">← Back</a>
    <button class="print-btn {{ $typeClass }}" onclick="window.print()">🖨 Print Receipt</button>
    <a id="wa-btn" href="#" target="_blank" class="btn btn-whatsapp">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Share on WhatsApp
    </a>
</div>

<script>
(function(){
    var seller = "{{ addslashes($sellerName ?? '') }}";
    var buyer  = "{{ addslashes($buyerName ?? '') }}";
    var amount = "{{ number_format($entry->amount ?? 0, 0) }}";
    var date   = "{{ \Carbon\Carbon::parse($entry->date ?? $entry->created_at)->format('d M Y') }}";
    var type   = "{{ strtoupper($entry->transaction_type ?? '') }}";
    var etype  = "{{ ucfirst($entry->entry_type ?? '') }}";
    var ref    = "{{ $entry->id }}";
    var market = "{{ addslashes($entry->market->name ?? '') }}";
    var msg = '🏪 JK - ' + type + ' Receipt*\n\n'
            + '📋 Ref: #' + ref + '\n'
            + '📅 Date: ' + date + '\n'
            + '📦 Type: ' + etype + '\n'
            + (seller ? '👤 Seller: ' + seller + '\n' : '')
            + (buyer  ? '👤 Buyer: '  + buyer  + '\n' : '')
            + (market ? '🏬 Market: ' + market + '\n' : '')
            + '💰 Amount: Rs ' + amount + '\n\n'
            + '✅ Transaction recorded. Thank you!';
    document.getElementById('wa-btn').href = 'https://wa.me/?text=' + encodeURIComponent(msg);
})();
</script>

</body>
</html>
