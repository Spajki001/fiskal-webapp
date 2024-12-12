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

$sqld = "DELETE FROM povijest_partner WHERE partner_id = ?";
$stmtd = $conn->prepare($sqld);
$stmtd->bind_param('i', $partnerId);

// Delete the partner
$sql = "DELETE FROM firme_podatci WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $partnerId);

if ($stmtd->execute() && $stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting partner']);
}

$stmt->close();
$stmtd->close();
$conn->close();
?>