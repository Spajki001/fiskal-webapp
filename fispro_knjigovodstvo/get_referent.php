<?php
include 'connection.php';

header('Content-Type: application/json');

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['username']) || $_SESSION['uloga'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the referent ID from the request
$referentId = $_GET['id'];

// Fetch referent data
$sql = "SELECT id, Ime, Prezime, OIB, Username, Password, Uloga FROM meni_korisnik WHERE id = ? AND Uloga = 'referent'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $referentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $referent = $result->fetch_assoc();
    echo json_encode(['success' => true, 'referent' => $referent]);
} else {
    echo json_encode(['success' => false, 'message' => 'Referent not found']);
}

$stmt->close();
$conn->close();
?>