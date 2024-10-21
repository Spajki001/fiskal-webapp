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
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        body {
            text-align: center;
            display: flex;
            justify-content: center;
        }
    </style>
    <title>Login</title>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center" style="height: auto">
        <div style="width: 30vw;">
            <div class="jumbotron">
                <h1 class="display-4">Login</h1>
            </div>
            <form action="index.php" method="post">
                <div class="form-floating mb-3 mt-3">
                    <input type="text" class="form-control" name="username" id="username" placeholder="username" required>
                    <label for="username" class="form-label">Username</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="password" class="form-control" name="password" id="password" placeholder="password" required>
                    <label for="password" class="form-label">Password</label>
                </div>
                <div class="d-inline me-2">
                    <button type="submit" class="btn btn-primary mt-2" name="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
    </script>
    <script src="inventura.js"></script>
</body>
</html>