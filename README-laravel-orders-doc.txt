laravel-orders
==============

Lyhyt kuvaus:
-------------
Laravel-sovellus tilausten hallintaan. Sovellus toimii Docker-ympäristössä ja tarjoaa REST API:n tilausten luomiseen, muokkaamiseen ja hakemiseen.

Tiedostorakenne:
----------------
laravel-orders/
│
├─ app/                 # Laravel-sovelluslogiikka (Controllers, Models, Services)
├─ bootstrap/           # Sovelluksen bootstrap-tiedostot
├─ config/              # Konfiguraatiotiedostot
├─ database/            # Migraatiot ja seedit
│  ├─ migrations/
│  └─ seeders/
├─ public/              # Julkinen kansio (index.php, assets)
├─ resources/           # Views, lang-tiedostot, frontend-resurssit
├─ routes/              # API- ja web-reitit
├─ storage/             # Logs, cache ja muut tallennukset
├─ tests/               # Unit- ja Feature-testit
├─ docker/              # Dockerfile ja mahdolliset configit
├─ .env.example         # Ympäristömuuttujien esimerkkitiedosto
├─ composer.json        # PHP riippuvuudet
├─ package.json         # Node.js riippuvuudet (frontend)
├─ README-laravel-orders.md  # Käyttö- ja Docker-ohjeet
└─ docker-compose.yml   # Docker-compose määrittely

Dokumentaatio:
--------------
1. Sovellus käynnistetään Dockerissa komennolla:
   docker compose up -d
   (katso README-laravel-orders.md)

2. API-päätepisteet (lyhyt yhteenveto):
   - GET /orders          : Hae kaikki tilaukset
   - POST /orders         : Luo uusi tilaus
   - GET /orders/{id}     : Hae yksittäinen tilaus
   - PUT /orders/{id}     : Päivitä tilaus
   - DELETE /orders/{id}  : Poista tilaus

3. Git-ohjeet:
   git init
   git add .
   git commit -m "Initial commit - laravel-orders project"
   (Tyypillisesti työntö etärepoon: git push origin main)

Huom:
-----
README-laravel-orders.md sisältää tarkemmat ohjeet sovelluksen käynnistämiseen ja Docker-ympäristön käyttöön.
