<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Orders API</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f9f9f9; padding: 30px; }
        input, textarea, button { display: block; margin-top: 10px; width: 300px; padding: 5px; }
        pre { background: #fff; padding: 15px; border: 1px solid #ccc; max-width: 800px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test Orders API</h1>

    <form id="orderForm">
        @csrf
        <label>Tilauksen nimi:
            <input type="text" id="orderName" required placeholder="Kirjoita tilauksen nimi">
        </label>

        <label>
            Toistuva tilaus:
            <input type="checkbox" id="recurring">
        </label>

        <label>Meta (JSON):
            <textarea id="meta" placeholder='{"note":"test"}'></textarea>
        </label>

        <button type="submit">Lähetä tilaus</button>
    </form>

    <h2>Vastaus palvelimelta:</h2>
    <pre id="response">Täytä lomake ja lähetä tilaus.</pre>

    <script>
        const responseEl = document.getElementById('response');
        const orderForm = document.getElementById('orderForm');

        orderForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            responseEl.textContent = "";
            responseEl.className = "";

            const orderName = document.getElementById('orderName').value.trim();
            if (!orderName) {
                responseEl.textContent = "Tilauksen nimi ei voi olla tyhjä!";
                responseEl.className = "error";
                return;
            }

            const payload = {
                order: orderName,
                recurring: document.getElementById('recurring').checked,
                meta: null
            };

            const metaValue = document.getElementById('meta').value.trim();
            if (metaValue) {
                try {
                    payload.meta = JSON.parse(metaValue);
                } catch {
                    responseEl.textContent = "Meta JSON on virheellinen!";
                    responseEl.className = "error";
                    return;
                }
            }

            try {
                const token = document.querySelector('input[name="_token"]').value;

                const res = await fetch('/api/submit-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                const text = await res.text();
                let data;
                try { data = JSON.parse(text); } catch { data = null; }

                if (!res.ok || !data?.success) {
                    responseEl.textContent = data?.message || text;
                    responseEl.className = "error";
                    return;
                }

                responseEl.textContent = JSON.stringify(data, null, 2);
                responseEl.className = "success";

                orderForm.reset();

            } catch (err) {
                responseEl.textContent = "Virhe yhteydessä palvelimeen!\n" + err;
                responseEl.className = "error";
            }
        });
    </script>
</body>
</html>
