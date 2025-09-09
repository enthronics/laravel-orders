<!-- resources/views/create-order.blade.php -->
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Uusi tilaus</title>
</head>
<body>
    <h1>Luo uusi tilaus</h1>

    <form action="/orders" method="POST">
        @csrf <!-- Laravelin turvakenttä -->

        <label for="order_name">Tilausnimi:</label><br>
        <input type="text" name="order_name" id="order_name" required><br><br>

        <label for="recurring">Onko tilaus toistuva?</label>
        <input type="checkbox" name="recurring" id="recurring" value="1"><br><br>

        <label for="meta">Lisätiedot:</label><br>
        <textarea name="meta" id="meta"></textarea><br><br>

        <button type="submit">💾 Tallenna</button>
    </form>

    <p><a href="/orders">⬅ Takaisin tilauslistaan</a></p>
</body>
</html>
