<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$sifra = $data['sifra'] ?? '';
$amount = $data['amount'] ?? '';
$userId = $data['userId'] ?? '';
$currentDate = $data['currentDate'] ?? '';
$idInventure = $_SESSION['IDInventure'] ?? '';
$uloga = $_SESSION['Uloga'] ?? '';
$kasaDB = $_SESSION['kasaDB'];

// Log received data for debugging
error_log("Received data: sifra=$sifra, amount=$amount, userId=$userId, currentDate=$currentDate, idInventure=$idInventure, uloga=$uloga");

if (empty($sifra) || empty($amount) || empty($userId) || empty($currentDate) || empty($idInventure) || empty($uloga)) {
    http_response_code(400);
    echo 'Invalid input';
    exit;
}

// Sanitize input data
$sifra = htmlspecialchars($sifra, ENT_QUOTES, 'UTF-8');
$amount = htmlspecialchars($amount, ENT_QUOTES, 'UTF-8');
$userId = htmlspecialchars($userId, ENT_QUOTES, 'UTF-8');
$currentDate = htmlspecialchars($currentDate, ENT_QUOTES, 'UTF-8');
$idInventure = htmlspecialchars($idInventure, ENT_QUOTES, 'UTF-8');
$sql = "USE $kasaDB";
$conn->query($sql);

try {
    if ($uloga == 'Upis1') {
        $sql = "INSERT INTO inventura_upis (ID_INV, Sifra_ART, Upis1_INV, KorUpis1, KorDatum1) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issis', $idInventure, $sifra, $amount, $userId, $currentDate);
    } else if ($uloga == 'Upis2') {
        $sql = "UPDATE inventura_upis SET Upis2_INV = ?, KorUpis2 = ?, KorDatum2 = ? WHERE ID_INV = ? AND Sifra_ART = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('disis', $amount, $userId, $currentDate, $idInventure, $sifra);
    } else {
        throw new Exception('Invalid role');
    }

    if ($stmt->execute()) {
        echo 'Data submitted successfully';
    } else {
        throw new Exception('Error executing statement: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Error submitting data: ' . $e->getMessage();
}
?>