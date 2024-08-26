<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$message = "";

// Gérer la suppression d'un utilisateur
if (isset($_GET['delete'])) {
    $username_to_delete = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username_to_delete);

    if ($stmt->execute()) {
        $message = "Utilisateur supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression de l'utilisateur: " . $stmt->error;
    }

    $stmt->close();
    header("Location: manage_users.php"); // Redirection pour éviter la suppression multiple en actualisant
    exit();
}

// Gérer l'édition d'un utilisateur
if (isset($_GET['edit'])) {
    $username_to_edit = $conn->real_escape_string($_GET['edit']);
    $sql = "SELECT username, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username_to_edit);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $updated_username = $conn->real_escape_string($_POST['username']);
    $updated_role = $_POST['role'];
    $original_username = $conn->real_escape_string($_POST['original_username']);
    $updated_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($updated_username && $updated_role) {
        if ($updated_password) {
            // Si un nouveau mot de passe est fourni, mettez à jour le mot de passe aussi
            $sql = "UPDATE users SET username = ?, role = ?, password = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $updated_username, $updated_role, $updated_password, $original_username);
        } else {
            // Sinon, ne mettez pas à jour le mot de passe
            $sql = "UPDATE users SET username = ?, role = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $updated_username, $updated_role, $original_username);
        }

        if ($stmt->execute()) {
            $message = "Utilisateur mis à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour de l'utilisateur: " . $stmt->error;
        }

        $stmt->close();
        header("Location: manage_users.php"); // Redirection après mise à jour
        exit();
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

// Gérer l'ajout d'un nouvel utilisateur ou sous-administrateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update'])) {
    $new_username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : null;
    $new_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $new_role = isset($_POST['role']) ? $_POST['role'] : null;

    if ($new_username && $new_password && $new_role) {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $new_username, $new_password, $new_role);
            
            if ($stmt->execute()) {
                $message = "Nouvel utilisateur ajouté avec succès.";
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Erreur de préparation de la requête: " . $conn->error;
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

// Récupérer la liste des utilisateurs existants
$sql = "SELECT username, role FROM users WHERE role = 'subadmin' OR role = 'user'";
$result = $conn->query($sql);

if ($result === false) {
    die("Erreur lors de la récupération des utilisateurs: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="style2.css">
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
  width: 90%;
  max-width: 1000px;
  animation: fadeIn 0.5s ease-in-out;
}

/* Header Styles */
header {
  text-align: center;
  margin-bottom: 40px;
}

h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 0;
  animation: slideIn 0.7s ease-in-out;
}

p {
  font-size: 1.4rem;
  color: #34495e;
  animation: slideIn 0.9s ease-in-out;
}

/* Content Styles */
.content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}

.add-user,
.user-list,
.edit-user {
  background-color: #f5f5f5;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  animation: slideIn 1.1s ease-in-out;
}

.message {
  color: #e74c3c;
  font-weight: 600;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 5px;
}

input,
select {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
}

.btn {
  display: inline-block;
  background-color: #3498db;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  text-decoration: none;
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

.user-list table {
  width: 100%;
  border-collapse: collapse;
}

.user-list th,
.user-list td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.user-list th {
  background-color: #f2f2f2;
}

.edit-user {
  background-color: #f5f5f5;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Actions Styles */
.actions {
  display: flex;
  justify-content: space-between;
  margin-top: 40px;
  animation: slideIn 1.3s ease-in-out;
}

.logout {
  background-color: #e74c3c;
}

.logout:hover {
  background-color: #c0392b;
}

.logout:active {
  background-color: #d35400;
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
      <h1>Gestion des utilisateurs</h1>
      <p>Bienvenue, <?php echo $_SESSION['username']; ?>!</p>
    </header>

    <div class="content">
      <section class="add-user">
        <h2>Ajouter un nouvel utilisateur</h2>
        <p class="message"><?php echo $message; ?></p>
        <form method="POST" action="manage_users.php">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
          </div>
          <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role">
              <option value="user">User</option>
              <option value="subadmin">subadmin</option>
            </select>
          </div>
          <button type="submit" class="btn">Ajouter un utilisateur</button>
        </form>
      </section>

      <?php if (isset($user_data)): ?>
        <section class="edit-user">
          <h2>Modifier l'utilisateur</h2>
          <p class="message"><?php echo $message; ?></p>
          <form method="POST" action="manage_users.php">
            <input type="hidden" name="original_username" value="<?php echo htmlspecialchars($user_data['username']); ?>">
            <div class="form-group">
              <label for="username">Nom d'utilisateur</label>
              <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>
            <div class="form-group">
              <label for="role">Rôle</label>
              <select id="role" name="role">
                <option value="user" <?php if ($user_data['role'] == 'user') echo 'selected'; ?>>User</option>
                <option value="subadmin" <?php if ($user_data['role'] == 'subadmin') echo 'selected'; ?>>subadmin</option>
              </select>
            </div>
            <div class="form-group">
              <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
              <input type="password" id="password" name="password" placeholder="Nouveau mot de passe">
            </div>
            <button type="submit" name="update" class="btn">Mettre à jour l'utilisateur</button>
          </form>
        </section>
      <?php endif; ?>

      <section class="user-list">
        <h2>Liste des utilisateurs</h2>
        <table>
          <thead>
            <tr>
              <th>Nom d'utilisateur</th>
              <th>Rôle</th>
              <th>Action</th> <!-- Nouvelle colonne pour les actions -->
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['username']; ?></td>
                  <td><?php echo $row['role']; ?></td>
                  <td>
                    <a href="manage_users.php?edit=<?php echo $row['username']; ?>" class="btn"><i class="fas fa-edit"></i></a>
                    <a href="manage_users.php?delete=<?php echo $row['username']; ?>" class="btn logout"><i class="fas fa-trash-alt"></i></a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">Aucun utilisateur trouvé.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </div>

    <div class="actions">
      <a href="admin.php" class="btn">Retour au dashboard admin</a>
      <a href="logout.php" class="btn logout">Déconnexion</a>
    </div>
  </div>
</body>
</html>
