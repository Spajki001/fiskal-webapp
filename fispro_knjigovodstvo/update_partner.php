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
$id = $_POST['id'];
$naziv = $_POST['naziv'];
$adresa = $_POST['adresa'];
$oib = $_POST['oib'];
$pdv = $_POST['pdv'];
$iznos_naknade = $_POST['iznos_naknade'];
$firma_id = $_POST['firma_id'];
$referent_id = $_POST['referent_id'];
$vrsta_knjigovodstva = $_POST['vrsta_knjigovodstva'];
$placa = $_POST['placa'];
$drugi_dohodak = $_POST['drugi_dohodak'];
$dodatne_usluge = $_POST['dodatne_usluge'];
$fakturira = $_POST['fakturira'];

// Validate the form data
if (empty($id) || empty($naziv) || empty($adresa) || empty($oib) || empty($pdv) || empty($iznos_naknade) || empty($firma_id) || empty($referent_id) || empty($vrsta_knjigovodstva) || empty($placa) || empty($drugi_dohodak) || empty($dodatne_usluge) || empty($fakturira)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Update the partner
$sql = "UPDATE firme_podatci SET Naziv = ?, Adresa = ?, OIB = ?, PDV = ?, Iznos_naknade = ?, firma_id = ?, referent_id = ?, Vrsta_knjigovodstva = ?, Placa = ?, Drugi_dohodak = ?, Dodatne_usluge = ?, Fakturira = ? WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssiissssi', $naziv, $adresa, $oib, $pdv, $iznos_naknade, $firma_id, $referent_id, $vrsta_knjigovodstva, $placa, $drugi_dohodak, $dodatne_usluge, $fakturira, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating partner']);
}

$stmt->close();
$conn->close();
?>