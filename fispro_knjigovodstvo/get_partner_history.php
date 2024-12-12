<?php
include 'connection.php';

if (isset($_GET['partner_id'])) {
    $partner_id = $_GET['partner_id'];

    $sql = "SELECT * FROM povijest_partner WHERE partner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $partner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    echo json_encode(['success' => true, 'history' => $history]);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid partner ID']);
}
?>