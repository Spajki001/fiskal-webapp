<?php
include 'connection.php';

header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');
error_reporting(E_ALL);

// Check if the user is logged in and has the admin or referent role
if (!isset($_SESSION['username']) || ($_SESSION['uloga'] != 'admin' && $_SESSION['uloga'] != 'referent')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the form data
$ime = $_POST['ime'];
$prezime = $_POST['prezime'];
$oib = $_POST['oib'];
$username = $_POST['username'];
$password = $_POST['password'];
$uloga = $_POST['uloga'];

// Validate the form data
if (empty($ime) || empty($prezime) || empty($oib) || empty($username) || empty($password) || empty($uloga)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check if the username already exists
$sql = "SELECT Username FROM meni_korisnik WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    exit;
}

// Insert the new user into the database
$sql = "INSERT INTO meni_korisnik (Ime, Prezime, OIB, Username, Password, Uloga) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssss', $ime, $prezime, $oib, $username, $password, $uloga);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding user']);
}

$stmt->close();
$conn->close();
?>