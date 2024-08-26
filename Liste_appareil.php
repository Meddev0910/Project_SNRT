<?php
$current_page = 'stock';
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

// Récupérer la valeur de la recherche
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination logic
$items_per_page = 5; // Number of items to display per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $items_per_page; // Offset for SQL query

// Construire la requête SQL en fonction de la recherche
$sql = "SELECT appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production, contact, document 
        FROM stock ORDER BY stock.status DESC";

if ($search) {
    $sql .= " WHERE n_serie LIKE '%$search%'";
}

// Calculer le nombre total d'éléments pour la pagination
$total_items_sql = "SELECT COUNT(*) as total FROM stock";
if ($search) {
    $total_items_sql .= " WHERE n_serie LIKE '%$search%'";
}

$total_items_result = $conn->query($total_items_sql);
if ($total_items_result === false) {
    die("Erreur de requête SQL : " . $conn->error);
}
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];

// Calculer le nombre total de pages
$total_pages = ceil($total_items / $items_per_page);

// Ajouter des limites pour la pagination dans la requête principale
$sql .= " LIMIT $items_per_page OFFSET $offset";

$result = $conn->query($sql);

// Vérifier si la requête a échoué
if ($result === false) {
    die("Erreur de requête SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- My CSS -->
    <link rel="stylesheet" href="style.css">
    <title>Gestion de Stock</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .table-container {
            width: 90%;
            margin: 50px auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        table th {
            background-color: #6e8efb;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .table-container h2 {
            margin: 0;
            padding: 20px;
            background-color: #6e8efb;
            color: #ffffff;
            text-align: center;
            text-transform: uppercase;
            font-size: 24px;
            border-bottom: 2px solid #ddd;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #999;
        }

        @media (max-width: 768px) {
            table th, table td {
                font-size: 14px;
                padding: 10px;
            }

            .table-container h2 {
                font-size: 20px;
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
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.pagination a.active, .pagination span.active {
    background-color: #0056b3;
}

.pagination a:hover {
    background-color: #0056b3;
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

    <!-- SIDEBAR -->
<section id="sidebar">
    <a href="dashboard.php" class="logo_fr">
        <BR></BR>
        <img src="logo.png" alt="logo">
    </a>
    <ul class="side-menu top">
        <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class='bx bx-grid-alt'></i> 
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'stock') ? 'active' : ''; ?>">
            <a href="stock.php">
                <i class='bx bxs-store'></i> 
                <span class="text">Stock</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'gestion_stock') ? 'active' : ''; ?>">
            <a href="gestion_stock.php">
                <i class='bx bx-chart'></i> 
                <span class="text">Gestion de Stock</span>
            </a>
        </li>
    </ul>

    <ul class="side-menu">
        
        <li>
            <a href="logout.php" class="logout">
                 <i class='bx bxs-log-out-circle'></i>
                 <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>
<!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
    <i class='bx bx-menu'></i>
    <a href="#" class="nav-link">Categories</a>
    <form action="stock.php" method="GET">
    <div class="form-input">
        <input type="search" name="search" placeholder="Rechercher par numéro de série..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
    </div>
</form>



</nav>

        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="table-container">
                <h2>Liste des Appareils</h2>
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                
                                <th>Type d'Appareil</th>
                                <th>Organisation</th>
                                <th>Status</th>
                                <th>Marque</th>
                                <th>Modèle</th>
                                <th>Numéro de Série</th>
                                <th>Date de Réception</th>
                                <th>Date de Mise en Production</th>
                                <th>Contact</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    
                                    <td><?php echo htmlspecialchars($row['appareil']); ?></td>
                                    <td><?php echo htmlspecialchars($row['organisation']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marque']); ?></td>
                                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                                    <td><?php echo htmlspecialchars($row['n_serie']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_achat']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_mise_production']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                    <td>
                                        <?php if ($row['document']): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($row['document']); ?>" title="Télécharger" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                  <!-- Pagination links -->
                  <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="Liste_appareil.php?page=<?php echo $page - 1; ?>" class="prev">&laquo;</a>
                        <?php else: ?>
                            <span class="prev disabled">&laquo;</span>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="Liste_appareil.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="Liste_appareil.php?page=<?php echo $page + 1; ?>" class="next">&raquo;</a>
                        <?php else: ?>
                            <span class="next disabled">&raquo;</span>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <p class="no-data">Aucun appareil trouvé.</p>
                <?php endif; ?>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
