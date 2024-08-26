<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
      header("Location: login.php");
      exit();
    }
  ?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Session administrateur</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="style3.css">
</head>
<style>
    /* Global Styles */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(to right, #4568DC, #B06AB3);
  color: #333;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.container {
  background-color: #fff;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 50px;
  width: 80%;
  max-width: 900px;
  animation: fadeIn 0.5s ease-in-out;
}

/* Header Styles */
.title {
  font-size: 3.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 0;
  animation: slideIn 0.7s ease-in-out;
}

.welcome {
  font-size: 1.4rem;
  color: #34495e;
  margin-bottom: 50px;
  animation: slideIn 0.9s ease-in-out;
}

/* Navigation Styles */
.nav-menu {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
  margin-bottom: 50px;
}

.nav-item {
  background-color: #3498db;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  color: white;
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  animation: slideIn 1.1s ease-in-out;
}

.nav-item:hover {
  transform: translateY(-10px);
  box-shadow: 0px 20px 30px rgba(0, 0, 0, 0.3);
}

.icon-container {
  width: 80px;
  height: 80px;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.nav-item i {
  font-size: 2.5rem;
}

.nav-item span {
  font-size: 1.3rem;
  font-weight: 600;
}

/* Logout Button Styles */
.logout {
  display: inline-block;
  background-color: #e74c3c;
  color: white;
  padding: 15px 30px;
  border-radius: 10px;
  text-decoration: none;
  transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
  animation: slideIn 1.3s ease-in-out;
}

.logout:hover {
  background-color: #c0392b;
  transform: translateY(-5px);
}

.logout:active {
  background-color: #d35400;
  transform: scale(0.98);
}

.logout i {
  margin-right: 10px;
}

/* Animations */
@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@keyframes slideIn {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
<body>
  
  <div class="container">
    <header>
      <h1 class="title">Session Administrateur</h1>
      <p class="welcome">Bienvenue, <?php echo $_SESSION['username']; ?>!</p>
    </header>
    <nav class="nav-menu">
      <a href="manage_users.php" class="nav-item">
        <div class="icon-container">
          <i class="fas fa-user-plus"></i>
        </div>
        <span>Créer un utilisateur</span>
      </a>
      <a href="dashboard.php" class="nav-item">
        <div class="icon-container">
          <i class="fas fa-tachometer-alt"></i>
        </div>
        <span>Dashboard</span>
      </a>
    </nav>
    <a href="logout.php" class="logout">
      <i class="fas fa-sign-out-alt"></i>
      Déconnexion
    </a>
  </div>
</body>
</html>