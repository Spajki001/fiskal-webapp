<?php
include 'connection.php';
header('Content-Type: application/json');

$filters = json_decode(file_get_contents('php://input'), true);
$query = "SELECT fp.*, mk.Prezime as Referent FROM firme_podatci fp LEFT JOIN meni_korisnik mk ON fp.referent_id = mk.id WHERE 1=1";
$params = [];

if (!empty($filters['pdv'])) {
    $query .= " AND fp.PDV = ?";
    $params[] = $filters['pdv'];
}
if (!empty($filters['referent'])) {
    $query .= " AND fp.referent_id = ?";
    $params[] = $filters['referent'];
}
if (!empty($filters['vrstaKnjigovodstva'])) {
    $query .= " AND fp.Vrsta_knjigovodstva = ?";
    $params[] = $filters['vrstaKnjigovodstva'];
}
if (!empty($filters['placa'])) {
    $query .= " AND fp.Placa = ?";
    $params[] = $filters['placa'];
}
if (!empty($filters['drugiDohodak'])) {
    $query .= " AND fp.Drugi_dohodak = ?";
    $params[] = $filters['drugiDohodak'];
}
if (!empty($filters['dodatneUsluge'])) {
    $query .= " AND fp.Dodatne_usluge = ?";
    $params[] = $filters['dodatneUsluge'];
}
if (!empty($filters['fakturira'])) {
    $query .= " AND fp.Fakturira = ?";
    $params[] = $filters['fakturira'];
}

$stmt = $conn->prepare($query);
if (count($params) > 0) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$filteredData = [];
while ($row = $result->fetch_assoc()) {
    $filteredData[] = $row;
}

echo json_encode($filteredData);
?>