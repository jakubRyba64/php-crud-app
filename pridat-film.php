<?php

require __DIR__ . "/assets/connection.php";
require __DIR__ . "/assets/helpers.php";

$errors = [];

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
            INSERT INTO movies (
                title,
                director,
                release_year,
                genre,
                status,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $statement = mysqli_prepare($connection, $sql);

        if (!$statement) {
            die(mysqli_error($connection));
        }

        mysqli_stmt_bind_param(
            $statement,
            "ssisss",
            $title,
            $director,
            $releaseYear,
            $genre,
            $status,
            $notes
        );

        if (!mysqli_stmt_execute($statement)) {
            die(mysqli_stmt_error($statement));
        } else {
            header("Location: filmy.php?success=added");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieTracker | Přidat film</title>
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
                <li><a class="navbar-link" href="filmy.php">Filmy</a></li>
                <li><a class="navbar-link navbar-link-active" href="pridat-film.php" aria-current="page">Přidat film</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="page-intro">
            <div class="page-intro-content">
                <div class="page-intro-copy">
                    <h1>Přidat film</h1>
                    <p>Vyplňte údaje o filmu a uložte jej do sbírky.</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="panel">
                <div class="panel-header">
                    <p class="panel-title">Informace o filmu</p>
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
                                    required
                                    value="<?= e($title ?? "") ?>"
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
                                    required
                                    value="<?= e($director ?? "") ?>"
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
                                    min="1888"
                                    max="2100"
                                    required
                                    value="<?= e($releaseYear ?? "") ?>"
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
                                    <option value="" <?= ($genre ?? "") === "" ? "selected" : "" ?> disabled>Vyberte žánr</option>
                                    <option value="drama" <?= ($genre ?? "") === "drama" ? "selected" : "" ?>>Drama</option>
                                    <option value="komedie" <?= ($genre ?? "") === "komedie" ? "selected" : "" ?>>Komedie</option>
                                    <option value="akcni" <?= ($genre ?? "") === "akcni" ? "selected" : "" ?>>Akční</option>
                                    <option value="horor" <?= ($genre ?? "") === "horor" ? "selected" : "" ?>>Horor</option>
                                    <option value="dokumentarni" <?= ($genre ?? "") === "dokumentarni" ? "selected" : "" ?>>Dokumentární</option>
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
                                    <option value="planned" <?= ($status ?? "planned") === "planned" ? "selected" : "" ?>>Plánováno</option>
                                    <option value="watched" <?= ($status ?? "") === "watched" ? "selected" : "" ?>>Zhlédnuto</option>
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
                            ><?= e($notes ?? "") ?></textarea>
                            <?php if (isset($errors["notes"])): ?>
                                <p class="form-error">
                                    <?= e($errors["notes"]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a class="button button-secondary" href="filmy.php">Zrušit</a>
                        <button class="button button-accent" type="submit">Uložit film</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
