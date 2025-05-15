@php
    use App\Models\CommandHistory;
    use App\Models\Item;
    use App\Models\Payment;
    use App\Models\Server;
@endphp
    <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'Roboto';
            font-weight: normal;
            src: url('{{ storage_path('fonts/Roboto-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Roboto';
            font-weight: bold;
            src: url('{{ storage_path('fonts/Roboto-Bold.ttf') }}') format('truetype');
        }
        body {
            font-family: 'Roboto', sans-serif;
            margin: 1in;
            color: #333;
        }
        .center {
            text-align: center;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        h1 {
            font-size: 2em;
        }
        h2 {
            font-size: 1.5em;
        }
        h3 {
            font-size: 1.2em;
        }
        p {
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5em;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
            font-weight: 500;
        }
        img {
            max-width: 100%;
        }
    </style>
</head>
<body>
<div class="center">
    <img src="{{ asset('img/logo.png') }}" width="200" alt="Logo">
</div>
<h1>Payment Dispute/Chargeback Evidence</h1>
<h2>Payment #{{ $chargeback->payment->id }}</h2>
<p>This document reveals the evidence of receipt of virtual goods and services by the customer. The transaction in question has the following information:</p>

<h3>Payment Information</h3>
<p>
    Transaction ID: {{ $chargeback->sid }}<br>
    Total Price: {{ $chargeback->payment->price }} {{ $chargeback->payment->currency }}<br>
    Date: {{ $chargeback->creation_date }} ({{ date("P") }} GMT)<br>
    <?php $jsonEmail = json_decode($chargeback->payment->details, true); ?>
    Email: {{ isset($jsonEmail['email']) ? $jsonEmail['email'] : 'none' }}<br>
    IP Address: {{ $chargeback->payment->ip }}<br>
</p>

<h3>Products Information</h3>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($items as $item)
        <tr>
            <td><img src="{{ $item['image'] }}" width="50" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('res/img/question-icon.png') }}';"></td>
            <td>{{ $item['name'] }}</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ $item['price'] }} {{ $item['currency'] }}</td>
            <td>{{ $item['price'] * $item['quantity'] }} {{ $item['currency'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if($paymentCommandsHistory)
    <h3>Delivery Confirmation</h3>
    <p>We provide in-game perks that offer enhanced benefits for our users. To facilitate this, we execute commands on our gaming server to deliver products directly to our customers.</p>
    <p>Below is a table of commands that were executed/processed for this transaction:</p>
    <table>
        <thead>
        <tr>
            <th>Command</th>
            <th>Product</th>
            <th>Server</th>
            <th>Status</th>
            <th>Updated At</th>
        </tr>
        </thead>
        <tbody>
        @foreach($paymentCommandsHistory as $command)
            <tr>
                <td>{{ $command->cmd }}</td>
                <td>
                    @if($command->type == CommandHistory::TYPE_ITEM)
                        @php($itemCMD = Item::where('id', $command->item_id)->select('name')->first())
                        {{ empty($itemCMD) ? __('Global Command') : $itemCMD->name }}
                    @elseif ($command->type == CommandHistory::TYPE_GLOBAL)
                        {{ __('Global command') }}
                    @elseif ($command->type == CommandHistory::TYPE_REF)
                        {{ __('Referral command') }}
                    @elseif ($command->type == CommandHistory::TYPE_VIRTUAL_CURRENCY)
                        {{ __('Virtual currency') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php($server = Server::where('id', $command->server_id)->select('name')->first())
                    {{ $server ? $server->name : __('Server not found') }}
                </td>
                <td>
                    @if($command->status == CommandHistory::STATUS_EXECUTED)
                        <span>{{ __('Executed') }}</span>
                    @elseif ($command->status == CommandHistory::STATUS_QUEUE)
                        <span>{{ __('Queue') }}</span>
                    @elseif ($command->status == CommandHistory::STATUS_PENDING)
                        <span>{{ __('Pending') }}</span>
                    @else
                        <span>{{ __('UNKNOWN') }}</span>
                    @endif
                </td>
                <td>{{ $command->updated_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<h3>Additional Information</h3>
@if (count($payments) > 0)
    <p>Along with this payment, the customer has made {{ count($payments) }} payments on this webstore for similar items. Each transaction provides further evidence that the customer’s intentions were clear when making these payments.</p>

    @foreach ($payments as $payment)
        <p>
            Gateway: {{ $payment->gateway }}<br>
            Amount: {{ $payment->price }} {{ $payment->currency }}<br>
            Date: {{ $payment->created_at }} ({{ date("P") }} GMT)<br>
                <?php $jsonEmail = json_decode($payment->details, true); ?>
            Email: {{ isset($jsonEmail['email']) ? $jsonEmail['email'] : 'none' }}<br>
            IP Address: {{ $payment->ip }}<br>
            Username: {{ $payment->user->username }}<br>
            UUID: {{ $payment->user->uuid }}<br>
            Status: Completed<br>
        </p>
    @endforeach
@else
    <p>No additional payments were found for this customer.</p>
@endif

<p>Regarding this, we ask you to review this evidence and make a decision about this case.</p>

<p>Best regards,<br>
    <strong>{{ $settings->site_name }}</strong></p>
</body>
</html>
