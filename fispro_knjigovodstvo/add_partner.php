<?php
include 'connection.php';

header('Content-Type: application/json');

// Check if the user is logged in and has the admin or referent role
if (!isset($_SESSION['username']) || ($_SESSION['uloga'] != 'admin' && $_SESSION['uloga'] != 'referent')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the form data
$naziv = $_POST['naziv'];
$adresa = $_POST['adresa'];
$oib = $_POST['oib'];
$pdv = $_POST['pdv'];
$iznos_naknade = $_POST['iznos_naknade'];
$firma_id = $_POST['firma_id'];
$referent_id = $_POST['referent_id'];
$Vrsta_knjigovodstva = $_POST['vrsta_knjigovodstva'];
$Placa = $_POST['placa'];
$Drugi_dohodak = $_POST['drugi_dohodak'];
$Dodatne_usluge = $_POST['dodatne_usluge'];
$Fakturira = $_POST['fakturira'];

// Validate the form data
if (empty($naziv) || empty($adresa) || empty($oib) || empty($pdv) || empty($iznos_naknade) || empty($firma_id) || empty($referent_id) || empty($Vrsta_knjigovodstva) || empty($Placa) || empty($Drugi_dohodak) || empty($Dodatne_usluge) || empty($Fakturira)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Insert the new firm into the database
$sql = "INSERT INTO firme_podatci (Naziv, Adresa, OIB, PDV, Iznos_naknade, firma_id, referent_id, Vrsta_knjigovodstva, Placa, Drugi_dohodak, Dodatne_usluge, Fakturira) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssssssss', $naziv, $adresa, $oib, $pdv, $iznos_naknade, $firma_id, $referent_id, $Vrsta_knjigovodstva, $Placa, $Drugi_dohodak, $Dodatne_usluge, $Fakturira);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding firm']);
}

$stmt->close();
$conn->close();
?>