<?php
include 'connection.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['username']) || $_SESSION['uloga'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the referent ID from the request
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing referent ID']);
    exit;
}

$referentId = $_GET['id'];

// Start a transaction
$conn->begin_transaction();

try {
    // Update partners to set referent_id to 1
    $sql = "UPDATE firme_podatci SET referent_id = 1 WHERE referent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $referentId);
    $stmt->execute();
    $stmt->close();

    // Delete the referent
    $sql = "DELETE FROM meni_korisnik WHERE id = ? AND Uloga = 'referent'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $referentId);
    $stmt->execute();
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback the transaction if any error occurs
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error deleting referent: ' . $e->getMessage()]);
}

$conn->close();
?>