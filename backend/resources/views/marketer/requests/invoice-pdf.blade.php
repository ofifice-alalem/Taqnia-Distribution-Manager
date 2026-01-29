<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice</title>
    <style>
        @page { margin: 15px; }
        body { font-family: 'DejaVu Sans'; color: #333; font-size: 12px; margin: 0; }
        .header { text-align: center; margin-bottom: 15px; background-color: #6366f1; color: white; padding: 12px; border-radius: 6px; }
        .header h1 { margin: 0 0 5px 0; font-size: 20px; font-weight: bold; }
        .header h2 { margin: 0; font-size: 16px; color: #e0e7ff; font-weight: normal; }
        .info-box { background-color: #f8f9fa; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #e5e7eb; text-align: right; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 4px; font-size: 11px; text-align: right; }
        .label { font-weight: bold; color: #6366f1; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #6366f1; color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 12px; }
        td { border: 1px solid #e5e7eb; padding: 6px; background-color: #ffffff; font-size: 11px; text-align: center; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .total-box { background-color: #10b981; color: white; padding: 10px; border-radius: 6px; margin-top: 12px; text-align: center; }
        .total-box .amount { font-size: 20px; font-weight: bold; margin-top: 3px; }
        .signatures { position: fixed; bottom: 15px; left: 15px; right: 15px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 30px; margin: 0 2%; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>#{{ $invoiceNumber }}</h2>
    </div>

    <div class="info-box">
        <div class="info-row">
            {{ $marketerName }} :<span class="label">{{ $labels['marketer'] }}</span>
        </div>
        <div class="info-row">
            {{ $date }} :<span class="label">{{ $labels['date'] }}</span>
        </div>
        <div class="info-row">
            {{ $status }} :<span class="label">{{ $labels['status'] }}</span>
        </div>
        @if($keeperName)
        <div class="info-row">
            {{ $keeperName }} :<span class="label">{{ $labels['keeper'] }}</span>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ $labels['total'] }}</th>
                <th>{{ $labels['price'] }}</th>
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
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $index + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <div>{{ $labels['grandTotal'] }}</div>
        <div class="amount">{{ $labels['currency'] }} {{ number_format($total, 2) }}</div>
    </div>

    <div class="signatures">
        <div class="signature-box">{{ $labels['marketerSign'] }}</div>
        <div class="signature-box">{{ $labels['keeperSign'] }}</div>
    </div>
</body>
</html>
