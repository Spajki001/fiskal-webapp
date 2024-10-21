<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$sifra = $_POST['sifra'] ?? '';
$amount = $_POST['amount'] ?? '';
$userId = $_POST['userId'] ?? '';
$currentDate = $_POST['currentDate'] ?? '';
$idInventure = $_POST['idInventure'] ?? '';

if (empty($sifra) || empty($amount) || empty($userId) || empty($currentDate) || empty($idInventure)) {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

$sql = "INSERT INTO inventura_upis (ID_INV, Sifra_ART, Upis1_INV, KorUpis1, KorDatum1) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssss', $idInventure, $sifra, $amount, $userId, $currentDate);

if ($stmt->execute()) {
    echo 'Data submitted successfully';
} else {
    http_response_code(500);
    echo 'Error submitting data: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>