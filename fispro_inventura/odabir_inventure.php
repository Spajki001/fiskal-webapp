<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$kasaDB = $_SESSION['kasaDB'];

$sql = "USE $kasaDB";
$result = $conn->query($sql);

$sql = "SELECT * FROM inventura_robe where IdxStatus_INV = '1'";
$result = $conn->query($sql);

if (isset($_POST['submit'])) {
    $IDInventure = $_POST['IDInventure'];
    $Uloga = $_POST['Uloga'];

    $_SESSION['IDInventure'] = $IDInventure;
    $_SESSION['Uloga'] = $Uloga;

    header("Location: inventura.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 500px;
            width: 100%;
        }
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
    </style>
    <link rel="apple-touch-icon" sizes="180x180" href="src/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/favicon-16x16.png">
    <link rel="manifest" href="src/site.webmanifest">
    <title>FISPRO Inventura</title>
</head>
<body>
    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4">Izbor inventure</h1>
            <a href="logout.php" class="btn btn-outline-danger mb-2"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        </div>
        <form action="odabir_inventure.php" method="POST">
            <div class="form-floating mb-3 mt-3">
                <select class="form-select" id="IDInventure" name="IDInventure" aria-label="Odabir otvorene inventure" required>
                    <option selected disabled>Izaberite inventuru</option>
                    <?php
                    $sql = "USE $kasaDB";
                    $result = $conn->query($sql);

                    $sql = "SELECT * FROM inventura_robe where IdxStatus_INV = '1'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['ID_INV'] . "'>" . $row['Broj_INV'] . "</option>";
                        }
                    }
                    ?>
                </select>
                <label for="IDInventure" class="form-label">Odabir otvorene inventure</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="Uloga" name="Uloga" aria-label="Odabir uloge" required>
                    <option selected disabled>Izaberite svoju ulogu</option>
                    <option value="Upis1">Upisnik 1</option>
                    <option value="Upis2">Upisnik 2</option>
                </select>
                <label for="Uloga" class="form-label">Odabir uloge</label>
            </div>
            <div class="d-inline me-2">
                <button type="submit" name="submit" class="btn btn-primary mt-2"><i class="fa-solid fa-check"></i> Nastavi</button>
            </div>
        </form>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
</body>
</html>