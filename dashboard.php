<?php
$current_page = 'dashboard';
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

// Initialisation des comptes
$counts = [
    'stock' => [
        'pc' => 0,
        'imprimante' => 0,
        'écran' => 0
    ],
    'production' => [
        'pc' => 0,
        'imprimante' => 0,
        'écran' => 0
    ],
    'reforme' => [
        'pc' => 0,
        'imprimante' => 0,
        'écran' => 0
    ]
];

// Requête SQL pour compter les appareils par type et statut
$sql = "SELECT LOWER(appareil) AS type, LOWER(status) AS status, COUNT(*) as count 
        FROM stock 
        GROUP BY type, status";
$result = $conn->query($sql);

if ($result === false) {
    die("Erreur dans la requête SQL: " . $conn->error);
}

// Traitement des résultats de la requête SQL
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $type = $row['type'];
        $status = $row['status'];
        $count = $row['count'];

        if (isset($counts[$status][$type])) {
            $counts[$status][$type] = $count;
        }
    }
}

// Gestion de la recherche
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = " WHERE n_serie LIKE '%$search%' OR appareil LIKE '%$search%' OR marque LIKE '%$search%' OR model LIKE '%$search%' OR organisation LIKE '%$search%'";
}

// Récupérer les données avec ou sans recherche
$sql = "SELECT appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production, contact, document FROM stock" . $search_query;
$result = $conn->query($sql);
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
    <title>Dashboard</title>
    
    <style>
    /* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-color, #f4f4f4);
    color: var(--text-color, #333);
    margin: 0;
    padding: 0;
}

.sidebar {
    background-color: var(--primary-color, #ffffff);
    padding: 20px;
}

.navbar {
    background-color: var(--primary-color, #ffffff);
    padding: 10px;
}

.main-content {
    background-color: var(--background-color, #f4f4f4);
    padding: 20px;
}

.box-info {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Three columns per row */
    gap: 20px;
    padding: 20px;
    margin-top: 90px;
    max-width: 1200px;
}

.box-info li {
    background-color: var(--primary-color, #ffffff);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    overflow: hidden;
    position: relative;
}

.box-info li:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

.box-info li i {
    font-size: 2.5rem;
    margin-right: 20px;
    padding: 15px;
    border-radius: 10px;
    flex-shrink: 0;
    background-color: var(--hover-color, rgba(0, 0, 0, 0.05));
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s ease;
}

.box-info li:nth-child(3n+1) i {
    background-color: #d9e8fb;
    color: #3b82f6;
}

.box-info li:nth-child(3n+2) i {
    background-color: #fff7e0;
    color: #f59e0b;
}

.box-info li:nth-child(3n+3) i {
    background-color: #fde4e1;
    color: #ef4444;
}

.box-info li .text h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: var(--text-color, #333);
    transition: transform 0.3s ease;
}

.box-info li .text p {
    margin: 0;
    color: var(--secondary-color, #666);
    font-size: 1rem;
}

/* Animation for entering the view */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.box-info li {
    animation: fadeInUp 0.5s ease forwards;
}

/* Responsive Grid Adjustments */
@media (max-width: 1024px) {
    .box-info {
        grid-template-columns: repeat(2, 1fr); /* Two columns per row on medium screens */
    }
}

@media (max-width: 768px) {
    .box-info {
        grid-template-columns: 1fr; /* One column per row on small screens */
    }

    .box-info li i {
        font-size: 2rem;
        margin-right: 10px;
    }

    .box-info li .text h3 {
        font-size: 1.5rem;
    }

    .box-info li .text p {
        font-size: 0.875rem;
    }
}

/* Dark Mode Styles */
body.dark-mode {
    --background-color: #2c3e50;
    --text-color: #ecf0f1;
    --primary-color: #34495e;
    --secondary-color: #bdc3c7;
    --hover-color: rgba(255, 255, 255, 0.1);
}

.dark-mode body {
    background-color: var(--background-color);
    color: var(--text-color);
}

.dark-mode .sidebar {
    background-color: var(--primary-color);
}

.dark-mode .navbar, .dark-mode .footer {
    background-color: var(--primary-color);
    color: var(--text-color);
}

.dark-mode .main-content {
    background-color: var(--background-color);
    color: var(--text-color);
}

.dark-mode .box-info li {
    background-color: var(--primary-color);
    color: var(--text-color);
}

.dark-mode .search-btn {
    background-color: var(--hover-color);
    color: var(--text-color);
}

/* Table Styles */
.table-container {
    margin: 20px 0;
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid var(--border-color, #bdc3c7);
}

th {
    background-color: var(--primary-color, #3498db);
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    position: sticky;
    top: 0;
    z-index: 10;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #e8f4f8;
    transition: background-color 0.3s ease;
}

.edit-btn, .save-btn, .download-btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    width: 30px;
    height: 30px;
    line-height: 30px;
    margin: 0 2px;
    border-radius: 4px;
}

.edit-btn {
    background-color: var(--primary-color, #3498db);
    color: white;
}

.save-btn {
    background-color: var(--secondary-color, #2ecc71);
    color: white;
    display: none;
}

.download-btn {
    background-color: var(--primary-color, #3498db);
    color: white;
}

.edit-btn:hover, .save-btn:hover, .download-btn:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

td .action-icons {
    display: flex;
    align-items: center;
    justify-content: space-between;
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
            <i class='bx bx-menu' ></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="dashboard.php" method="get">
                <div class="form-input">
                    <input type="search" name="search" placeholder="Rechercher par N° de série..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
                </div>
            </form>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
        <?php if (empty($search_query)): ?>
        <ul class="box-info">
        <li>
            <i class="fa-solid fa-desktop"></i>
            <span class="text">
                <h3><?php echo $counts['stock']['pc']; ?></h3>
                <p>PC En Stock</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-tv"></i> 
            <span class="text">
                <h3><?php echo $counts['stock']['écran']; ?></h3>
                <p>Écran En Stock</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-print"></i>
            <span class="text">
                <h3><?php echo $counts['stock']['imprimante']; ?></h3>
                <p>Imprimante En Stock</p>
            </span>
        </li>

        <li>
            <i class="fa-solid fa-desktop"></i>
            <span class="text">
                <h3><?php echo $counts['production']['pc']; ?></h3>
                <p>PC En Production</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-tv"></i> 
            <span class="text">
                <h3><?php echo $counts['production']['écran']; ?></h3>
                <p>Écran En Production</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-print"></i>
            <span class="text">
                <h3><?php echo $counts['production']['imprimante']; ?></h3>
                <p>Imprimante En Production</p>
            </span>
        </li>
        
        <li>
            <i class="fa-solid fa-desktop"></i>
            <span class="text">
                <h3><?php echo $counts['reforme']['pc']; ?></h3>
                <p>PC En Reforme</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-tv"></i> 
            <span class="text">
                <h3><?php echo $counts['reforme']['écran']; ?></h3>
                <p>Écran En Reforme</p>
            </span>
        </li>
        <li>
            <i class="fa-solid fa-print"></i>
            <span class="text">
                <h3><?php echo $counts['reforme']['imprimante']; ?></h3>
                <p>Imprimante En Reforme</p>
            </span>
        </li>
        </ul>
        <?php endif; ?>

    <!-- Vérifier si une recherche est effectuée -->
    <?php if (!empty($search_query)): ?>
        <div class="table-container">
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
            <?php else: ?>
                <p class="no-data">Aucun appareil trouvé.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
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
