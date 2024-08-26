<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login_page.html");
    exit();
}

include('db_connection.php'); // Connexion à la base de données

// Fonction pour ajouter un enregistrement dans l'historique
function ajouterHistorique($conn, $utilisateur, $n_serie, $changement) {
    $stmt = $conn->prepare("INSERT INTO historique (utilisateur, n_serie, changement) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $utilisateur, $n_serie, $changement);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['export'])) {
    $selected_ids = $_POST['selected_ids'];

    if (!empty($selected_ids)) {
        // Sanitize and prepare the selected IDs
        $ids = implode(',', array_map('intval', $selected_ids));

        // Prepare the query using prepared statements
        $stmt = $conn->prepare("SELECT appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production FROM stock WHERE id IN ($ids)");
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Check if result is valid
            if ($result) {
                // Définir le nom du fichier et envoyer les en-têtes appropriés
                $filename = "appareils_export.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename="' . $filename . '"');

                // Ouvrir un flux en sortie
                $output = fopen('php://output', 'w');

                // Ajouter la ligne d'en-tête au fichier CSV
                fputcsv($output, array('Appareil', 'Organisation', 'Status', 'Marque', 'Model', 'Numéro de Série', 'Date d\'Achat', 'Date de Mise en Production'));

                // Ajouter les données dans le fichier CSV et l'historique
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);

                    // Enregistrer dans l'historique
                    $utilisateur = $_SESSION['username']; // Assurez-vous que le nom d'utilisateur est enregistré dans la session
                    $n_serie = $row['n_serie'];
                    $changement = "Appareil exporté: " . $row['n_serie'];

                    ajouterHistorique($conn, $utilisateur, $n_serie, $changement);
                }

                // Fermer le flux de sortie
                fclose($output);
                exit();
            } else {
                die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
            }
        } else {
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }
    } else {
        echo "Aucun appareil sélectionné.";
    }
}

// Pagination logic
$items_per_page = 5; // Nombre d'éléments par page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Numéro de la page actuelle
$offset = ($page - 1) * $items_per_page; // Offset pour la requête SQL

// Compter le nombre total d'éléments pour la pagination
$total_items_sql = "SELECT COUNT(*) as total FROM historique"; // Utiliser $search_query si nécessaire
$total_items_result = $conn->query($total_items_sql);
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];

// Calculer le nombre total de pages
$total_pages = ceil($total_items / $items_per_page);

// Récupérer les données pour la page actuelle et les organiser par statut
$query = "SELECT * FROM stock ORDER BY stock.status DESC, stock.id ASC LIMIT $offset, $items_per_page";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . mysqli_error($conn));
}

// Organiser les résultats par statut
$grouped_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $grouped_data[$row['status']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Exporter CSV</title>
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f7f9fc;
            --accent-color: #34c759;
            --text-color: #333333;
            --font-family: 'Poppins', sans-serif;
            --border-radius: 12px;
            --transition-speed: 0.3s;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: var(--font-family);
            background-color: var(--secondary-color);
            color: var(--text-color);
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 36px;
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0.5px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background-color: #3498db;
            border-radius: 2px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all var(--transition-speed) ease;
            margin: 15px 0;
            box-shadow: var(--box-shadow);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background-color: #3a7cbd;
            box-shadow: var(--hover-shadow);
            transform: translateY(-3px);
        }

        .status-group {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #f7f9fc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .status-group input[type="checkbox"] {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #4a90e2;
            border-radius: 5px;
            outline: none;
            cursor: pointer;
            position: relative;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .status-group input[type="checkbox"]:checked {
            background-color: #34c759;
            border-color: #34c759;
        }

        .status-group input[type="checkbox"]:checked::before {
            content: '\2714'; /* Symbole de coche */
            font-size: 16px;
            color: white;
            position: absolute;
            top: 1px;
            left: 3px;
        }

        .status-group label {
            font-size: 16px;
            color: #333333;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
        }

        .status-group:hover {
            background-color: #e2f0ff;
        }

        .status-group:hover label {
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 20px;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th, td {
            padding: 18px 25px;
            text-align: left;
            border: none;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }

        tbody tr {
            background-color: white;
            box-shadow: var(--box-shadow);
            transition: all var(--transition-speed) ease;
            border-radius: var(--border-radius);
        }

        tbody tr:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        td:first-child, th:first-child {
            border-top-left-radius: var(--border-radius);
            border-bottom-left-radius: var(--border-radius);
        }

        td:last-child, th:last-child {
            border-top-right-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 20px;
                box-shadow: var(--box-shadow);
            }

            td {
                position: relative;
                padding-left: 50%;
                text-align: right;
                border-bottom: 1px solid #eee;
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 12px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: var(--primary-color);
            }

            td:last-child {
                border-bottom: none;
            }
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination a, .pagination span {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .pagination a.active {
            background-color: var(--secondary-color);
        }

        .pagination a:hover {
            background-color: #2980b9;
        }

        .pagination .prev, .pagination .next {
            background-color: orange;
            color: white;
        }

        .pagination .disabled {
            pointer-events: none;
            background-color: #ccc;
            color: #666;
        }
    </style>

    <script>
        function toggleCheckboxes() {
            const checkboxes = document.querySelectorAll('.checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.style.display = checkbox.style.display === 'none' ? 'inline-block' : 'none';
            });
        }

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }

        function toggleSelectGroup(source, status) {
            const checkboxes = document.querySelectorAll('.' + status);
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
    </script>
</head>
<body>
    <h2>Exporter Appareils en CSV</h2>
    
    <!-- Bouton Retour au dashboard -->
    <a href="dashboard.php"><button type="button">Retour au dashboard</button></a>

    <!-- Bouton Sélectionner -->
    <button type="button" onclick="toggleCheckboxes()">Sélectionner</button>

    <form method="post" action="export_excel.php">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th> <!-- Sélectionner tout -->
                    <th>Appareil</th>
                    <th>Organisation</th>
                    <th>Status</th>
                    <th>Marque</th>
                    <th>Model</th>
                    <th>Numéro de Série</th>
                    <th>Date de Réception</th>
                    <th>Date de Mise en Production</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grouped_data as $status => $rows): ?>
                    <tr class="status-group">
                        <td colspan="9">
                            <input type="checkbox" onclick="toggleSelectGroup(this, '<?php echo $status; ?>')">
                            <label><?php echo ucfirst($status); ?></label>
                        </td>
                    </tr>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="<?php echo $row['id']; ?>" class="checkbox <?php echo $status; ?>" style="display:none;"></td>
                            <td data-label="Appareil"><?php echo $row['appareil']; ?></td>
                            <td data-label="Organisation"><?php echo $row['organisation']; ?></td>
                            <td data-label="Status"><?php echo $row['status']; ?></td>
                            <td data-label="Marque"><?php echo $row['marque']; ?></td>
                            <td data-label="Model"><?php echo $row['model']; ?></td>
                            <td data-label="Numéro de Série"><?php echo $row['n_serie']; ?></td>
                            <td data-label="Date d'Achat"><?php echo $row['date_achat']; ?></td>
                            <td data-label="Date de Mise en Production"><?php echo $row['date_mise_production']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" name="export">Exporter</button>

        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="export_excel.php?page=<?php echo $page - 1; ?>" class="prev">&laquo;</a>
            <?php else: ?>
                <span class="prev disabled">&laquo;</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="export_excel.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="export_excel.php?page=<?php echo $page + 1; ?>" class="next">&raquo;</a>
            <?php else: ?>
                <span class="next disabled">&raquo;</span>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>
