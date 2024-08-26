<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Erreur d'accès</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e74c3c, #c0392b);
      color: #fff;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }

    .container {
      background-color: #fff;
      color: #333;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      width: 90%;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .btn {
      display: inline-block;
      background-color: #3498db;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 1rem;
      transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .btn:hover {
      background-color: #2980b9;
      transform: translateY(-5px);
    }

    .btn:active {
      background-color: #2980b9;
      transform: scale(0.98);
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Accès Interdit</h1>
    <p>Il est impossible de voir d'autres pages ou d'effectuer des modifications en tant qu'utilisateur.</p>
    <a href="login.php" class="btn">Retour à la connexion</a>
  </div>
</body>
</html>
