<?php
// Read the JSON file
$jsonString = file_get_contents('src/database_credentials.json');
$credentials = json_decode($jsonString, true);

// Extract the credentials
$servername = $credentials['servername'];
$username = $credentials['username'];
$password = $credentials['password'];
$prijavaDB = $credentials['prijavaDB'];
$kasaDB = $credentials['kasaDB'];

// Create connection
$conn = new mysqli($servername, $username, $password, $prijavaDB);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$_SESSION['prijavaDB'] = $prijavaDB;
$_SESSION['kasaDB'] = $kasaDB;
?>