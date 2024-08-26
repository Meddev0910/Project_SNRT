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

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$success_message = "";
$error_message = "";

$utilisateur = $_SESSION['username']; // Assurez-vous que le nom d'utilisateur est stocké en session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appareil = $_POST['appareil'];
    if ($appareil == "autre") {
        $appareil = $_POST['autre_appareil'];
    }
    $organisation = $_POST['organisation'];
    $status = $_POST['status'];
    $marque = $_POST['marque'];
    $model = $_POST['model'];
    $n_serie = $_POST['n_serie'];
    $date_achat = $_POST['date_achat'];
    $date_mise_production = $_POST['date_mise_production'];
    $contact = $_POST['contact'];

    $document = '';
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $document = basename($_FILES['document']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $document;
        move_uploaded_file($_FILES['document']['tmp_name'], $target_file);
    }

    $sql = "INSERT INTO stock (appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production, contact, document) 
            VALUES ('$appareil', '$organisation', '$status', '$marque', '$model', '$n_serie', '$date_achat', '$date_mise_production', '$contact', '$document')";

    if ($conn->query($sql) === TRUE) {
        // Enregistrer l'action dans la table historique
        $changement = "Ajouté un appareil : $n_serie";
        $sql_historique = "INSERT INTO historique (utilisateur, n_serie, changement) VALUES ('$utilisateur', '$n_serie', '$changement')";
        $conn->query($sql_historique);

        $success_message = "L'appareil a été ajouté avec succès.";
    } else {
        $error_message = "Erreur lors de l'ajout de l'appareil : " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ajouter un Appareil</title>
    <style>
         
        
        /* Background similar to the provided image */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0d2748, #132641);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Form container styling */
        #content {
            background: rgba(13, 39, 72, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            margin-right: 600px;
            transition: transform 0.3s ease-in-out;
            animation: fadeIn 1s ease-in-out;
            color: #fff;
        }
        /* Animation légère pour l'apparition de la carte */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        /* Title styling */
        h3 {
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form and its elements */
        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            color: #ddd;
            font-size: 1rem;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: #24344d;
            color: #fff;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 8px rgba(110, 142, 251, 0.6);
            outline: none;
        }

        /* Button styling */
        button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #6e8efb, #a777e3);
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0px 4px 12px rgba(110, 142, 251, 0.3);
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        button:hover {
            box-shadow: 0px 6px 20px rgba(110, 142, 251, 0.4);
            transform: translateY(-3px);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0px 3px 8px rgba(110, 142, 251, 0.3);
        }

        /* Message de succès */
        .msg.success {
            color: #27ae60; /* Vert pour le succès */
            background-color: rgba(39, 174, 96, 0.1); /* Fond vert pâle pour plus de contraste */
            border: 1px solid #27ae60;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-size: 1rem;
            margin-bottom: 20px;
            animation: fadeIn var(--transition-speed) ease-in-out;
        }

        p {
            font-size: 1rem;
            margin-top: 10px;
            color: #e74c3c;
            text-align: center;
        }

        /* Style pour le lien "Retour au Dashboard" */
        .form-container a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6e8efb; /* Couleur du texte pour correspondre au bouton */
            font-weight: bold; /* Texte en gras pour plus de visibilité */
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .form-container a:hover {
            color: #a777e3; /* Changement de couleur au survol pour correspondre au dégradé du bouton */
            transform: scale(1.05); /* Agrandissement léger au survol */
        }

    
        #autre_appareil_input {
            display: none;
            margin-top: 10px;
        }
    </style>
  
    <script>
        function toggleAutreAppareilInput() {
            var appareilSelect = document.getElementById("appareil");
            var autreAppareilInput = document.getElementById("autre_appareil_input");
            if (appareilSelect.value === "autre") {
                autreAppareilInput.style.display = "block";
                document.getElementById("autre_appareil").required = true;
            } else {
                autreAppareilInput.style.display = "none";
                document.getElementById("autre_appareil").required = false;
            }
        }
    </script>
</head>
<body>
    <section id="content">
        <div class="form-container">
            <h2>Ajouter un Appareil</h2>

            <?php if (!empty($success_message)) : ?>
                <p class="msg success"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)) : ?>
                <p class="msg error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="ajouter_apparaille.php" enctype="multipart/form-data">
                <label for="appareil">Type d'Appareil</label>
                <select id="appareil" name="appareil" required onchange="toggleAutreAppareilInput()">
                    <option value="PC">PC</option>
                    <option value="Imprimante">Imprimante</option>
                    <option value="Écran">Écran</option>
                    <option value="autre">Autre</option>
                </select>

                <div id="autre_appareil_input">
                    <label for="autre_appareil">Veuillez spécifier</label>
                    <input type="text" id="autre_appareil" name="autre_appareil">
                </div>

                <label for="organisation">Organisation</label>
                <input type="text" id="organisation" name="organisation" required>

                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="stock">Stock</option>
                    <option value="production">Production</option>
                    <option value="reforme">Réforme</option>
                </select>

                <label for="marque">Marque</label>
                <input type="text" id="marque" name="marque" required>

                <label for="model">Modèle</label>
                <input type="text" id="model" name="model" required>

                <label for="n_serie">Numéro de Série</label>
                <input type="text" id="n_serie" name="n_serie" required>

                <label for="date_achat">Date de Réception</label>
                <input type="date" id="date_achat" name="date_achat" required>

                <label for="date_mise_production">Date de Mise en Production</label>
                <input type="date" id="date_mise_production" name="date_mise_production" required>

                <label for="contact">Contact</label>
                <input type="text" id="contact" name="contact" >

                <label for="document">Document (PDF uniquement)</label>
                <input type="file" id="document" name="document" accept="application/pdf">

                <button type="submit">Ajouter l'Appareil</button>
            </form>

            <a href="dashboard.php">Retour au Dashboard</a>
        </div>
    </section>
</body>
</html>
