<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$sql = "USE kiboxtecheu_2024";
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
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Izbor inventure</title>
</head>
<body>
    <form action="odabir_inventure.php" method="POST">
        <select class="form-select IDInventure" id="IDInventure" name="IDInventure" aria-label="Odabir otvorene inventure">
            <option selected>Izaberite inventuru</option>
            <?php
            $sql = "USE kasa001_2024";
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
        <select class="form-select Uloga" id="Uloga" name="Uloga" aria-label="Odabir uloge">
            <option selected>Izaberite svoju ulogu</option>
            <option value="Upis1">Upis 1</option>
            <option value="Upis2">Upis 2</option>
        </select>
        <button type="submit" name="submit" class="btn btn-primary">Nastavi</button>
    </form>
</body>
</html>