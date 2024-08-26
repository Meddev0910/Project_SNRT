<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login_page.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("La connexion a échoué: " . $conn->connect_error);
    }

    $selectedDevices = $_POST['selectedDevices'];
    $currentUser = isset($_POST['currentUser']) ? $_POST['currentUser'] : '';  // Ajout de vérification
    $observation = $_POST['observation'];
    $signature = $_POST['signature'];
    $qualite = $_POST['qualite'];
    $service = $_POST['service'];

    if (!empty($selectedDevices)) {
        $ids = implode(',', array_map('intval', $selectedDevices));
        $sql = "UPDATE stock SET status = 'Production' WHERE id IN ($ids)";
    
        if ($conn->query($sql) !== TRUE) {
            die("Erreur lors de la mise à jour des appareils : " . $conn->error);
        }
    } else {
        die("Aucun appareil sélectionné.");
    }

    $sql = "SELECT appareil, marque, model, n_serie FROM stock WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Erreur de requête SQL : " . $conn->error);
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impression des appareils sélectionnés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }

        th, td { 
            border: 1px solid black; 
            padding: 8px; 
            text-align: left; 
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            display: block;
            margin: 0 auto;
            width: 150px;
        }

        .signature {
            margin-top: 20px;
            text-align: center;
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
            border: none;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #2ecc71;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo" class="logo">
        <h2>Royaume du Maroc</h2>
        <h3>Société Nationale de Radiodiffusion et de Télévision</h3>
        <h4>Direction Technique Et Des Systèmes d'Information</h4>
        <h4>Service Magasin</h4>
        <p>Le <?php echo date('d/m/Y'); ?></p>
    </div>
    <div class="signature">
        <p>Prise en charge</p>
    </div>
    <p>Je soussigné : <?php echo htmlspecialchars($signature); ?></p>
    <p>Qualité : <?php echo htmlspecialchars($qualite); ?></p>
    <p>Service : <?php echo htmlspecialchars($service); ?></p>

    <table>
        <tr>
            <th>Qté</th>
            <th>Désignation</th>
            <th>Marque</th>
            <th>Type</th>
            <th>N° SERIE</th>
            <th>Observation</th>
        </tr>
        <?php if ($result->num_rows > 0) { 
            $counter = 1;
            while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>01</td>
                    <td><?php echo $row['appareil']; ?></td>
                    <td><?php echo $row['marque']; ?></td>
                    <td><?php echo $row['model']; ?></td>
                    <td><?php echo $row['n_serie']; ?></td>
                    <td><?php echo htmlspecialchars($observation); ?></td>
                </tr>
            <?php } 
        } else { ?>
            <tr>
                <td colspan="8">Aucun appareil sélectionné</td>
            </tr>
        <?php } ?>
    </table>

    <!-- Footer table with hidden borders -->
    <table class="footer-table">
        <tr>
            <td>VU POUR ACCORD SERVICE MAGASIN</td>
            <td>UTILISATEUR NOM ET SIGNATURE</td>
            <td>LU ET APPROUVE SIGNATURE ET CACHET</td>
        </tr>
    </table>

    <!-- Back to Dashboard button -->
    <button id="backButton" class="back-button" onclick="goToDashboard()">Retour au tableau de bord</button>

    <script>
        // Function to redirect back to the dashboard
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }

        // Hide the back button when printing starts
        window.onbeforeprint = function() {
            document.getElementById('backButton').classList.add('hidden');
        };

        // Show the back button after printing is done
        window.onafterprint = function() {
            document.getElementById('backButton').classList.remove('hidden');
        };

        // Automatically trigger print dialog when the page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
