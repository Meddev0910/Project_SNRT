<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login_page.html");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// Initialize $search_query and set up search functionality if needed
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = " WHERE appareil LIKE '%$search%' OR marque LIKE '%$search%' OR model LIKE '%$search%' OR n_serie LIKE '%$search%'";
}

// Pagination logic
$items_per_page = 5; // Changed from 10 to 5 to show 5 items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $items_per_page; // Offset for the SQL query

// Count total items for pagination
$total_items_sql = "SELECT COUNT(*) as total FROM stock" . $search_query;
$total_items_result = $conn->query($total_items_sql);
if ($total_items_result === false) {
    die("Erreur de requête SQL : " . $conn->error);
}
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

// Récupérer les données des appareils avec pagination
$sql = "SELECT id, appareil, marque, model, n_serie FROM stock where status='stock'" . $search_query . " LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);

// Vérification de la requête SQL
if ($result === false) {
    die("Erreur de requête SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prise en charge</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        #searchBar {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        #searchBar:focus {
            outline: none;
            box-shadow: 0 0 5px var(--primary-color);
        }

        .device-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }

        .device-table th, .device-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .device-table th {
            background-color: var(--primary-color);
            color: white;
        }

        .device-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .device-table tr:hover {
            background-color: #e6e6e6;
            transition: background-color 0.3s ease;
        }

        .buttons-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .select-checkbox {
            display: none;
        }

        .device-table .checkbox-column {
            width: 50px;
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
</head>
<body>

    <h2>Prise en charge</h2>

    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Rechercher un appareil...">
    </div>

    <form id="deviceForm" method="post" action="frm.php">
        <table class="device-table" id="deviceTable">
            <thead>
                <tr>
                    <th class="checkbox-column"></th>
                    <th>Appareil</th>
                    <th>Marque</th>
                    <th>Model</th>
                    <th>N° Série</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="checkbox-column"><input type="checkbox" class="select-checkbox" name="selectedDevices[]" value="<?php echo $row['id']; ?>"></td>
                            <td><?php echo $row['appareil']; ?></td>
                            <td><?php echo $row['marque']; ?></td>
                            <td><?php echo $row['model']; ?></td>
                            <td><?php echo $row['n_serie']; ?></td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr>
                        <td colspan="5">Aucun appareil trouvé</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="buttons-container">
            <button type="button" onclick="toggleCheckboxes()">Sélectionner</button>
            <button type="button" onclick="goToDashboard()">Retour au tableau de bord</button>
            <button type="submit">Imprimer</button>
        </div>

          <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="prise_charge.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>" class="prev">&laquo;</a>
            <?php else: ?>
                <span class="prev disabled">&laquo;</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="prise_charge.php?page=<?php echo $i; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="prise_charge.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>" class="next">&raquo;</a>
            <?php else: ?>
                <span class="next disabled">&raquo;</span>
            <?php endif; ?>
        </div>
    </form>

    <script>
        // Toggle display of checkboxes
        function toggleCheckboxes() {
            const checkboxes = document.querySelectorAll('.select-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.style.display = checkbox.style.display === 'block' ? 'none' : 'block';
            });
        }

        // Redirect to dashboard
        function goToDashboard() {
            window.location.href = 'dashboard.php';
        }

        // Search functionality for the table
        document.getElementById('searchBar').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#deviceTable tbody tr');
            
            rows.forEach(row => {
                const cells = Array.from(row.getElementsByTagName('td')).slice(1); // Exclude checkbox column
                const match = cells.some(cell => cell.textContent.toLowerCase().includes(filter));
                
                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>
