<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice</title>
    <style>
        @page { margin: 20px; }
        body { font-family: 'DejaVu Sans'; color: #333; font-size: 13px; }
        .header { text-align: center; margin-bottom: 30px; background-color: #6366f1; color: white; padding: 20px; border-radius: 10px; }
        .header h1 { margin: 0 0 10px 0; font-size: 26px; font-weight: bold; }
        .header h2 { margin: 0; font-size: 20px; color: #e0e7ff; font-weight: normal; }
        .info-box { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e7eb; }
        .info-row { margin-bottom: 10px; padding: 5px 0; font-size: 14px; text-align: right; }
        .label { font-weight: bold; color: #6366f1; display: inline-block; width: 100px; font-size: 14px; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #6366f1; color: white; padding: 12px; text-align: center; font-weight: bold; font-size: 14px; }
        td { border: 1px solid #e5e7eb; padding: 10px; background-color: #ffffff; font-size: 13px; text-align: center; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .total-box { background-color: #10b981; color: white; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center; }
        .total-box .amount { font-size: 26px; font-weight: bold; margin-top: 5px; }
        .signatures { margin-top: 40px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 40px; margin: 0 2%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>#{{ $invoiceNumber }}</h2>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span>{{ $marketerName }}</span>
            <span class="label">{{ $labels['marketer'] }}</span>
        </div>
        <div class="info-row">
            <span>{{ $date }}</span>
            <span class="label">{{ $labels['date'] }}</span>
        </div>
        <div class="info-row">
            <span>{{ $status }}</span>
            <span class="label">{{ $labels['status'] }}</span>
        </div>
        @if($keeperName)
        <div class="info-row">
            <span>{{ $keeperName }}</span>
            <span class="label">{{ $labels['keeper'] }}</span>
        </div>
        <div class="info-row">
            <span>{{ $statusDate }}</span>
            <span class="label">{{ $labels['statusDate'] }}</span>
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
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $labels['currency'] }} {{ number_format($item->total, 2) }}</td>
                <td>{{ $labels['currency'] }} {{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->name }}</td>
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
