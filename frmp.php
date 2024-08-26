<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Récupération des IDs des réformes sélectionnées
$reforme_ids = isset($_POST['reforme_ids']) ? $_POST['reforme_ids'] : [];
if (empty($reforme_ids)) {
    header("Location: reforme.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Réforme</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CONTENT -->
    <section id="content">
        <main>
            <div class="form-container">
                <h2>Formulaire de Réforme</h2>
                <form action="imprimer.php" method="POST">
                    <input type="hidden" name="reforme_ids" value="<?php echo htmlspecialchars(json_encode($reforme_ids)); ?>">
                    
                    
                    <div class="form-group">
                        <label for="je_soussigne">Je soussigné :</label>
                        <input type="text" id="je_soussigne" name="je_soussigne" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="qualite">Qualité :</label>
                        <input type="text" id="qualite" name="qualite" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service">Service :</label>
                        <input type="text" id="service" name="service" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Terminé</button>
                </form>
            </div>
        </main>
    </section>
    <!-- CONTENT -->

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #6e8efb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>

</body>
</html>
