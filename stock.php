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

// Gestion de la recherche
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = " WHERE n_serie LIKE '%$search%' OR appareil LIKE '%$search%' OR marque LIKE '%$search%' OR model LIKE '%$search%' OR organisation LIKE '%$search%'";
}



// Mise à jour des informations si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $original_n_serie = $_POST['original_n_serie']; // Get the original n_serie
    $appareil = $_POST['appareil'];
    if ($appareil == "autre") {
        $appareil = $_POST['autre_appareil'];
    }
    $organisation = $_POST['organisation'];
    $status = $_POST['status'];
    $marque = $_POST['marque'];
    $model = $_POST['model'];
    $n_serie = $_POST['n_serie']; // Get the updated n_serie
    $date_achat = $_POST['date_achat'];
    $date_mise_production = $_POST['date_mise_production'];
    $contact = $_POST['contact'];
    
    $document = $_POST['current_document'];
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $document = basename($_FILES['document']['name']);
        $target_dir = "uploads/";
        
        // Ensure the uploads directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $document;
        if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
            // File uploaded successfully
        } else {
            echo "Erreur lors de l'upload du fichier.";
        }
    }

    // Fetch the existing record before updating
    $sql_old = "SELECT * FROM stock WHERE n_serie='$original_n_serie'";
    $result_old = $conn->query($sql_old);
    $old_data = $result_old->fetch_assoc();

    // Detect changes
    $changes = [];
    if ($old_data['appareil'] != $appareil) {
        $changes[] = "Appareil: " . $old_data['appareil'] . " -> " . $appareil;
    }
    if ($old_data['organisation'] != $organisation) {
        $changes[] = "Organisation: " . $old_data['organisation'] . " -> " . $organisation;
    }
    if ($old_data['status'] != $status) {
        $changes[] = "Status: " . $old_data['status'] . " -> " . $status;
    }
    if ($old_data['marque'] != $marque) {
        $changes[] = "Marque: " . $old_data['marque'] . " -> " . $marque;
    }
    if ($old_data['model'] != $model) {
        $changes[] = "Model: " . $old_data['model'] . " -> " . $model;
    }
    if ($old_data['n_serie'] != $n_serie) {
        $changes[] = "Numéro de Série: " . $old_data['n_serie'] . " -> " . $n_serie;
    }
    if ($old_data['date_achat'] != $date_achat) {
        $changes[] = "Date d'Achat: " . $old_data['date_achat'] . " -> " . $date_achat;
    }
    if ($old_data['date_mise_production'] != $date_mise_production) {
        $changes[] = "Date de Mise en Production: " . $old_data['date_mise_production'] . " -> " . $date_mise_production;
    }
    if ($old_data['contact'] != $contact) {
        $changes[] = "Contact: " . $old_data['contact'] . " -> " . $contact;
    }
    if ($old_data['document'] != $document) {
        $changes[] = "Document: " . $old_data['document'] . " -> " . $document;
    }

    $change_details = implode(", ", $changes);

    // Log changes if any
    if (!empty($change_details)) {
        $utilisateur = $_SESSION['username']; // Assuming username is stored in session
        $sql_log = "INSERT INTO historique (utilisateur, n_serie, changement) VALUES ('$utilisateur', '$original_n_serie', '$change_details')";
        $conn->query($sql_log);
    }

    // Update the record in the stock table
    $sql = "UPDATE stock SET 
                appareil='$appareil',
                organisation='$organisation',
                status='$status',
                marque='$marque',
                model='$model',
                n_serie='$n_serie', 
                date_achat='$date_achat',
                date_mise_production='$date_mise_production',
                contact='$contact',
                document='$document'
            WHERE n_serie='$original_n_serie'";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='success-message'>Les informations ont été mises à jour avec succès.</div>";
    } else {
        $message = "<div class='error-message'>Erreur lors de la mise à jour : " . $conn->error . "</div>";
    }
}

// Pagination logic
$items_per_page = 5; // Changed from 10 to 5 to show 5 items per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $items_per_page; // Offset for the SQL query

// Count total items for pagination
$total_items_sql = "SELECT COUNT(*) as total FROM stock" . $search_query;
$total_items_result = $conn->query($total_items_sql);
$total_items_row = $total_items_result->fetch_assoc();
$total_items = $total_items_row['total'];

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

