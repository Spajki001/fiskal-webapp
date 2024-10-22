<?php
include 'connection.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM meni_djelatnik WHERE UserName = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['UserPass']) {
            $_SESSION['username'] = $row['UserName'];
            $_SESSION['userpass'] = $row['UserPass'];
            $_SESSION['name'] = $row['Ime_DJE'];
            $_SESSION['surname'] = $row['Prezime_DJE'];
            $_SESSION['user_id'] = $row['ID_DJE'];

            header("Location: odabir_inventure.php");
            exit;
        } else {
            echo "<header>
                    <div class='alert alert-danger mt-3' role='alert'>
                        Invalid password!
                    </div>
                <header/>";
        }
    } else {
        echo "<header>
                <div class='alert alert-danger mt-3' role='alert'>
                    Username does not exist!<br>Try registering.
                </div>
            <header/>";
    }
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
    <link rel="stylesheet" href="index.css">
    <style>
        .logo {
            max-width: 85%;
            height: auto;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.8rem;
            text-align: right;
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
        <div class="jumbotron text-center">
            <img src="src/Logo.png" alt="Logo image" class="logo img-fluid">
            <h1 class="display-4">Prijava</h1>
        </div>
        <form action="index.php" method="post">
            <div class="form-floating mb-3 mt-3">
                <input type="text" class="form-control" name="username" id="username" placeholder="username" required>
                <label for="username" class="form-label">Korisničko ime</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" class="form-control" name="password" id="password" placeholder="password" required>
                <label for="password" class="form-label">Zaporka</label>
            </div>
            <div class="d-inline me-2">
                <button type="submit" class="btn btn-primary mt-2" name="submit"><i class="fa-solid fa-right-to-bracket"></i> Prijava</button>
            </div>
        </form>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
    <script>
        const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
    </script>
    <script src="inventura.js"></script>
</body>
</html>