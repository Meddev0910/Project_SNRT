-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 23 août 2024 à 05:00
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
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `champ_modifie` varchar(50) DEFAULT NULL,
  `ancienne_valeur` text DEFAULT NULL,
  `nouvelle_valeur` text DEFAULT NULL,
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique`
--

INSERT INTO `historique` (`id`, `stock_id`, `champ_modifie`, `ancienne_valeur`, `nouvelle_valeur`, `date_modification`) VALUES
(1, NULL, 'appareil', NULL, 'PC', '2024-08-23 00:58:54'),
(2, NULL, 'organisation', NULL, 'Org 1', '2024-08-23 00:58:54'),
(3, NULL, 'status', NULL, 'stock', '2024-08-23 00:58:54'),
(4, NULL, 'marque', NULL, 'Dell', '2024-08-23 00:58:54'),
(5, NULL, 'model', NULL, 'XPS 15', '2024-08-23 00:58:54'),
(6, NULL, 'n_serie', NULL, 'vvvv', '2024-08-23 00:58:54'),
(7, NULL, 'date_achat', NULL, '2022-01-10', '2024-08-23 00:58:54'),
(8, NULL, 'date_mise_production', NULL, '2022-01-15', '2024-08-23 00:58:54'),
(9, NULL, 'document', NULL, 'sss.pdf', '2024-08-23 00:58:54');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `contact` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `appareil`, `organisation`, `status`, `marque`, `model`, `n_serie`, `date_achat`, `date_mise_production`, `created_at`, `contact`, `document`) VALUES
(3, 'PC', 'Org 1', 'Production', 'Dell', 'XPS 15', 'ccc', '2022-01-10', '2022-01-15', '2024-08-17 02:04:32', '', 'sss.pdf'),
(85, 'Imprimante', 'Org B', 'reforme', 'HP', 'vvv', 'm', '2021-07-15', '2021-08-01', '2024-08-17 03:34:54', '', ''),
(99, 'PC', 'Org A', 'production', 'Dell', 'XPS 15', 'SN1', '2022-01-10', '2022-01-15', '2024-08-22 15:57:40', NULL, NULL),
(100, 'Écran', 'Org A', 'réforme', 'Samsung', 'U28R55', 'SN5', '2020-03-20', '2020-04-01', '2024-08-22 15:57:40', NULL, NULL),
(101, 'PC', 'Org C', 'production', 'Lenovo', 'ThinkPad X1', 'SN9', '2023-02-05', '2023-03-01', '2024-08-22 15:57:40', NULL, NULL),
(102, 'Imprimante', 'Org B', 'stock', 'Brother', 'HL-L2350DW', 'SN4', '2022-11-22', '2022-12-01', '2024-08-22 15:57:40', '', ''),
(103, 'PC', 'xxx', 'Production', 'xxxxx', 'xxxx', 'xxxxx', '2024-08-04', '2024-08-02', '2024-08-22 23:42:40', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','subadmin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'Snrt', '$2y$10$6YUJpGwt.2QpspLpi7U7sOS.ikm5OzN4.z8f9b0oYcIBcS6jTw.MG', '2024-08-17 01:41:31', 'admin'),
(3, 'Mohamed', '$2y$10$MRek77SnHT9IGPJuFBuVYO84UhntvPF2ATPQtwLT1CLT.JLYO1HSi', '2024-08-21 22:14:55', 'subadmin'),
(20, 'kamal', '$2y$10$CVX7DkQ1ud9QrJvcLBqnJeXJtqxDV3KMTrJt1kFr7ZqrGhNMQJVVm', '2024-08-21 23:07:44', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `n_serie` (`n_serie`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
