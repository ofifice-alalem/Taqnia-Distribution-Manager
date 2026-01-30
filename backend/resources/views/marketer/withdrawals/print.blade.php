<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Withdrawal Request</title>
    <style>
        @page { margin: 15px; }
        body { font-family: 'DejaVu Sans'; color: #333; font-size: 12px; margin: 0; position: relative; }
        .header { margin-bottom: 15px; background-color: #333; color: white; padding: 12px; border-radius: 6px; display: table; width: 100%; }
        .header-right { display: table-cell; text-align: right; width: 50%; vertical-align: middle; }
        .header-left { display: table-cell; text-align: left; width: 50%; vertical-align: middle; }
        .header h1 { margin: 0; font-size: 20px; font-weight: bold; }
        .header h2 { margin: 0; font-size: 20px; font-weight: bold; color: white; }
        .info-box { background-color: #f8f9fa; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #333; text-align: right; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 8px; font-size: 11px; text-align: right; }
        .label { font-weight: bold; color: #333; font-size: 11px; }
        .balance-section { margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #333; color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 12px; }
        td { border: 1px solid #333; padding: 8px; background-color: #ffffff; font-size: 11px; text-align: center; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .highlight-row td { background-color: #fff3cd; font-weight: bold; font-size: 12px; }
        .signatures { position: fixed; bottom: 15px; left: 15px; right: 15px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 30px; margin: 0 2%; font-size: 10px; }
        .cancelled-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(220, 53, 69, 0.15);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
        .cancelled-note {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-top: 15px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    @if(in_array($statusCode, ['rejected', 'cancelled']))
    <div class="cancelled-watermark">{{ $labels['cancelledNote'] }}</div>
    @endif

    <div class="header">
        <div class="header-left">
            <h2>#{{ $requestNumber }}</h2>
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
            {{ $status }} :<span class="label">{{ $labels['status'] }}</span>
        </div>
    </div>

    @if(in_array($statusCode, ['rejected', 'cancelled']))
    <div class="cancelled-note">
        {{ $labels['cancelledNote'] }}
    </div>
    @endif

    <div class="balance-section">
        @if($statusCode === 'pending')
        <table>
            <thead>
                <tr>
                    <th>{{ $labels['remaining'] }}</th>
                    <th>{{ $labels['requestedAmount'] }}</th>
                    <th>{{ $labels['availableBalance'] }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $labels['currency'] }} {{ $remaining }}</td>
                    <td class="highlight-row"><strong>{{ $labels['currency'] }} {{ $requestedAmount }}</strong></td>
                    <td>{{ $labels['currency'] }} {{ $availableBalance }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <table>
            <thead>
                <tr>
                    <th>{{ $labels['totalEarned'] }}</th>
                    <th>{{ $labels['totalWithdrawn'] }}</th>
                    <th>{{ $labels['availableBalance'] }}</th>
                    <th>{{ $labels['requestedAmount'] }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $labels['currency'] }} {{ $totalEarned }}</td>
                    <td>{{ $labels['currency'] }} {{ $totalWithdrawn }}</td>
                    <td>{{ $labels['currency'] }} {{ $availableBalance }}</td>
                    <td class="highlight-row"><strong>{{ $labels['currency'] }} {{ $requestedAmount }}</strong></td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>

    <div class="signatures">
        <div class="signature-box">{{ $labels['marketerSign'] }}</div>
        <div class="signature-box">{{ $labels['adminSign'] }}</div>
    </div>
</body>
</html>
