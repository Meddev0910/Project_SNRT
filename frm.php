<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login_page.html");
    exit();
}

// Vérifiez si des appareils ont été sélectionnés
if (!isset($_POST['selectedDevices']) || empty($_POST['selectedDevices'])) {
    echo "<script>alert('Veuillez sélectionner au moins un appareil.'); window.location.href='prise_charge.php';</script>";
    exit();
}

$selectedDevices = $_POST['selectedDevices'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'impression</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ecf0f1;
            margin: 0;
            padding: 20px;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <form action="print.php" method="post">
        <?php
        foreach ($selectedDevices as $device) {
            echo "<input type='hidden' name='selectedDevices[]' value='" . htmlspecialchars($device) . "'>";
        }
        ?>
        <input type="text" id="observation" name="observation" placeholder="Observation (optionnel)">
        <input type="text" id="signature" name="signature" placeholder="Je soussigné" required>
        <input type="text" id="qualite" name="qualite" placeholder="Qualité" required>
        <input type="text" id="service" name="service" placeholder="Service" required>
        <button type="submit">Terminé</button>
    </form>
</body>
</html>