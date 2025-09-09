\# Laravel Orders \& Subscriptions



Tekijä: Kristian Kouvo  

Sähköposti: kristian.kouvo@example.com  

Puhelin: +35840-3630603  

Päivämäärä: 9.9.2025



Tämä on yksinkertainen Laravel-projekti tilausten ja subscriptionien hallintaan Docker-ympäristössä.  

Kaikki toiminnot on testattu toimimaan mock-API:lla.



---



\## Aloitus



1\. Kloonaa repository ja siirry projektiin:

git clone https://github.com/enthronics/laravel-orders.git  

cd laravel-orders/src



2\. Kopioi ".env.example" tiedosto ja nimeä se ".env"

cp .env.example .env



3\. Käynnistä Docker-ympäristö:

docker-compose up -d



4\. Asenna PHP-riippuvuudet:

docker exec -it laravel\_app composer install



---



\## Käyttö



\- Testaa tilaukset:

http://localhost:8000/test-orders



\- Testaa subscriptionit:

http://localhost:8000/test-subscription



\- Toistuvat tilaukset ja meta-kentät toimivat automaattisesti.



---



\## Huomiot



\- PostgreSQL-data ja lokit tallentuvat paikallisesti "pgdata/" ja "storage/" kansioihin.  

\- ".env" sisältää salaisuudet, eikä sitä tule pushaa GitHubiin.









