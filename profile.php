<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "user_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$user_id = $_SESSION['loggedin'];

// Récupérer les données du formulaire
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$organisation = $_POST['organisation'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$profile_picture = $_FILES['profile_picture']['name'];

// Vérifier le mot de passe actuel
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!password_verify($current_password, $user['password'])) {
        echo "Mot de passe actuel incorrect.";
        exit();
    }
} else {
    echo "Erreur: Utilisateur non trouvé.";
    exit();
}

// Changement de mot de passe
if (!empty($new_password)) {
    if ($new_password === $confirm_password) {
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password_hash, $user_id);
        $stmt->execute();
    } else {
        echo "Les nouveaux mots de passe ne correspondent pas.";
        exit();
    }
}

// Changement de photo de profil
if (!empty($profile_picture)) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture);
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);

    $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $target_file, $user_id);
    $stmt->execute();
}

// Mise à jour des informations utilisateur
$sql = "UPDATE users SET name = ?, email = ?, phone = ?, organisation = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $email, $phone, $organisation, $user_id);

if ($stmt->execute()) {
    echo "Profil mis à jour avec succès.";
    header("Location: profile.php"); // Rediriger vers le profil après la mise à jour
} else {
    echo "Erreur lors de la mise à jour du profil: " . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Profil Utilisateur</h1>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Photo de profil" class="profile-pic">
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <label for="name">Nom Complet:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Adresse Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Numéro de Téléphone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

            <label for="organisation">Organisation:</label>
            <input type="text" id="organisation" name="organisation" value="<?php echo htmlspecialchars($user['organisation']); ?>">

            <label for="profile_picture">Changer la Photo de Profil:</label>
            <input type="file" id="profile_picture" name="profile_picture">

            <label for="current_password">Mot de Passe Actuel:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">Nouveau Mot de Passe:</label>
            <input type="password" id="new_password" name="new_password">

            <label for="confirm_password">Confirmer le Nouveau Mot de Passe:</label>
            <input type="password" id="confirm_password" name="confirm_password">

            <button type="submit" name="update_profile">Mettre à Jour le Profil</button>
        </form>

        <a href="logout.php" class="logout">Déconnexion</a>
    </div>
</body>
</html>
