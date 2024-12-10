<?php
include 'connection.php';

header('Content-Type: application/json');

// Check if the user is logged in and has the admin or referent role
if (!isset($_SESSION['username']) || ($_SESSION['uloga'] != 'admin' && $_SESSION['uloga'] != 'referent')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the partner ID from the request
$partnerId = $_GET['id'];

// Fetch partner data
$sql = "SELECT * FROM firme_podatci WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $partnerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $partner = $result->fetch_assoc();
    echo json_encode(['success' => true, 'partner' => $partner]);
} else {
    echo json_encode(['success' => false, 'message' => 'Partner not found']);
}

$stmt->close();
$conn->close();
?>