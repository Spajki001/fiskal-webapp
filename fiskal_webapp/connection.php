<?php
$servername = "multipos.ddns.net:3307";
$username = "test";
$password = "test";
$dbname = "kasa001_osnovna";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
?>