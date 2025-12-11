<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
        }

        .invoice-details {
            text-align: right;
        }

        .billing-info {
            margin-bottom: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th {
            text-align: left;
            padding: 10px;
            background: #f9fafb;
            border-bottom: 1px solid #ddd;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">AuraAssets</div>
            <div class="invoice-details">
                <h1>INVOICE</h1>
                <p><strong>Invoice #:</strong> {{ $order->id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
            </div>
        </div>

        <div class="billing-info">
            <h3>Billed To:</h3>
            <p>{{ $order->user->name }}</p>
            <p>{{ $order->user->email }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $order->product->name }}</strong><br>
                        <span style="font-size: 12px; color: #666;">Sold by {{ $order->product->shop->name }}</span>
                    </td>
                    <td style="text-align: right;">{{ $order->product->formatted_price }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            Total: {{ $order->product->formatted_price }}
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>AuraAssets Marketplace • support@auraassets.com</p>
        </div>
    </div>
</body>

</html>