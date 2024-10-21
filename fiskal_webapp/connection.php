<?php
$servername = "kibox-tech.eu:3306";
$username = "kibox";
$password = "Kiboxtech2024";
$dbname = "kiboxtecheu_osnovna";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
?>