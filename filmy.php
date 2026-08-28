<?php

require __DIR__ . "/assets/connection.php";
require __DIR__ . "/assets/helpers.php";

$sql = "
    SELECT
        id,
        title,
        director,
        release_year,
        genre,
        status
    FROM movies
    ORDER BY id DESC
";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die(mysqli_error($connection));
}

$movies = mysqli_fetch_all($result, MYSQLI_ASSOC);

$successMessages = [
    "added" => "Film byl úspěšně přidán.",
    "updated" => "Film byl úspěšně upraven.",
    "deleted" => "Film byl úspěšně smazán.",
];

$successKey = $_GET["success"] ?? "";
$successMessage = $successMessages[$successKey] ?? null;

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieTracker | Filmy</title>
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
                <li><a class="navbar-link" href="index.php">Přehled</a></li>
                <li><a class="navbar-link navbar-link-active" href="filmy.php" aria-current="page">Filmy</a></li>
                <li><a class="navbar-link" href="pridat-film.php">Přidat film</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($successMessage !== null): ?>
            <div class="flash-message" role="status">
                <?= e($successMessage) ?>
            </div>
        <?php endif; ?>

        <section class="page-intro">
            <div class="page-intro-content">
                <div class="page-intro-copy">
                    <h1>Filmy</h1>
                    <p>Celkem <?= count($movies) ?> filmů ve vaší sbírce.</p>
                </div>
                <div class="page-intro-action">
                    <a href="pridat-film.php" class="add-button">+ Přidat film</a>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="panel">
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
                                <div class="movie-actions">
                                    <a class="button button-secondary" href="upravit-film.php?id=<?= (int) $movie["id"] ?>">Upravit</a>
                                    <form
                                        class="delete-form"
                                        method="POST"
                                        action="smazat-film.php"
                                        onsubmit="return confirm('Opravdu chcete tento film smazat?');"
                                    >
                                        <input type="hidden" name="id" value="<?= (int) $movie["id"] ?>">
                                        <button class="button button-danger" type="submit">Smazat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
