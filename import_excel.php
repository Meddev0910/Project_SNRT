<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

require_once 'lib/SimpleXLSX.php';
use Shuchkin\SimpleXLSX;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

$error_messages = [];
$success_count = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excelFile'])) {
    if ($xlsx = SimpleXLSX::parse($_FILES['excelFile']['tmp_name'])) {
        $rows = $xlsx->rows();
        $dataRows = array_slice($rows, 1);

        foreach ($dataRows as $row) {
            $appareil = $conn->real_escape_string($row[0]);
            $organisation = $conn->real_escape_string($row[1]);
            $status = $conn->real_escape_string($row[2]);
            $marque = $conn->real_escape_string($row[3]);
            $model = $conn->real_escape_string($row[4]);
            $n_serie = $conn->real_escape_string($row[5]);
            $date_achat = $conn->real_escape_string($row[6]);
            $date_mise_production = $conn->real_escape_string($row[7]);

            $check_query = "SELECT * FROM stock WHERE n_serie = '$n_serie'";
            $result = $conn->query($check_query);

            if ($result->num_rows > 0) {
                $error_messages[] = "Erreur : L'appareil avec le numéro de série '$n_serie' existe déjà.";
            } else {
                $sql = "INSERT INTO stock (appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production) 
                        VALUES ('$appareil', '$organisation', '$status', '$marque', '$model', '$n_serie', '$date_achat', '$date_mise_production')";

                if ($conn->query($sql) === TRUE) {
                    $success_count++;

                    // Enregistrer l'historique de l'ajout
                    $utilisateur = $conn->real_escape_string($_SESSION['username']);
                    $changement = "Ajout de l'appareil(Fichier importé): $n_serie";
                    
                    // Échapper la chaîne de changement
                    $changement = $conn->real_escape_string($changement);

                    $historique_sql = "INSERT INTO historique (utilisateur, n_serie, changement) 
                                       VALUES ('$utilisateur', '$n_serie', '$changement')";
                    
                    if (!$conn->query($historique_sql)) {
                        $error_messages[] = "Erreur lors de l'enregistrement de l'historique pour l'appareil '$n_serie' : " . $conn->error;
                    }
                } else {
                    $error_messages[] = "Erreur lors de l'ajout de l'appareil '$n_serie' : " . $conn->error;
                }
            }
        }
    } else {
        $error_messages[] = SimpleXLSX::parseError();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importer un fichier Excel</title>
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

        /* Style pour le lien "Retour à la Gestion de Stock" */
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

        /* Cacher l'input par défaut */
input[type="file"] {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}

/* Style pour le label qui remplace l'input */
input[type="file"] + label {
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(135deg, #ff7eb3, #ff758c);
    display: inline-block;
    padding: 12px 24px;
    cursor: pointer;
    border-radius: 12px;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0px 4px 15px rgba(255, 117, 140, 0.4);
}

/* Effet au survol */
input[type="file"] + label:hover {
    background: linear-gradient(135deg, #ff758c, #ff7eb3);
    box-shadow: 0px 6px 20px rgba(255, 117, 140, 0.6);
}

/* Effet au focus */
input[type="file"]:focus + label {
    outline: 2px solid #ff7eb3;
    outline-offset: 4px;
}

/* Style pour le texte "Aucun fichier choisi" */
input[type="file"] + label + span {
    margin-left: 20px;
    font-size: 1rem;
    color: #ddd;
}

/* Animation légère pour le label */
input[type="file"] + label {
    animation: fadeInLabel 0.5s ease-in-out;
}

@keyframes fadeInLabel {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style> 
</head>
<body>
    <div id="content">
        <h3>Importer un fichier Excel</h3>

        <?php if ($success_count > 0): ?>
            <p class='msg success'>Données importées avec succès! <?php echo $success_count; ?> appareil(s) ajouté(s).</p>
        <?php endif; ?>

        <?php if (!empty($error_messages)): ?>
            <p class='msg error'>
                <?php foreach ($error_messages as $error): ?>
                    <?php echo $error; ?><br>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <form action="import_excel.php" method="POST" enctype="multipart/form-data">
            <label for="excelFile">Choisir un fichier Excel :</label>
            <input type="file" name="excelFile" id="excelFile" required>
            <label for="excelFile">Choisir un fichier</label>
            <span id="file-chosen">Aucun fichier choisi</span>
            <button type="submit">Importer</button>
        </form>

        <!-- Bouton de retour au Dashboard -->
        <div class="form-container">
            <a href="dashboard.php">Retour au Dashboard</a>
        </div>

    </div>
<script>
    const realFileBtn = document.getElementById("excelFile");
    const customTxt = document.getElementById("file-chosen");

    realFileBtn.addEventListener("change", function() {
        if (realFileBtn.value) {
            customTxt.innerHTML = realFileBtn.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
        } else {
            customTxt.innerHTML = "Aucun fichier choisi";
        }
    });

</script>
</body>
</html>
