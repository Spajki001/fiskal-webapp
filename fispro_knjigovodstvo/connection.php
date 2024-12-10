<?php
// Read the JSON file
$jsonString = file_get_contents('src/database_credentials.json');
$credentials = json_decode($jsonString, true);

// Extract the credentials
$servername = $credentials['servername'];
$username = $credentials['username'];
$password = $credentials['password'];
$dbPrefix = $credentials['dbPrefix'];

$prijavaDB = $dbPrefix . "knjigovodstvo";

// Create connection
$conn = new mysqli($servername, $username, $password, $prijavaDB);

mysqli_set_charset($conn, 'utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

$sql = "USE $prijavaDB";
$result = $conn->query($sql);
?>