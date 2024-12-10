<?php
include 'connection.php';

header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');
error_reporting(E_ALL);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['username']) || $_SESSION['uloga'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Fetch referents
$sql = "SELECT id, Ime, Prezime, OIB FROM meni_korisnik WHERE Uloga = 'referent'";
$result = $conn->query($sql);

$referents = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $referents[] = $row;
    }
}

echo json_encode(['success' => true, 'referents' => $referents]);

$conn->close();
?>