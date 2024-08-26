-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 août 2024 à 06:05
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `user_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `appareil` varchar(100) NOT NULL,
  `organisation` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `marque` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `n_serie` varchar(100) NOT NULL,
  `date_achat` date NOT NULL,
  `date_mise_production` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `appareil`, `organisation`, `status`, `marque`, `model`, `n_serie`, `date_achat`, `date_mise_production`, `created_at`) VALUES
(3, 'PC', 'Org A', 'production', 'Dell', 'XPS 15', 'SN12345', '2022-01-10', '2022-01-15', '2024-08-17 02:04:32'),
(4, 'Imprimante', 'Org B', 'Production', 'HP', 'LaserJet', 'SN67890', '2021-07-15', '2021-08-01', '2024-08-17 02:04:32'),
(5, 'Écran', 'Org A', 'réforme', 'Samsung', 'U28R55', 'SN54321', '2020-03-20', '2020-04-01', '2024-08-17 02:04:32'),
(6, 'PC', 'Org C', 'production', 'Lenovo', 'ThinkPad X1', 'SN98765', '2023-02-05', '2023-03-01', '2024-08-17 02:04:32'),
(63, 'appareil', 'organisation', 'status', 'marque', 'model', 'n_serie', '0000-00-00', '0000-00-00', '2024-08-17 03:18:21'),
(84, 'PC', 'Org A', 'Production', 'Dell', 'XPS 15', 'SN1', '2022-01-10', '2022-01-15', '2024-08-17 03:34:54'),
(85, 'Imprimante', 'Org B', 'Production', 'HP', 'LaserJet', 'SN6', '2021-07-15', '2021-08-01', '2024-08-17 03:34:54'),
(86, 'Écran', 'Org A', 'réforme', 'Samsung', 'U28R55', 'SN5', '2020-03-20', '2020-04-01', '2024-08-17 03:34:54'),
(87, 'PC', 'Org C', 'Production', 'Lenovo', 'ThinkPad X1', 'SN9', '2023-02-05', '2023-03-01', '2024-08-17 03:34:54'),
(88, 'Imprimante', 'Org B', 'Production', 'Brother', 'HL-L2350DW', 'SN4', '2022-11-22', '2022-12-01', '2024-08-17 03:34:54'),
(89, 'PC', 'Org A', 'Production', 'Dell', 'XPS 15', 'S1', '2022-01-10', '2022-01-15', '2024-08-17 03:41:54'),
(90, 'Imprimante', 'Org B', 'Production', 'HP', 'LaserJet', 'S6', '2021-07-15', '2021-08-01', '2024-08-17 03:41:54'),
(91, 'Écran', 'Org A', 'réforme', 'Samsung', 'U28R55', 'S5', '2020-03-20', '2020-04-01', '2024-08-17 03:41:54'),
(92, 'PC', 'Org C', 'production', 'Lenovo', 'ThinkPad X1', 'S9', '2023-02-05', '2023-03-01', '2024-08-17 03:41:54'),
(93, 'Imprimante', 'Org B', 'Production', 'Brother', 'HL-L2350DW', 'S4', '2022-11-22', '2022-12-01', '2024-08-17 03:41:54'),
(94, 'PC', 'ismagi', 'reforme', 'hp', 'AA112', '19', '2024-08-02', '2024-07-30', '2024-08-17 04:37:58'),
(95, 'PC', 'zz', 'Production', 'hp', 'zzz', 'zzzzzzzzz', '2024-08-01', '2024-09-05', '2024-08-17 15:40:01'),
(96, 'PC', 'ismagi', 'Production', 'ASUS', 'AA1aa', 'aaa1', '2024-08-03', '2024-08-30', '2024-08-17 16:57:11'),
(97, 'Imprimante', 'ismagi', 'Production', 'ASUS', 'AVCa', 'a1acv', '2024-09-07', '2024-09-28', '2024-08-17 16:57:34');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `n_serie` (`n_serie`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
