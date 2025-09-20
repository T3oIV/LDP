<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "webapp";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Connessione fallita: " . $conn->connect_error);

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT username, password, status FROM utenti WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['status'] = $row['status'];

                // Redirect in base allo status
                if (strtolower($row['status']) === 'manager') {
                    header("Location: sito_consulenza_completo.php");
                } else {
                    header("Location: index.html");
                }
                exit();
            } else {
                $error = "Password errata!";
            }
        } else {
            $error = "Utente non trovato!";
        }
        $stmt->close();
    } else {
        $error = "Compila tutti i campi.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; color: black; }
.card { background: #8CC850; }
.btn-primary { background-color: #8CBC8C; border: none; color: black; }
.btn-primary:hover { background-color: #8CC850; color: black; }
.btn-link { color: black; text-decoration: underline; }
</style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4">Login</h2>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Accedi</button>
        </form>
        <p class="mt-3 text-center">
            Non hai un account? 
            <a href="registrazione.php" class="btn btn-link p-0">Registrati</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
