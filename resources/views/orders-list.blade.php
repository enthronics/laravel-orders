<!-- resources/views/orders-list.blade.php -->
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilausten lista</title>
</head>
<body>
    <h1>Tilausten lista</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p><a href="/orders/create">➕ Lisää uusi tilaus</a></p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nimi</th>
                <th>Toistuva?</th>
                <th>Status</th>
                <th>Lisätiedot</th>
                <th>Luotu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_name }}</td>
                    <td>{{ $order->recurring ? 'Kyllä' : 'Ei' }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->meta ?? '-' }}</td>
                    <td>{{ $order->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Ei tilauksia vielä.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
