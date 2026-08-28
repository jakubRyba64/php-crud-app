# Movie Tracker CRUD

Jednoduchá PHP/MySQL aplikace pro přidávání, zobrazování, úpravu a mazání filmů.

## Spuštění

1. Nainstalujte PHP s rozšířením `mysqli` a MySQL.
2. V MySQL vytvořte databázi `movie_tracker` a tabulku `movies` se sloupci `id`, `title`, `director`, `release_year`, `genre`, `status` a `notes`.
3. Zkopírujte `.env.example` jako `.env` a doplňte přístupové údaje k databázi.
4. Ve složce projektu spusťte `php -S localhost:8000` a otevřete `http://localhost:8000`.
