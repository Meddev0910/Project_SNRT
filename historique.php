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

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Initialize $search_query if it's not used elsewhere
$search_query = "";

// Pagination logic
$items_per_page = 5; // Changed from 10 to 5 to show 5 items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $items_per_page; // Offset for the SQL query

// Count total items for pagination
$total_items_sql = "SELECT COUNT(*) as total FROM historique" . $search_query;
$total_items_result = $conn->query($total_items_sql);
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

// Récupération de l'historique des modifications
$sql = "SELECT * FROM historique ORDER BY date_modification DESC LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Historique des Modifications</title>
    <style>
:root {
    --primary-color: #3498db;
    --secondary-color: #2c3e50;
    --background-color: #f4f6f9;
    --text-color: #34495e;
    --highlight-color: #00b8a9; /* Couleur de l'en-tête et du bouton */
    --border-radius: 12px;
    --box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem;
}

h1 {
    text-align: center;
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.table-container {
    width: 90%;
    max-width: 1200px;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
    transition: var(--transition);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

thead {
    background-color: var(--highlight-color);
    color: white;
}

th, td {
    padding: 1rem;
    text-align: left;
}

th {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 1rem;
}

tbody tr {
    background-color: rgba(52, 152, 219, 0.05);
}

tbody tr:nth-child(even) {
    background-color: rgba(52, 152, 219, 0.1);
}

tbody tr:hover {
    background-color: rgba(52, 152, 219, 0.2);
    transition: var(--transition);
}

td pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: 'Courier New', Courier, monospace;
    background-color: rgba(44, 62, 80, 0.05);
    padding: 0.5rem;
    border-radius: 4px;
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
    background-color:  #00b8a9;
    color: white;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.pagination a.active {
    background-color:  #00b8a9;
}

.pagination a:hover {
    background-color: #00b8a9;
}

.pagination .prev, .pagination .next {
    background-color: #f39c12;
    color: white;
}

.pagination .disabled {
    pointer-events: none;
    background-color: #ccc;
    color: #666;
}

.back-to-dashboard {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.back-to-dashboard button {
    background: linear-gradient(135deg, #00b8a9, #34c759); /* Dégradé moderne */
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Ombre portée moderne */
    transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.back-to-dashboard button:hover {
    background: linear-gradient(135deg, #34c759, #00b8a9); /* Inversion de gradient */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    transform: translateY(-4px);
}

.back-to-dashboard button:active {
    transform: translateY(2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}



    </style>
</head>
<body>
    <h1>Historique des Modifications</h1>
    <div class="table-container">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Numéro de Série</th>
                        <th>Changement</th>
                        <th>Date de Modification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($row['n_serie']); ?></td>
                            <td><pre><?php echo htmlspecialchars($row['changement']); ?></pre></td>
                            <td><?php echo htmlspecialchars($row['date_modification']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Pagination links -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="historique.php?page=<?php echo $page - 1; ?>" class="prev">&laquo;</a>
                <?php else: ?>
                    <span class="prev disabled">&laquo;</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="historique.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="historique.php?page=<?php echo $page + 1; ?>" class="next">&raquo;</a>
                <?php else: ?>
                    <span class="next disabled">&raquo;</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data">Aucune Modification Enregistrée.</p>
        <?php endif; ?>
    </div>

    <!-- Bouton de retour au tableau de bord -->
    <div class="back-to-dashboard">
        <button onclick="window.location.href='dashboard.php';">Retour au Tableau de Bord</button>
    </div>
</body>
</html>

<?php
$conn->close();
?>
