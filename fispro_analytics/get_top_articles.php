<?php
include 'connection.php';

$month = $_GET['month'];

$kasaDB = $_SESSION['kasaDB'];

$sql = "USE $kasaDB";
$conn->query($sql);

$sql = "SELECT ma.Naziv_ART, COUNT(*) AS QuantitySold
        FROM skladiste_kartica sk
        INNER JOIN maloprodaja_artikli ma ON sk.SIFRA_ART = ma.SIFRA_ART
        WHERE MONTH(sk.Datum_KA) = $month AND sk.IdxUI = 1
        GROUP BY ma.Naziv_ART
        ORDER BY QuantitySold DESC
        LIMIT 10";
$result = $conn->query($sql);

$topArticlesData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topArticlesData[] = $row;
    }
}

echo json_encode($topArticlesData);
?>