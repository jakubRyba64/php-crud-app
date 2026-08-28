<?php

require __DIR__ . "/assets/connection.php";
require __DIR__ . "/assets/helpers.php";

$errors = [];

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);


if ($id === false || $id === null) {
    die("Neplatné ID filmu.");
}

$sql = "
    SELECT
        id,
        title,
        director,
        release_year,
        genre,
        status,
        notes
    FROM movies
    WHERE id = ?
";

$statement = mysqli_prepare($connection, $sql);

if (!$statement) {
    die(mysqli_error($connection));
}

mysqli_stmt_bind_param($statement, "i", $id);

if (!mysqli_stmt_execute($statement)) {
    die(mysqli_stmt_error($statement));
}

$result = mysqli_stmt_get_result($statement);

$movie = mysqli_fetch_assoc($result);

mysqli_stmt_close($statement);

if (!$movie) {
    die("Film nebyl nalezen.");
}

$title = $movie["title"];
$director = $movie["director"];
$releaseYear = $movie["release_year"];
$genre = $movie["genre"];
$status = $movie["status"];
$notes = $movie["notes"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $director = trim($_POST["director"] ?? "");
    $releaseYear = filter_input(INPUT_POST, "release_year", FILTER_VALIDATE_INT);
    $genre = trim($_POST["genre"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    if ($title === "") {
        $errors["title"] = "Vyplňte název filmu.";
    }

    if ($director === "") {
        $errors["director"] = "Vyplňte režiséra.";
    }

    if (
        $releaseYear === false ||
        $releaseYear < 1888 ||
        $releaseYear > 2100
    ) {
        $errors["release_year"] = "Rok musí být mezi 1888 a 2100.";
    }

    if (!in_array(
        $genre,
        ["drama", "komedie", "akcni", "horor", "dokumentarni"],
        true
    )) {
        $errors["genre"] = "Vyberte platný žánr.";
    }

    if (!in_array($status, ["planned", "watched"], true)) {
        $errors["status"] = "Vyberte platný stav.";
    }

    if ($notes === "") {
        $errors["notes"] = "Vyplňte poznámky.";
    }
    if ($errors === []) {
         $sql = "
        UPDATE movies
        SET
            title = ?,
            director = ?,
            release_year = ?,
            genre = ?,
            status = ?,
            notes = ?
        WHERE id = ?
    ";

    $statement = mysqli_prepare($connection, $sql);

    if (!$statement) {
        die(mysqli_error($connection));
    }

    mysqli_stmt_bind_param(
        $statement,
        "ssisssi",
        $title,
        $director,
        $releaseYear,
        $genre,
        $status,
        $notes,
        $id
    );

    if (!mysqli_stmt_execute($statement)) {
        die(mysqli_stmt_error($statement));
    }

    mysqli_stmt_close($statement);

    header("Location: filmy.php");
    exit;
    }
}


?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieTracker | Upravit film</title>
    <link rel="stylesheet" href="assets/style.css?v=5">
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
    <section class="page-intro">
        <div class="page-intro-content">
            <div class="page-intro-copy">
                <h1>Upravit film</h1>
                <p>Upravte údaje o filmu a uložte provedené změny.</p>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="panel">
            <div class="panel-header">
                <p class="panel-title">Upravit informace o filmu</p>
            </div>
            <form class="movie-form" action="" method="POST">
                <div class="form-grid">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="title">Název filmu *</label>
                            <input
                                class="form-input"
                                type="text"
                                id="title"
                                name="title"
                                placeholder="např. Ostře sledované vlaky"
                                value="<?= e($title) ?>"
                                required
                            >
                            <?php if (isset($errors["title"])): ?>
                                <p class="form-error">
                                    <?= e($errors["title"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="director">Režisér *</label>
                            <input
                                class="form-input"
                                type="text"
                                id="director"
                                name="director"
                                placeholder="např. Jiří Menzel"
                                value="<?= e($director) ?>"
                                required
                            >
                            <?php if (isset($errors["director"])): ?>
                                <p class="form-error">
                                    <?= e($errors["director"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="release-year">Rok vydání *</label>
                            <input
                                class="form-input"
                                type="number"
                                id="release-year"
                                name="release_year"
                                placeholder="1966"
                                value="<?= e($releaseYear) ?>"
                                min="1888"
                                max="2100"
                                required
                            >
                            <?php if (isset($errors["release_year"])): ?>
                                <p class="form-error">
                                    <?= e($errors["release_year"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="genre">Žánr *</label>
                            <select class="form-input form-select" id="genre" name="genre" required>
                                <option value="" disabled>Vyberte žánr</option>
                                <option value="drama" <?= $genre === "drama" ? "selected" : "" ?>>Drama</option>
                                <option value="komedie" <?= $genre === "komedie" ? "selected" : "" ?>>Komedie</option>
                                <option value="akcni" <?= $genre === "akcni" ? "selected" : "" ?>>Akční</option>
                                <option value="horor" <?= $genre === "horor" ? "selected" : "" ?>>Horor</option>
                                <option value="dokumentarni" <?= $genre === "dokumentarni" ? "selected" : "" ?>>Dokumentární</option>
                            </select>
                            <?php if (isset($errors["genre"])): ?>
                                <p class="form-error">
                                    <?= e($errors["genre"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="watch-status">Stav zhlédnutí *</label>
                            <select class="form-input form-select" id="watch-status" name="status" required>
                                <option value="planned" <?= $status === "planned" ? "selected" : "" ?>>Plánováno</option>
                                <option value="watched" <?= $status === "watched" ? "selected" : "" ?>>Zhlédnuto</option>
                            </select>
                            <?php if (isset($errors["status"])): ?>
                                <p class="form-error">
                                    <?= e($errors["status"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group form-group-full">
                        <label class="form-label" for="notes">Poznámky *</label>
                        <textarea
                            class="form-textarea form-input"
                            id="notes"
                            name="notes"
                            rows="5"
                            placeholder="Vlastní poznámky k filmu..."
                            required
                        ><?= e($notes) ?></textarea>
                        <?php if (isset($errors["notes"])): ?>
                            <p class="form-error">
                                <?= e($errors["notes"]) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="button button-secondary" href="filmy.php">Zrušit</a>
                    <button class="button button-accent" type="submit">Uložit změny</button>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
