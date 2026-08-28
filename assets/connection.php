<?php

$envFile = dirname(__DIR__) . "/.env";

if (!is_file($envFile)) {
    die("Chybí soubor .env. Vytvořte ho podle .env.example.");
}

$config = parse_ini_file($envFile, false, INI_SCANNER_RAW);

if ($config === false) {
    die("Soubor .env se nepodařilo načíst.");
}

foreach (["DB_HOST", "DB_USER", "DB_PASSWORD", "DB_NAME"] as $key) {
    if (!array_key_exists($key, $config)) {
        die("V souboru .env chybí položka {$key}.");
    }
}

$connection = mysqli_connect(
    $config["DB_HOST"],
    $config["DB_USER"],
    $config["DB_PASSWORD"],
    $config["DB_NAME"]
);

if (!$connection) {
    die("Připojení k databázi se nezdařilo.");
}

mysqli_set_charset($connection, "utf8mb4");
