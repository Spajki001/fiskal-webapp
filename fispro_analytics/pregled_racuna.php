<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$kasaDB = $_SESSION['kasaDB'];

$ID_KD = $_GET['ID_KD'] ?? null;

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
    <link rel="stylesheet" href="pregled_racuna.css">
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Pregled računa</h1>
            <a href="zakljucci.php" class="btn btn-primary mt-3 me-2"><i class="fa-solid fa-cash-register"></i> Pregled zaključaka</a>
            <a href="pregled_stolova.php" class="btn btn-secondary mt-3 me-2"><i class="fa-solid fa-mug-hot"></i> Pregled stolova</a>
            <a href="logout.php" class="btn btn-outline-danger mt-3"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        </div>
        <div class="row table-container mt-3">
            <?php
                if ($ID_KD) {
                    $sql = "USE $kasaDB";
                    $result = $conn->query($sql);
                    $sql = "SELECT * FROM maloprodaja_robe WHERE ID_KD = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $ID_KD);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $totalIznosMPR = 0;
                    $tableRows = "";

                    if ($result->num_rows > 0){
                        while ($row = $result->fetch_assoc()) {
                            $totalIznosMPR += $row['Iznos_MPR'];
                            $tableRows .= "<tr>
                                            <td>" . $row['ID_MPR'] . "</td>
                                            <td>" . $row['Posl_MPR'] . "</td>
                                            <td>" . $row['Datum_MPR'] . "</td>
                                            <td>" . $row['ID_SKLAD'] . "</td>
                                            <td>" . $row['KupacNaziv'] . "</td>
                                            <td>" . $row['KupacOIB'] . "</td>
                                            <td>" . $row['Iznos_MPR'] . " €</td>
                                            <td>" . $row['Porez_MPR'] . " €</td>
                                            <td>" . $row['KorID'] . "</td>
                                            <td>" . $row['KorOIB'] . "</td>
                                        </tr>";
                        }
                    }
            ?>
        </div>
        <?php if ($ID_KD && $result->num_rows > 0): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="jumbotron text-center">
                        <h4 class="display-8"><strong>Ukupno_MPR: </strong><?php echo number_format($totalIznosMPR, 2); ?></h4>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID_MPR</th>
                            <th>Posl_MPR</th>
                            <th>Datum_MPR</th>
                            <th>ID_SKLAD</th>
                            <th>KupacNaziv</th>
                            <th>KupacOIB</th>
                            <th>Iznos_MPR</th>
                            <th>Porez_MPR</th>
                            <th>KorID</th>
                            <th>KorOIB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $tableRows; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class='jumbotron text-center'>
                <h3 class='display-6 text-align-center'>Nema podataka</h3>
            </div>
        <?php endif; ?>
        <?php } ?>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
</body>
</html>