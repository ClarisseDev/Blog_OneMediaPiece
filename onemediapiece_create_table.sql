-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 14 sep. 2025 à 22:25
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `onemediapiece`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id_article` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(127) NOT NULL,
  `contenu` varchar(8191) NOT NULL,
  `dateCreation` datetime NOT NULL,
  `dateModification` datetime NOT NULL,
  `estPublic` bit(1) NOT NULL,
  `enAttenteDeModeration` bit(1) NOT NULL,
  `estSupprime` bit(1) NOT NULL,
  `fk_auteur` bigint(20) UNSIGNED NOT NULL,
  `fk_moderateur` bigint(20) UNSIGNED DEFAULT NULL,
  `dateModeration` datetime DEFAULT NULL,
  `motifModeration` varchar(127) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `titre`, `contenu`, `dateCreation`, `dateModification`, `estPublic`, `enAttenteDeModeration`, `estSupprime`, `fk_auteur`, `fk_moderateur`, `dateModeration`, `motifModeration`) VALUES
(1, 'Bienvenu', 'Welcome in my blog', '2024-10-21 15:46:27', '2024-10-21 15:46:27', b'1', b'0', b'0', 1, NULL, NULL, NULL),
(2, 'Article 2', 'Les hommes naissent et demeurent libres', '2024-10-21 15:47:47', '2024-10-21 15:47:47', b'1', b'0', b'0', 1, NULL, NULL, NULL),
(3, 'Article Privé', 'Y a rien à voir', '2024-10-21 15:50:12', '2024-10-21 15:50:12', b'0', b'0', b'0', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id_commentaire` bigint(20) UNSIGNED NOT NULL,
  `contenu` varchar(1023) NOT NULL,
  `dateCreation` datetime NOT NULL,
  `dateModification` datetime NOT NULL,
  `estSupprime` bit(1) NOT NULL,
  `estModere` bit(1) NOT NULL,
  `moderationDescription` varchar(127) DEFAULT NULL,
  `fk_article` bigint(20) UNSIGNED NOT NULL,
  `fk_auteur` bigint(20) UNSIGNED NOT NULL,
  `fk_moderePar` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

CREATE TABLE `compte` (
  `id_compte` bigint(20) UNSIGNED NOT NULL,
  `login` varchar(127) NOT NULL,
  `password` varchar(63) NOT NULL,
  `pseudo` varchar(31) NOT NULL,
  `dateCreation` datetime NOT NULL,
  `dateModification` datetime NOT NULL,
  `estSupprime` bit(1) NOT NULL,
  `estSignale` bit(1) NOT NULL,
  `estBanni` bit(1) NOT NULL,
  `enAttenteDeModeration` bit(1) NOT NULL,
  `fk_role` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`id_compte`, `login`, `password`, `pseudo`, `dateCreation`, `dateModification`, `estSupprime`, `estSignale`, `estBanni`, `enAttenteDeModeration`, `fk_role`) VALUES
(1, 'ced@bge.fr', 'azerty', 'cedric', '2024-10-21 15:43:18', '2024-10-21 15:43:18', b'0', b'0', b'0', b'0', 3),
(2, 'toto@test.com', 'AZERTY', 'toto', '2024-10-24 11:10:28', '2024-10-24 11:10:28', b'0', b'0', b'0', b'1', 1),
(20, 'toto@google.com', 'AZERTY', 'totooooo', '2024-10-25 09:10:45', '2024-10-25 09:10:45', b'0', b'0', b'0', b'1', 1),
(33, 'hash@google.com', '$2y$10$5QJWhA6tz7I.yc.hiICDieEWMCi4I8lK4HcLud3CXGvhtkUZz0VH6', 'hash', '2024-10-25 11:10:32', '2024-10-25 11:10:32', b'0', b'0', b'0', b'1', 1),
(47, 'hashddd@google.com', '$2y$10$DmJ2AVv6o6JwBsSBRq0KBOU.eL1kCFnA.li0JKCIahBZ/XXUMxKFO', 'hashsss', '2024-10-30 03:10:09', '2024-10-30 03:10:09', b'0', b'0', b'0', b'1', 1),
(50, 'hashmmmmm@google.com', '$2y$10$xxwar4UYIsA1aYFf0CICUuNSF9P8gqbRF59lJx7T/xsBSQOnBp2y2', 'hashmmmmm', '2024-10-30 04:10:43', '2024-10-30 04:10:43', b'0', b'0', b'0', b'1', 1),
(51, 'test@test.fr', 'test', 'test', '2025-09-14 17:34:45', '2025-09-14 17:34:45', b'0', b'0', b'0', b'0', 1);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `label`) VALUES
(3, 'Administrateur'),
(2, 'Modérateur'),
(1, 'Rédacteur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `fk_Article_Compte1_idx` (`fk_auteur`),
  ADD KEY `fk_Article_Compte2_idx` (`fk_moderateur`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id_commentaire`),
  ADD KEY `fk_Commentaire_Article1_idx` (`fk_article`),
  ADD KEY `fk_Commentaire_Compte1_idx` (`fk_auteur`),
  ADD KEY `fk_Commentaire_Compte2_idx` (`fk_moderePar`);

--
-- Index pour la table `compte`
--
ALTER TABLE `compte`
  ADD PRIMARY KEY (`id_compte`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`),
  ADD UNIQUE KEY `pseudo_UNIQUE` (`pseudo`),
  ADD KEY `fk_Compte_Role_idx` (`fk_role`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `label_UNIQUE` (`label`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id_article` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id_commentaire` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `compte`
--
ALTER TABLE `compte`
  MODIFY `id_compte` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `fk_Article_Compte1` FOREIGN KEY (`fk_auteur`) REFERENCES `compte` (`id_compte`),
  ADD CONSTRAINT `fk_Article_Compte2` FOREIGN KEY (`fk_moderateur`) REFERENCES `compte` (`id_compte`);

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `fk_Commentaire_Article1` FOREIGN KEY (`fk_article`) REFERENCES `article` (`id_article`),
  ADD CONSTRAINT `fk_Commentaire_Compte1` FOREIGN KEY (`fk_auteur`) REFERENCES `compte` (`id_compte`),
  ADD CONSTRAINT `fk_Commentaire_Compte2` FOREIGN KEY (`fk_moderePar`) REFERENCES `compte` (`id_compte`);

--
-- Contraintes pour la table `compte`
--
ALTER TABLE `compte`
  ADD CONSTRAINT `fk_Compte_Role` FOREIGN KEY (`fk_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
