<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirigez l'utilisateur en fonction de son rôle
            if ($role === 'admin') {
                header("Location: admin.php");
            } elseif ($role === 'subadmin') {
                header("Location: dashboard.php");
            } else {
                header("Location: stock_user.php");
            }
            exit();
        } else {
            $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login page</title>
  <link rel="stylesheet" href="style1.css">
  <style>
    /* Animation et style du message d'erreur */
    .msg.error {
        color: red;
        font-weight: bold;
        text-align: center;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
  </style>
</head>
<body>
<section class='login' id='login'>
  <div class='head'>
    <img src="logo.png" alt="logo_snrt">
  </div>
  <p class='msg <?php echo !empty($error_message) ? "error" : ""; ?>'>
    <?php echo $error_message; ?>
  </p>
  <div class='form'>
    <form method="POST" action="login.php">
      <input type="text" name="username" placeholder="Username or email" class="text" required><br>
      <input type="password" name="password" placeholder="Password" class="password" required><br>
      <button type="submit" class="btn-login">Login</button>
    
    </form>  
  </div>
</section>
<footer>
  <p>&copy; 2024 Presented by Mohamed Lechhab. All rights reserved.</p>
</footer>
<script src="./script.js"></script>
</body>
</html>
