<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$kasaDB = $_SESSION['kasaDB'];

?>
<!DOCTYPE html>
<html lang="hr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="src/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/favicon-16x16.png">
    <link rel="manifest" href="src/site.webmanifest">
    <title>FISPRO Analytics</title>
    <style>
        .footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.8rem;
            text-align: right;
        }
        h1 {
            font-weight: 500 !important;
        }
        td, th {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Pregled prometa</h1>
            <a href="zakljucci.php" class="btn btn-primary mt-3 me-2"><i class="fa-solid fa-cash-register"></i> Pregled zaključaka</a>
            <a href="stolovi.php" class="btn btn-secondary mt-3 me-2"><i class="fa-solid fa-receipt"></i> Pregled stolova</a>
            <a href="logout.php" class="btn btn-outline-danger mt-3"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        </div>
        <div class="row table-container">
            <?php
                $sql = "USE $kasaDB";
                $result = $conn->query($sql);
                $sql = "SELECT * FROM zakljucak_blagajne";
                $result = $conn->query($sql);

                echo "<div class='table-responsive mt-3'>";
                echo "<table class='table table-striped table-hover'>";
                echo "<thead><tr>
                        <th>Broj_KD</th>
                        <th>Datum_KD</th>
                        <th>ID_SKLAD</th>
                        <th>RacOd_KD</th>
                        <th>RacDo_KD</th>
                        <th>Ukupno_KD</th>
                        <th>Porez_KD</th>
                        <th>Porez1_KD</th>
                        <th>SystemDate</th>
                    </tr></thead>";
                echo "<tbody>";
                if ($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['Broj_KD'] . "</td>
                                <td>" . $row['Datum_KD'] . "</td>
                                <td>" . $row['ID_SKLAD'] . "</td>
                                <td>" . $row['RacOd_KD'] . "</td>
                                <td>" . $row['RacDo_KD'] . "</td>
                                <td>" . $row['Ukupno_KD'] . "</td>
                                <td>" . $row['Porez_KD'] . "</td>
                                <td>" . $row['Porez1_KD'] . "</td>
                                <td>" . $row['SystemDate'] . "</td>
                            </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<h3 class='display-6 text-align-center'>Nema podataka</h1>";
                    echo "</tbody></table></div>";
                }
            ?>
        </div>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
</body>
</html>