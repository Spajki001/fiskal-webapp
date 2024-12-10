<?php
include 'connection.php';

header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');
error_reporting(E_ALL);

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['username']) || $_SESSION['uloga'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the form data
$id = $_POST['id'];
$ime = $_POST['ime'];
$prezime = $_POST['prezime'];
$oib = $_POST['oib'];
$username = $_POST['username'];
$password = $_POST['password'];
$uloga = $_POST['uloga'];

// Validate the form data
if (empty($id) || empty($ime) || empty($prezime) || empty($oib) || empty($username) || empty($uloga)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$sql = "UPDATE meni_korisnik SET Ime = ?, Prezime = ?, OIB = ?, Username = ?, Password = ?, Uloga = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssi', $ime, $prezime, $oib, $username, $password, $uloga, $id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating referent']);
}

$stmt->close();
$conn->close();
?>