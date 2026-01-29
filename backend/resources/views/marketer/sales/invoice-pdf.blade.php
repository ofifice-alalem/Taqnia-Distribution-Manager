<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice</title>
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
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #333; color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 12px; }
        td { border: 1px solid #333; padding: 6px; background-color: #ffffff; font-size: 11px; text-align: center; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .total-row { background-color: #f0f0f0; font-weight: bold; font-size: 12px; }
        .total-row td { background-color: #f0f0f0; color: #000; border: 1px solid #333; font-weight: bold; }
        .signatures { position: fixed; bottom: 15px; left: 15px; right: 15px; }
        .signature-box { display: inline-block; width: 30%; text-align: center; border-top: 1px solid #000; padding-top: 30px; margin: 0 1%; font-size: 10px; }
        .cancelled-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(255, 0, 0, 0.3); font-weight: bold; z-index: 1000; white-space: nowrap; }
    </style>
</head>
<body>
    @if(isset($cancelled) && $cancelled)
    <div class="cancelled-watermark">{{ $cancelledText }}</div>
    @endif
    
    <div class="header">
        <div class="header-left">
            <h2>#{{ $invoiceNumber }}</h2>
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
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ $labels['total'] }}</th>
                <th>{{ $labels['price'] }}</th>
                <th>{{ $labels['discount'] }}</th>
                <th>{{ $labels['quantity'] }}</th>
                <th>{{ $labels['product'] }}</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $labels['currency'] }} {{ number_format($item->total, 2) }}</td>
                <td>{{ $labels['currency'] }} {{ number_format($item->price, 2) }}</td>
                <td>
                    @if($item->freeQty > 0)
                        {{ $item->freeQty }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $index + 1 }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>{{ $labels['currency'] }} {{ number_format($total, 2) }}</td>
                <td colspan="5">{{ $labels['grandTotal'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">{{ $labels['marketer'] }}</div>
        <div class="signature-box">{{ $labels['keeper'] }}</div>
        <div class="signature-box">{{ $labels['store'] }}</div>
    </div>
</body>
</html>
