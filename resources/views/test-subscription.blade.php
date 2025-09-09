<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Subscription API</title>
    <style>
        /* Perus tyylit sivulle */
        body { font-family: system-ui, sans-serif; background: #f9f9f9; padding: 30px; }
        input, textarea, button { display: block; margin-top: 10px; width: 320px; padding: 6px; }
        button { cursor: pointer; }
        pre { background: #fff; padding: 15px; border: 1px solid #ccc; max-width: 800px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test Subscription API</h1>

    <!-- Lomake subscriptionin lähettämiseen -->
    <form id="subscriptionForm">
        <!-- Subscription-nimi -->
        <label>Tilauksen nimi:
            <input type="text" id="subscriptionName" required placeholder="Kirjoita tilauksen nimi">
        </label>

        <!-- Toistuva tilaus -->
        <label>
            Toistuva tilaus:
            <input type="checkbox" id="recurring">
        </label>

        <!-- Meta JSON-kenttä (valinnainen) -->
        <label>Meta (JSON):
            <textarea id="meta" placeholder='{"note":"test"}'></textarea>
        </label>

        <button type="submit">Lähetä tilaus</button>
    </form>

    <h2>Vastaus palvelimelta:</h2>
    <pre id="response">Täytä lomake ja lähetä tilaus.</pre>

    <script>
        // Haetaan lomake ja vastaus-elementti
        const responseEl = document.getElementById('response');
        const subscriptionForm = document.getElementById('subscriptionForm');

        // Lomakkeen submit-tapahtuman käsittelijä
        subscriptionForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // estetään lomakkeen normaali lähetys
            responseEl.textContent = "";
            responseEl.className = "";

            // Haetaan lomakkeen syötetyt arvot
            const subscriptionName = document.getElementById('subscriptionName').value.trim();
            if (!subscriptionName) {
                responseEl.textContent = "Tilauksen nimi ei voi olla tyhjä!";
                responseEl.className = "error";
                return;
            }

            // Käsitellään meta JSON (valinnainen)
            let meta = null;
            const metaValue = document.getElementById('meta').value.trim();
            if (metaValue) {
                try { 
                    meta = JSON.parse(metaValue); 
                } catch { 
                    // Virheellinen JSON
                    responseEl.textContent = "Meta JSON on virheellinen!";
                    responseEl.className = "error";
                    return;
                }
            }

            // Payload lähetettäväksi palvelimelle
            const payload = {
                subscription: subscriptionName,
                recurring: document.getElementById('recurring').checked,
                meta: meta
            };

            try {
                // Lähetetään POST-pyyntö API:in
                const res = await fetch('/api/submit-subscription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                // Luetaan ensin raw-teksti
                const text = await res.text(); 

                // Yritetään parsia JSONiksi
                let data;
                try { 
                    data = JSON.parse(text); 
                } catch {
                    // Jos palvelin ei palauta JSONia, näytetään raw-data virheilmoituksessa
                    responseEl.textContent = "Palvelin palautti ei-JSONia:\n" + text;
                    responseEl.className = "error";
                    return;
                }

                // Tarkistetaan palvelimen palauttama status
                if (!res.ok || !data.success) {
                    responseEl.textContent = data.message || "Tuntematon virhe palvelimella";
                    responseEl.className = "error";
                    return;
                }

                // Onnistunut lähetys, näytetään JSON siististi
                responseEl.textContent = JSON.stringify(data, null, 2);
                responseEl.className = "success";

                // Tyhjennetään lomake
                subscriptionForm.reset();

            } catch (err) {
                // Yhteys- tai fetch-virhe
                responseEl.textContent = "Virhe yhteydessä palvelimeen!\n" + err;
                responseEl.className = "error";
            }
        });
    </script>
</body>
</html>
