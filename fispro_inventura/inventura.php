<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$IDInventure = $_SESSION['IDInventure'] ?? '';
$Uloga = $_SESSION['Uloga'] ?? '';
$userId = $_SESSION['user_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="hr"  data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="inventura.css">
    <title>FISPRO Inventura</title>
    <link rel="apple-touch-icon" sizes="180x180" href="src/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/favicon-16x16.png">
    <link rel="manifest" href="src/site.webmanifest">
    <style>
        .footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.8rem;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Skeniranje artikala</h1>
        <a href="logout.php" class="btn btn-outline-danger mt-2"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        <div class="section">
            <div id="my-qr-reader"></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="scan-result-modal" tabindex="-1" aria-labelledby="scanResultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scanResultModalLabel">Rezultat skeniranja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="alert-container"></div>
                    <form id="scan-result-form">
                        <div class="mb-3">
                            <label for="sifraInput" class="form-label">Šifra</label>
                            <input type="text" class="form-control" id="sifraInput" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="katalogInput" class="form-label">Katalog</label>
                            <input type="text" class="form-control" id="katalogInput" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="nazivInput" class="form-label">Naziv</label>
                            <input type="text" class="form-control" id="nazivInput" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="mpcInput" class="form-label">MPC</label>
                            <input type="text" class="form-control" id="mpcInput" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="amountInput" class="form-label">Količina</label>
                            <input type="number" class="form-control" id="amountInput" required>
                        </div>
                        <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-floppy-disk"></i> Spremi</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-ban"></i> Odustani</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
    <script>
        const IDInventure = <?php echo json_encode($IDInventure); ?>;
        const Uloga = <?php echo json_encode($Uloga); ?>;
        const userId = <?php echo json_encode($userId); ?>;
    </script>
    <script src="html5-qrcode.js"></script>
    <script src="inventura.js"></script>
</body>
</html>