<?php

require __DIR__ . "/assets/connection.php";
require __DIR__ . "/assets/helpers.php";

$sqlStats = "
    SELECT
        COUNT(*) AS total,
        SUM(
            CASE
                WHEN status = 'watched' THEN 1
                ELSE 0
            END
        ) AS watched,
        SUM(
            CASE
                WHEN status = 'planned' THEN 1
                ELSE 0
            END
        ) AS planned
    FROM movies
";

$resultStats = mysqli_query($connection, $sqlStats);

if (!$resultStats) {
    die(mysqli_error($connection));
}

$stats = mysqli_fetch_assoc($resultStats);

$sqlMovies = "
    SELECT
        id,
        title,
        director,
        release_year,
        genre,
        status
    FROM movies
    ORDER BY id DESC
    LIMIT 10
";

$resultMovies = mysqli_query($connection, $sqlMovies);

if (!$resultMovies) {
    die(mysqli_error($connection));
}

$movies = mysqli_fetch_all($resultMovies, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieTracker | Přehled</title>
    <link rel="stylesheet" href="assets/style.css?v=7">
    <link rel="icon" href="assets/pics/logo-icon.svg" type="image/svg+xml">
</head>
<body>
    <header class="site-header">
        <nav class="navbar">
            <a class="navbar-brand" href="index.php" aria-label="FilmTracker – přejít na hlavní stránku">
                <img class="navbar-logo" src="assets/pics/logo.svg?v=2" alt="FilmTracker">
            </a>
            <ul class="navbar-list">
                <li><a class="navbar-link navbar-link-active" href="index.php" aria-current="page">Přehled</a></li>
                <li><a class="navbar-link" href="filmy.php">Filmy</a></li>
                <li><a class="navbar-link" href="pridat-film.php">Přidat film</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="page-intro">
            <div class="page-intro-content">
                <div class="page-intro-copy">
                    <h1>Přehled</h1>
                    <p>Souhrn vaší filmové sbírky a nedávno přidané tituly.</p>
                </div>
                <div class="page-intro-action">
                    <a href="pridat-film.php" class="add-button">+ Přidat film</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <p class="stat-label">Celkem filmů</p>
                    <p class="stat-value">
                        <?= (int) $stats["total"] ?>
                    </p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Zhlédnuto</p>
                    <p class="stat-value">
                        <?= (int) $stats["watched"] ?>
                    </p>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Plánováno</p>
                    <p class="stat-value">
                        <?= (int) $stats["planned"] ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="panel">
                <div class="panel-header">
                    <p class="panel-title">Nedávno přidané filmy</p>
                    <a class="button button-secondary" href="filmy.php">Zobrazit vše</a>
                </div>

                <?php if ($movies === []): ?>
                    <p class="empty-message">
                        Zatím nebyl přidán žádný film.
                    </p>
                <?php else: ?>
                    <?php foreach ($movies as $movie): ?>
                        <div class="movie-row">
                            <div class="movie-info">
                                <p class="movie-title">
                                    <?= e($movie["title"]) ?>
                                </p>
                                <p class="movie-meta">
                                    <?= e($movie["director"]) ?>
                                    ·
                                    <?= (int) $movie["release_year"] ?>
                                </p>
                                <p class="movie-genre">
                                    <?= e(ucfirst($movie["genre"])) ?>
                                </p>
                            </div>
                            <div class="movie-summary">
                                <p class="movie-status <?= $movie["status"] === "watched"
                                    ? "movie-status-watched"
                                    : "movie-status-planned" ?>">
                                    <?= $movie["status"] === "watched"
                                        ? "Zhlédnuto"
                                        : "Plánováno" ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