// Fetch data for the current page
$sql = "SELECT appareil, organisation, status, marque, model, n_serie, date_achat, date_mise_production, contact, document 
        FROM stock" . $search_query . " 
        ORDER BY stock.status ASC 
        LIMIT $items_per_page OFFSET $offset";



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
    <title>Gestion de Stock</title>
    <style>
        /* Styles de base */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --border-color: #bdc3c7;
            --input-bg: #fff;
            --input-border: #dcdde1;
            --input-focus: #74b9ff;
            --success-bg: #2ecc71;
            --success-text: #ffffff;
            --error-bg: #e74c3c;
            --error-text: #ffffff;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Style du message de succès et d'erreur */
        .success-message, .error-message {
            margin: 20px;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            display: block;
            text-align: center;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }

        .success-message.visible, .error-message.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .success-message {
            background-color: var(--success-bg);
            color: var(--success-text);
        }

        .error-message {
            background-color: var(--error-bg);
            color: var(--error-text);
        }

        /* Style de la table */
        .table-container {
            margin: 60px 20px 20px 20px;
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
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 10;
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

        /* Style des champs de saisie */
        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--input-border);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 2px rgba(116, 185, 255, 0.2);
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%2334495e" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 30px;
        }

        /* Style pour les lignes alternées */
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Effet de survol sur les lignes */
        tr:hover {
            background-color: #e8f4f8;
            transition: background-color 0.3s ease;
        }

        /* Style pour les boutons d'action */
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
            background-color: var(--primary-color);
            color: white;
        }

        .save-btn {
            background-color: var(--secondary-color);
            color: white;
            display: none;
        }

        .download-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .edit-btn:hover, .save-btn:hover, .download-btn:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        /* Conteneur pour les icônes d'action */
        td .action-icons {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Responsive design */
        @media screen and (max-width: 1200px) {
            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 1000px;
            }
        }
        h1 {
  font-family: 'Roboto', sans-serif;
  font-size: 3rem;
  font-weight: 300;
  color: #333;
  text-align: center;
  padding: 20px;
  margin-bottom: 30px;
  background: linear-gradient(135deg, #f6f8fa 0%, #e9f0f5 100%);
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

h1:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="dashboard.php" class="logo_fr">
            <br></br>
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
            <form action="stock.php" method="get">
                <div class="form-input">
                    <input type="search" name="search" placeholder="Rechercher par N° de série..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <?php if(isset($message)): ?>
                <?php echo $message; ?>
            <?php endif; ?>
            <h1>Modification Stock</h1 >
            <div class="table-container">
                <!--  <h2>Liste des Appareils</h2>-->
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
                                <th>Date de réception</th>
                                <th>Date de Mise en Production</th>
                                <th>Contact</th>
                                <th>Document</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="POST" action="stock.php" class="edit-form" enctype="multipart/form-data">
                <!-- Store the original n_serie -->
                <input type="hidden" name="original_n_serie" value="<?php echo htmlspecialchars($row['n_serie']); ?>">
                
                <td>
                    <select name="appareil" disabled onchange="toggleAutreAppareilInput(this)">
                        <option value="PC" <?php echo ($row['appareil'] == 'PC') ? 'selected' : ''; ?>>PC</option>
                        <option value="Imprimante" <?php echo ($row['appareil'] == 'Imprimante') ? 'selected' : ''; ?>>Imprimante</option>
                        <option value="Écran" <?php echo ($row['appareil'] == 'Écran') ? 'selected' : ''; ?>>Écran</option>
                        <option value="autre" <?php echo ($row['appareil'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                    <div id="autre_appareil_input" style="<?php echo ($row['appareil'] != 'autre') ? 'display:none;' : ''; ?>">
                        <input type="text" name="autre_appareil" value="<?php echo htmlspecialchars($row['appareil']); ?>" placeholder="Autre Appareil">
                    </div>
                </td>
                <td>
                    <input type="text" name="organisation" value="<?php echo htmlspecialchars($row['organisation']); ?>">
                </td>
                <td>
                    <select name="status" disabled>
                        <option value="stock" <?php echo ($row['status'] == 'stock') ? 'selected' : ''; ?>>Stock</option>
                        <option value="production" <?php echo ($row['status'] == 'production') ? 'selected' : ''; ?>>Production</option>
                        <option value="reforme" <?php echo ($row['status'] == 'reforme') ? 'selected' : ''; ?>>Réforme</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="marque" value="<?php echo htmlspecialchars($row['marque']); ?>">
                </td>
                <td>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($row['model']); ?>">
                </td>
                <td>
                    <input type="text" name="n_serie" value="<?php echo htmlspecialchars($row['n_serie']); ?>">
                </td>
                <td>
                    <input type="date" name="date_achat" value="<?php echo htmlspecialchars($row['date_achat']); ?>">
                </td>
                <td>
                    <input type="date" name="date_mise_production" value="<?php echo htmlspecialchars($row['date_mise_production']); ?>">
                </td>
                <td>
                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>">
                </td>
                <td>
                    <input type="text" name="current_document" value="<?php echo htmlspecialchars($row['document']); ?>" readonly>
                    <input type="file" name="document" style="display:none;">
                </td>
                <td>
                    <div class="action-icons">
                    <button type="button" class="edit-btn" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="submit" class="save-btn" title="Enregistrer" style="display: none;">
                        <i class="fas fa-save"></i>
                    </button>
                    <?php if ($row['document']): ?>
                        <a href="uploads/<?php echo htmlspecialchars($row['document']); ?>" class="download-btn" title="Télécharger" download>
                            <i class="fas fa-download"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
</tbody>

                    </table>

                                  <!-- Pagination links -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="stock.php?page=<?php echo $page - 1; ?>" class="prev">&laquo;</a>
                        <?php else: ?>
                            <span class="prev disabled">&laquo;</span>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="stock.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="stock.php?page=<?php echo $page + 1; ?>" class="next">&raquo;</a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.success-message');
            const errorMessage = document.querySelector('.error-message');

            if (successMessage || errorMessage) {
                const message = successMessage || errorMessage;
                
                // Show the message
                message.classList.add('visible');

                // Hide the message after 5 seconds
                setTimeout(() => {
                    message.classList.remove('visible');
                }, 5000); // Adjust the time as necessary
            }

            // Handling the edit and save button actions
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    row.querySelectorAll('input').forEach(input => {
                        input.removeAttribute('readonly');
                    });
                    row.querySelectorAll('select').forEach(select => {
                        select.removeAttribute('disabled');
                    });
                    row.querySelector('[type="file"]').style.display = 'block';
                    row.querySelector('.edit-btn').style.display = 'none';
                    row.querySelector('.save-btn').style.display = 'inline-block';
                });
            });

            // Toggle visibility of "Autre Appareil" input field
            document.querySelectorAll('select[name="appareil"]').forEach(select => {
                select.addEventListener('change', function() {
                    const autreAppareilInput = this.closest('td').querySelector('#autre_appareil_input');
                    if (this.value === "autre") {
                        autreAppareilInput.style.display = "block";
                    } else {
                        autreAppareilInput.style.display = "none";
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
