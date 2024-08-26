<?php
$current_page = 'gestion_stock';
session_start();

if (!isset($_SESSION['loggedin'])) {
    header("Location: login_page.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
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


        .dashboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 20px;
        }
        .tile {
            border-radius: 5px;
            padding: 20px;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 150px;
        }
        .tile-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .tile-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .tile-content {
            font-size: 14px;
        }
        .ajouter { background-color: #673AB7; }
        .listes { background-color: #2196F3; }
        .importer { background-color: #4CAF50; }
        .exporter { background-color: #FF9800; }
        .reforme { background-color: #E91E63; }
        .historique { background-color: #03A9F4; }
        .prise-en-charge { background-color: #FF5722; }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
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
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
                </div>
            </form>

        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
      
        <main>
        <h1>Gestion de Stock</h1 >
        <div class="dashboard">
        <a href="ajouter_apparaille.php">
            <div class="tile ajouter">
                <div class="tile-icon"><i class='bx bx-plus-circle'></i></div>
                <div class="tile-title">Ajouter Appareil</div>
                <div class="tile-content">Ajouter un nouvel appareil au stock</div>
            </div>
        </a>
        <a href="Liste_appareil.php">
            <div class="tile listes">
                <div class="tile-icon"><i class='bx bx-list-ul'></i></div>
                <div class="tile-title">Listes des Appareils</div>
                <div class="tile-content">Voir tous les appareils en stock</div>
            </div>
        </a>
        <a href="import_excel.php">
            <div class="tile importer">
                <div class="tile-icon"><i class='bx bx-import'></i></div>
                <div class="tile-title">Importer Excel</div>
                <div class="tile-content">Importer des données depuis Excel</div>
            </div>
        </a>
        <a href="export_excel.php">
            <div class="tile exporter">
                <div class="tile-icon"><i class='bx bx-export'></i></div>
                <div class="tile-title">Exporter Excel</div>
                <div class="tile-content">Exporter les données vers Excel</div>
            </div>
        </a>
        <a href="Reforme.php">
            <div class="tile reforme">
                <div class="tile-icon"><i class='bx bx-list-ul'></i></div>
                <div class="tile-title">Listes des Réformes</div>
                <div class="tile-content">Imprimer les appareils réformés</div>
            </div>
        </a>
        <a href="historique.php">
            <div class="tile historique">
                <div class="tile-icon"><i class='bx bx-history'></i></div>
                <div class="tile-title">Historique</div>
                <div class="tile-content">Consulter l'historique des opérations</div>
            </div>
        </a>
        <a href="prise_charge.php">
            <div class="tile prise-en-charge">
                <div class="tile-icon"><i class='bx bx-check-circle'></i></div>
                <div class="tile-title">Prise en charge</div>
                <div class="tile-content">Gérer les prises en charge</div>
            </div>
        </div>
        </a>
    </main>


       
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="script.js"></script>
</body>
</html>
