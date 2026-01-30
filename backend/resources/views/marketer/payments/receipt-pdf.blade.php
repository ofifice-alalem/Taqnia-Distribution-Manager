<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Receipt</title>
    <style>
        @page { margin: 15px; }
        body { font-family: 'DejaVu Sans'; color: #333; font-size: 12px; margin: 0; }
        .header { margin-bottom: 15px; background-color: #333; color: white; padding: 12px; border-radius: 6px; display: table; width: 100%; }
        .header-right { display: table-cell; text-align: right; width: 50%; vertical-align: middle; }
        .header-left { display: table-cell; text-align: left; width: 50%; vertical-align: middle; }
        .header h1 { margin: 0; font-size: 20px; font-weight: bold; }
        .header h2 { margin: 0; font-size: 20px; font-weight: bold; color: white; }
        .info-box { background-color: #f8f9fa; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #333; text-align: right; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 4px; font-size: 11px; text-align: right; }
        .label { font-weight: bold; color: #333; font-size: 11px; }
        .amount-box { background-color: #f8f9fa; border: 2px solid #333; padding: 20px; text-align: center; margin: 20px 0; border-radius: 6px; }
        .amount-box .amount-label { font-size: 14px; color: #666; margin-bottom: 10px; font-weight: bold; }
        .amount-box .amount { font-size: 32px; font-weight: bold; color: #333; }
        .signatures { position: fixed; bottom: 15px; left: 15px; right: 15px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 30px; margin: 0 2%; font-size: 10px; }
        .cancelled-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(255, 0, 0, 0.3); font-weight: bold; z-index: 1000; white-space: nowrap; }
    </style>
</head>
<body>
    @if(isset($cancelled) && $cancelled)
    <div class="cancelled-watermark">{{ $cancelledText }}</div>
    @endif
    
    <div class="header">
        <div class="header-left">
            <h2>#{{ $paymentNumber }}</h2>
        </div>
        <div class="header-right">
            <h1>{{ $title }}</h1>
        </div>
    </div>

    <div class="info-box">
        <div class="info-row">
            {{ $marketerName }} :<span class="label">{{ $labels['marketer'] }}</span>
        </div>
        <div class="info-row">
            {{ $date }} :<span class="label">{{ $labels['date'] }}</span>
        </div>
        <div class="info-row">
            {{ $storeName }} :<span class="label">{{ $labels['store'] }}</span>
        </div>
        <div class="info-row">
            {{ $storeOwner }} :<span class="label">{{ $labels['owner'] }}</span>
        </div>
        <div class="info-row">
            {{ $paymentMethod }} :<span class="label">{{ $labels['paymentMethod'] }}</span>
        </div>
        @if($keeperName)
        <div class="info-row">
            {{ $keeperName }} :<span class="label">{{ $labels['keeper'] }}</span>
        </div>
        @endif
        @if($confirmedAt)
        <div class="info-row">
            {{ $confirmedAt }} :<span class="label">{{ $labels['confirmedAt'] }}</span>
        </div>
        @endif
    </div>

    <div class="amount-box">
        <div class="amount-label">{{ $labels['amount'] }}</div>
        <div class="amount">{{ $labels['currency'] }} {{ number_format($amount, 2) }}</div>
    </div>

    <div class="signatures">
        <div class="signature-box">{{ $labels['marketerSignature'] }}</div>
        <div class="signature-box">{{ $labels['keeperSignature'] }}</div>
    </div>
</body>
</html>
