<!doctype html>

<html lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order Item - {{ $order->uuid }}</title>

    <style>
        html, body {
            font-family:  -apple-system, serif;
        }

        body {
            font-size: 8px !important;
        }

        .receipt-header {
            margin-bottom: 45px;
            width: 100%;
        }

        .details-section {
            display: block;
            width: 100%;
        }

        .customer-details, .address-details {
            width: 45%;
        }

        .customer-details {
            display: block;
            float: left;
            margin-right: 30px;
        }

        .address-details {
            display: block;
            float: right;
            margin-left: 30px;
        }

        .customer-details > .title, .address-details > .title {
            margin-bottom: 10px;
        }

        .customer-details > .content, .address-details > .content {
            padding: 10px;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }

        .content > p {
            margin: 0;
            line-height: 1.4;
        }

        .order-items {
            display: block;
            margin-top: 130px;
        }

        .order-items table {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            font-size: 8px !important;
            border-spacing: 0;
            border-collapse: collapse;
        }

        table td, table th {
            padding: .35rem;
            vertical-align: middle;
            border-left: 1px solid #e5e5e5;
            border-top: 1px solid #e5e5e5;
        }

        table td:last-child, table th:last-child {
            border-right: 1px solid #e5e5e5;
        }

        table tbody tr:last-child td {
            border-bottom: 1px solid #e5e5e5;
        }

        table thead th {
            vertical-align: bottom;
            border-bottom: 1px solid #e5e5e5;
        }

        .order-total {
            display: block;
            width: 100%;
        }
    </style>

    <body>
        <main>
            <header class="receipt-header">
                <div style="float: left; margin-right: 30px;">{{ config('app.name') }}</div>

                <div style="float: right; width: 45%; margin-left: 30px;">
                    <div style="font-size: 8px">
                        <strong>Date:</strong> {{ $order->created_at->format('d-m-Y') }}
                    </div>
                    <div style="font-size: 8px">
                        <strong>Invoice #:</strong> {{ $order->uuid }}
                    </div>
                </div>
            </header>

            <section class="details-section">
                <div class="customer-details">
                    <div class="title">Customer Details</div>

                    <div class="content">
                        <p>First name: {{ $order->user->first_name }}</p>
                        <p>Last name: {{ $order->user->last_name }}</p>
                        <p>ID: {{ $order->user->uuid }}</p>
                        <p>Phone number: {{ $order->user->phone_number }}</p>
                        <p>Email: {{ $order->user->email }}</p>
                        <p>Address: {{ $order->user->address }}</p>
                    </div>
                </div>

                <div class="address-details">
                    <div class="title">Billing/Shipping Details</div>

                    <div class="content">
                        <p>Billing: {{ $order->address['billing'] }}</p>
                        <p>Shipping: {{ $order->address['shipping'] }}</p>
                        <br />
                        <p>Payment method: {{ strtoupper($order->payment->type) }}</p>
                        @if($order->payment->type === \App\Models\Payment::TYPE_BANK_TRANSFER)
                            <p>IBAN: {{ $order->payment->details['iban'] }}</p>
                            <p>Account name: {{ $order->payment->details['name'] }}</p>
                            <p>Swift: {{ $order->payment->details['swift'] }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="order-items">
                <p>Items:</p>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: left">#</th>
                            <th style="width: 20%">ID</th>
                            <th style="width: 45%">Item Name</th>
                            <th style="width: 8%">Quantity</th>
                            <th style="width: 10%">Unit Price</th>
                            <th style="width: 12%">Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($order->products as $product)
                            <tr>
                                <td style="text-align: left">{{ $loop->index + 1 }}</td>
                                <td>{{ $product['uuid'] }}</td>
                                <td>{{ $product['product'] }}</td>
                                <td style="text-align: center">{{ $product['quantity'] }}</td>
                                <td>$ {{ $product['price'] }}</td>
                                <td>$ {{ number_format($product['price'] * $product['quantity'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <aside class="order-total">
                    <div style="float: right; width: 200px">
                        <p>Total:</p>

                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td>$ {{ number_format($orderSubTotal, 2) }}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>Delivery fee</strong>
                                    </td>
                                    <td>$ {{ number_format($order->delivery_fee, 2) }}</td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>TOTAL</strong>
                                    </td>
                                    <td>
                                        <strong>
                                            $ {{ number_format($orderSubTotal + $order->delivery_fee,  2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>
