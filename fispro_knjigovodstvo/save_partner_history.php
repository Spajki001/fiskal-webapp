<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted data
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
    $datum_promjene = date('Y-m-d H:i:s');

    // Retrieve the partner_id from the firme_podatci table
    $sql = "SELECT id FROM firme_podatci WHERE OIB = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $oib);
    $stmt->execute();
    $stmt->bind_result($partner_id);
    $stmt->fetch();
    $stmt->close();

    if ($partner_id) {
        // Insert the data into the povijest_partner table
        $sql = "INSERT INTO povijest_partner (partner_id, Naziv, Adresa, OIB, PDV, Iznos_naknade, firma_id, referent_id, Vrsta_knjigovodstva, Placa, Drugi_dohodak, Dodatne_usluge, Fakturira, Datum_promjene) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssdiissssss", $partner_id, $naziv, $adresa, $oib, $pdv, $iznos_naknade, $firma_id, $referent_id, $vrsta_knjigovodstva, $placa, $drugi_dohodak, $dodatne_usluge, $fakturira, $datum_promjene);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving partner history: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Partner not found']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>