<?php
// Read the JSON file
$jsonString = file_get_contents('src/database_credentials.json');
$credentials = json_decode($jsonString, true);

// Extract the credentials
$servername = $credentials['servername'];
$username = $credentials['username'];
$password = $credentials['password'];
$dbPrefix_0 = $credentials['dbPrefix_0'];
$dbPrefix_1 = $credentials['dbPrefix_1'];
$id_programa = $credentials['id_programa'];

if ($id_programa === "0") {
    $prijavaDB = $dbPrefix_0 . "osnovna";
    $kasaDBPrefix = $dbPrefix_0 . "mat";
} else if ($id_programa === "1") {
    $prijavaDB = $dbPrefix_1 . "osnovna";
    $kasaDBPrefix = $dbPrefix_1;
} else {
    die("Invalid id_programa");
}

// Create connection
$conn = new mysqli($servername, $username, $password, $prijavaDB);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$_SESSION['prijavaDB'] = $prijavaDB;
$_SESSION['kasaDBPrefix'] = $kasaDBPrefix;
?>