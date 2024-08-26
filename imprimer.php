<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Récupération des données envoyées par frmp.php
$reforme_ids = isset($_POST['reforme_ids']) ? json_decode($_POST['reforme_ids'], true) : [];
$observation = isset($_POST['observation']) ? htmlspecialchars($_POST['observation']) : '';
$je_soussigne = isset($_POST['je_soussigne']) ? htmlspecialchars($_POST['je_soussigne']) : '';
$qualite = isset($_POST['qualite']) ? htmlspecialchars($_POST['qualite']) : '';
$service = isset($_POST['service']) ? htmlspecialchars($_POST['service']) : '';

if (empty($reforme_ids)) {
    echo "Aucune réforme sélectionnée.";
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

// Requête pour récupérer les détails des réformes sélectionnées
$reforme_ids_placeholder = implode(',', array_fill(0, count($reforme_ids), '?'));
$sql = "SELECT * FROM stock WHERE n_serie IN ($reforme_ids_placeholder)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($reforme_ids)), ...$reforme_ids);
$stmt->execute();
$result = $stmt->get_result();
$reformes = $result->fetch_all(MYSQLI_ASSOC);

// Déconnexion de la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression des Réformes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .print-container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .print-container h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #6e8efb;
            color: #fff;
        }
        @media print {
            .print-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .return-button {
                display: none; /* Hide the button when printing */
            }
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
            border: none;
        }
        .footer-table td {
            padding-top: 40px;
            border: none; /* Hide the table borders */
        }
        .return-button {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 20px;
            background-color: #6e8efb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
        }
        .return-button:hover {
            background-color: #567ed4;
        }
    </style>
</head>
<body>

    <div class="print-container">
       
        <img src="logo.png" alt="Logo" class="logo">
        <h3>Royaume du Maroc</h3>
        <h3>Société Nationale de Radiodiffusion et de Télévision</h3>
        <h4>Direction Technique Et Des Systèmes d'Information</h4>
        <h4>Service Magasin</h4>
        <p>Le <?php echo date('d/m/Y'); ?></p> <!-- Affichage de la date actuelle --> 
        <h2>Liste des Appareil Réformes Sélectionnées</h2>
        <p>Je soussigné : <?php echo htmlspecialchars($je_soussigne); ?></p>
    <p>Qualité : <?php echo htmlspecialchars($qualite); ?></p>
    <p>Service : <?php echo htmlspecialchars($service); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Type d'Appareil</th>
                    <th>Organisation</th>
                    <th>Status</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Numéro de Série</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reformes as $reforme): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reforme['appareil']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['organisation']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['status']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['marque']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['model']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['n_serie']); ?></td>
                        <td><?php echo htmlspecialchars($reforme['contact']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="footer-table">
            <tr>
                <td>VU POUR ACCORD SERVICE MAGASIN</td>
                <td>UTILISATEUR NOM ET SIGNATURE</td>
                <td>LU ET APPROUVE SIGNATURE ET CACHET</td>
            </tr>
        </table>

        <!-- Return to Dashboard Button -->
        <a href="Dashboard.php" class="return-button" id="returnButton">Retour au Dashboard</a>

    </div>

    <script>
        // Automatically print the page on load
        window.onload = function() {
            window.print();
        };

        // Hide the return button during printing
        window.onbeforeprint = function() {
            document.getElementById('returnButton').style.display = 'none';
        };

        // Show the return button after printing is done
        window.onafterprint = function() {
            document.getElementById('returnButton').style.display = 'inline-block';
        };
    </script>

</body>
</html>
