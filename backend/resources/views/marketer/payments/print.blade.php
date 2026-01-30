<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال قبض - {{ $payment->payment_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            padding: 20px;
            background: white;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        .header .receipt-number {
            font-size: 18px;
            color: #666;
            font-weight: 600;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 700;
            color: #333;
            width: 40%;
        }

        .info-value {
            color: #555;
            width: 60%;
            text-align: left;
        }

        .amount-box {
            background: #f8f9fa;
            border: 2px solid #333;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .amount-box .label {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .amount-box .amount {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .payment-method {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .payment-method.cash {
            background: #d4edda;
            color: #155724;
        }

        .payment-method.transfer {
            background: #d1ecf1;
            color: #0c5460;
        }

        .payment-method.check {
            background: #fff3cd;
            color: #856404;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: 600;
        }

        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .print-button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ طباعة
    </button>

    <div class="receipt">
        <div class="header">
            <h1>إيصال قبض</h1>
            <div class="receipt-number">رقم الإيصال: {{ $payment->payment_number }}</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">المتجر:</div>
                <div class="info-value">{{ $payment->store->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">المسوق:</div>
                <div class="info-value">{{ $payment->marketer->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">التاريخ:</div>
                <div class="info-value">{{ $payment->created_at ? $payment->created_at->format('Y-m-d H:i') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">طريقة الدفع:</div>
                <div class="info-value">
                    <span class="payment-method 
                        @if($payment->payment_method === 'cash') cash
                        @elseif($payment->payment_method === 'transfer') transfer
                        @else check
                        @endif">
                        @if($payment->payment_method === 'cash')
                            كاش
                        @elseif($payment->payment_method === 'transfer')
                            حوالة
                        @else
                            شيك مصدق
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="amount-box">
            <div class="label">المبلغ المدفوع</div>
            <div class="amount">{{ number_format($payment->amount, 2) }} ريال</div>
        </div>

        @if($payment->status == 'approved')
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">أمين المخزن:</div>
                <div class="info-value">{{ $payment->keeper->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">تاريخ التوثيق:</div>
                <div class="info-value">{{ $payment->confirmed_at ? $payment->confirmed_at->format('Y-m-d H:i') : '-' }}</div>
            </div>
        </div>
        @endif

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line">توقيع المسوق</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">توقيع أمين المخزن</div>
            </div>
        </div>
    </div>

    <script>
        // Auto print on load
        window.onload = function() { 
            window.print(); 
        }
    </script>
</body>
</html>
