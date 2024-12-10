<?php
include 'connection.php';

$query = $_GET['query'];
$sql = "SELECT Naziv, OIB FROM firme_podatci WHERE Naziv LIKE '%$query%' OR OIB LIKE '%$query%'";
$result = $conn->query($sql);

$suggestions = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suggestions .= '<a href="#" class="list-group-item list-group-item-action search-suggestion">' . htmlspecialchars($row['Naziv']) . ' (' . htmlspecialchars($row['OIB']) . ')</a>';
    }
} else {
    $suggestions = '<a href="#" class="list-group-item list-group-item-action disabled">No results found</a>';
}

echo $suggestions;
?>