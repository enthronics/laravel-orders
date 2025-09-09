<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilauslista</title>
</head>
<body>
    <h1>Kaikki tilaukset</h1>

    @if ($orders->isEmpty())
        <p>Ei vielä yhtään tilausta.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nimi</th>
                    <th>Jatkuva tilaus</th>
                    <th>Status</th>
                    <th>Luotu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->order_name }}</td>
                        <td>{{ $order->recurring ? 'Kyllä' : 'Ei' }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p><a href="/test-orders">Palaa testilomakkeeseen</a></p>
</body>
</html>
