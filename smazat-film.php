<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    die("Mazání je povoleno pouze přes formulář.");
}

require __DIR__ . "/assets/connection.php";

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    die("Neplatné ID filmu.");
}

$sql = "
    DELETE FROM movies
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

mysqli_stmt_close($statement);

header("Location: filmy.php");
exit;
