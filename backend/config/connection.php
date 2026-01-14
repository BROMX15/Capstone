<?php
require "constants.php";

$connection = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    3306
);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8mb4");
?>
