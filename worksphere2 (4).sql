-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 02:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `worksphere2`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL,
  `id_candidat` int(11) NOT NULL,
  `id_offre` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `id_candidat`, `id_offre`, `date_created`) VALUES
(43, 32, 19, '2025-03-06 12:12:58'),
(44, 35, 22, '2025-03-28 16:53:15'),
(48, 13, 26, '2025-04-04 20:39:37'),
(55, 46, 52, '2025-05-12 22:09:13');

-- --------------------------------------------------------

--
-- Table structure for table `candidature`
--

CREATE TABLE `candidature` (
  `id_candidature` int(11) NOT NULL,
  `id_offre` int(11) NOT NULL,
  `id_candidat` int(11) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `lettre_motivation` varchar(255) NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidature`
--

INSERT INTO `candidature` (`id_candidature`, `id_offre`, `id_candidat`, `cv`, `status`, `lettre_motivation`, `updated_at`) VALUES
(160, 52, 46, 'CV.pdf', NULL, 'CV.pdf', NULL),
(161, 19, 46, '1747093309092-Modèle du CV.pdf', NULL, '1747093309095-LettreMotivation.pdf', NULL),
(162, 48, 46, '1747093605451-Modèle du CV.pdf', NULL, '1747093605455-LettreMotivation.pdf', NULL),
(163, 22, 46, '1747093677277-Modèle du CV.pdf', NULL, '1747093677277-LettreMotivation.pdf', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cours`
--

CREATE TABLE `cours` (
  `id_c` int(11) NOT NULL,
  `nom_c` varchar(255) DEFAULT NULL,
  `description_c` varchar(255) DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `id_f` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cours`
--

INSERT INTO `cours` (`id_c`, `nom_c`, `description_c`, `heure_debut`, `heure_fin`, `photo`, `date`, `id_f`) VALUES
(8, 'Flutter', 'Un framework de développement mobile cross plateform', '18:00:00', '20:00:00', 'img/67fe9bea203c6.jpg', '2025-04-23', 31),
(9, 'Android', 'un framawork de développement mobile', '18:00:00', '20:00:00', 'img/67fe9c252eb3e.jpg', '2025-04-25', 31),
(10, 'IPnet routing', 'un cours qui contient les notions de bases du routage', '19:00:00', '21:00:00', 'img/67fe9e239961b.jpg', '2025-05-02', 32);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entretiens`
--

CREATE TABLE `entretiens` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_entretien` date NOT NULL,
  `heure_entretien` time NOT NULL,
  `type_entretien` enum('EN_PRESENTIEL','EN_VISIO') NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `employe_id` int(11) DEFAULT NULL,
  `feedbackId` int(11) DEFAULT NULL,
  `candidatId` int(11) DEFAULT NULL,
  `idOffre` int(11) DEFAULT NULL,
  `idCandidature` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `nom_equipe` varchar(255) NOT NULL,
  `imageEquipe` varchar(255) DEFAULT NULL,
  `nbrProjet` int(11) DEFAULT 0,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipe`
--

INSERT INTO `equipe` (`id`, `nom_equipe`, `imageEquipe`, `nbrProjet`, `id_user`) VALUES
(58, 'equipe dev', 'img/68112cb74c8a0.jpg', 2, 27),
(59, 'scrum', 'img/68112d7e20429.jpg', 1, 27),
(60, 'reseaux', 'img/68112d9dd7b53.jpg', 2, 27);

-- --------------------------------------------------------

--
-- Table structure for table `equipe_employee`
--

CREATE TABLE `equipe_employee` (
  `equipe_id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipe_employee`
--

INSERT INTO `equipe_employee` (`equipe_id`, `id_user`) VALUES
(58, 13),
(58, 38),
(58, 39),
(59, 38),
(59, 39),
(59, 46),
(60, 38),
(60, 39),
(60, 46);

-- --------------------------------------------------------

--
-- Table structure for table `equipe_projet`
--

CREATE TABLE `equipe_projet` (
  `equipe_id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipe_projet`
--

INSERT INTO `equipe_projet` (`equipe_id`, `projet_id`) VALUES
(58, 38),
(59, 38),
(60, 38),
(60, 39);

-- --------------------------------------------------------

--
-- Table structure for table `evenement_sponsor`
--

CREATE TABLE `evenement_sponsor` (
  `evenement_id` int(11) NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `datedebutContrat` date DEFAULT NULL,
  `duree` enum('troisMois','sixMois','unAns') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evenement_sponsor`
--

INSERT INTO `evenement_sponsor` (`evenement_id`, `sponsor_id`, `datedebutContrat`, `duree`) VALUES
(5, 51, '2025-03-21', 'sixMois'),
(5, 53, '2025-03-14', 'sixMois');

-- --------------------------------------------------------

--
-- Table structure for table `evennement`
--

CREATE TABLE `evennement` (
  `idEvent` int(11) NOT NULL,
  `nomEvent` varchar(255) DEFAULT NULL,
  `descEvent` text DEFAULT NULL,
  `dateEvent` datetime DEFAULT NULL,
  `lieuEvent` varchar(255) DEFAULT NULL,
  `capaciteEvent` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `typeEvent` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evennement`
--

INSERT INTO `evennement` (`idEvent`, `nomEvent`, `descEvent`, `dateEvent`, `lieuEvent`, `capaciteEvent`, `id_user`, `typeEvent`) VALUES
(4, 'Christmas', 'evennement de xmas fi lac', '2025-03-21 10:10:10', 'lac 2 , tunis', 30, 28, ''),
(5, 'Team building', 'formation pour tous les jours', '2025-03-26 11:00:00', 'Marsa , Tunis', 20, 28, '');

-- --------------------------------------------------------

--
-- Table structure for table `favoris`
--

CREATE TABLE `favoris` (
  `id_favori` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_f` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favoris`
--

INSERT INTO `favoris` (`id_favori`, `id_user`, `id_f`) VALUES
(15, 39, 32);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `rate` int(11) NOT NULL CHECK (`rate` between 1 and 5),
  `date_feedback` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `message`, `rate`, `date_feedback`) VALUES
(47, 'hgghkjhkjj', 3, '2025-04-16 07:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `formation`
--

CREATE TABLE `formation` (
  `id_f` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `nb_place` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `langue` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `certifie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `formation`
--

INSERT INTO `formation` (`id_f`, `titre`, `description`, `nb_place`, `type`, `id_user`, `photo`, `langue`, `date`, `certifie`) VALUES
(31, 'Développement mobile', 'Une formation sur le développement mobile', 30, 'distanciel', NULL, 'img/67fe9b955b2c5.jpg', 'Anglais', '2025-04-23', 'oui'),
(32, 'Réseau informatique', 'Connaitre les notions de bases d\'un réseau informatique', 40, 'présentiel', NULL, 'img/67fe9e1661b2a.jpg', 'Français', '2025-05-01', 'oui'),
(31, 'Développement mobile', 'Une formation sur le développement mobile', 30, 'distanciel', NULL, 'img/67fe9b955b2c5.jpg', 'Anglais', '2025-04-23', 'oui'),
(32, 'Réseau informatique', 'Connaitre les notions de bases d\'un réseau informatique', 40, 'présentiel', NULL, 'img/67fe9e1661b2a.jpg', 'Français', '2025-05-01', 'oui');

-- --------------------------------------------------------

--
-- Table structure for table `historique_entretien`
--

CREATE TABLE `historique_entretien` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_entretien` date DEFAULT NULL,
  `heure_entretien` time DEFAULT NULL,
  `type_entretien` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `date_action` timestamp NOT NULL DEFAULT current_timestamp(),
  `employe_id` int(11) DEFAULT NULL,
  `feedbackId` int(11) DEFAULT NULL,
  `candidatId` int(11) DEFAULT NULL,
  `idOffre` int(11) DEFAULT NULL,
  `idCandidature` int(11) DEFAULT NULL,
  `entretien_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historique_entretien`
--

INSERT INTO `historique_entretien` (`id`, `titre`, `description`, `date_entretien`, `heure_entretien`, `type_entretien`, `status`, `action`, `date_action`, `employe_id`, `feedbackId`, `candidatId`, `idOffre`, `idCandidature`, `entretien_id`) VALUES
(34, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 02:53:47', 13, 0, 4, 4, 3, 2),
(35, 'fsddfdg', 'dgdgfdfhfhfdhf', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 02:54:01', 13, 0, 4, 4, 0, 0),
(36, 'fsddfdgh', 'dgdgfdfhfhfdhf', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'modification', '2025-03-06 02:58:30', 13, 0, 4, 4, 3, 56),
(37, 'hhhhhhhhhhhhhhh', 'dgdgfdfhfhfdhf', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'modification', '2025-03-06 02:58:36', 13, 0, 4, 4, 3, 56),
(38, 'hhhhhhhhhhhhhhh', 'dgdgfdfhfhfdhf', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 03:01:41', 13, 0, 4, 4, 3, 56),
(39, 'khkhhk', 'khhkhkhkhkkhkh', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 03:01:58', 13, 0, 4, 4, 0, 0),
(40, 'khkhhk', 'khhkhkhkhkkhkh', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 03:05:52', 13, 0, 4, 4, 3, 57),
(41, 'fhffhhf', 'fhfhfhfhhffhfh', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 03:06:07', 38, 0, 4, 4, 0, 0),
(42, 'fhffhhf', 'fhfhfhfhhffhfh', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 1, 'suppression', '2025-03-06 05:21:34', 38, 20, 4, 4, 3, 58),
(43, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:21:42', 13, 0, 4, 4, 3, 0),
(44, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:21:48', 13, 0, 4, 4, 3, 0),
(45, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:22:57', 13, 0, 4, 4, 3, 60),
(46, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:23:00', 13, 0, 4, 4, 3, 59),
(47, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:23:11', 13, 0, 4, 4, 3, 0),
(48, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:23:19', 13, 0, 4, 4, 3, 61),
(49, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:23:23', 13, 0, 4, 4, 3, 0),
(50, 'entretien spring boot ', 'maitriser les technologie de java ', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:24:41', 13, 0, 36, 4, 0, 0),
(51, 'entretien spring boot ', 'maitriser les technologie de java ', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:26:50', 13, 0, 36, 4, 37, 63),
(52, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:26:52', 13, 0, 4, 4, 3, 62),
(53, 'entretien spring boot ', 'maitriser les technologie de java ', '2025-03-06', '10:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:28:18', 13, 0, 4, 4, 0, 0),
(54, 'gdgddg', 'dggddggddgdgdgdg', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:28:51', 13, 0, 36, 4, 0, 0),
(55, 'gdgddg', 'dggddggddgdgdgdg', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:30:20', 13, 0, 36, 4, 37, 65),
(56, 'entretien spring boot ', 'maitriser les technologie de java ', '2025-03-06', '10:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 05:30:22', 13, 0, 4, 4, 3, 64),
(57, 'dgdgdgdg', 'dgdgdgdgdgdg', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 05:30:33', 38, 0, 4, 4, 0, 0),
(58, 'azeazeazeazeza', 'dgdgdgdgdgdg', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 1, 'modification', '2025-03-06 06:37:45', 38, 23, 4, 4, 3, 66),
(59, 'azeazeazeazeza', 'dgdgdgdgdgdg', '2025-03-07', '10:00:00', 'EN_PRESENTIEL', 1, 'suppression', '2025-03-06 06:37:51', 38, 23, 4, 4, 3, 66),
(60, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 06:37:59', 13, 0, 4, 4, 3, 0),
(61, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 06:38:01', 13, 0, 4, 4, 3, 0),
(62, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 06:38:01', 13, 0, 4, 4, 3, 0),
(63, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 06:38:02', 13, 0, 4, 4, 3, 0),
(64, 'tester', 'azeazeazeazeazeazeazeazea', '2025-03-18', '15:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 06:39:29', 13, 0, 36, 4, 0, 0),
(65, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 10:46:53', 13, 0, 4, 4, 3, 67),
(66, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 10:47:00', 13, 0, 4, 4, 3, 68),
(67, 'tester', 'azeazeazeazeazeazeazeazea', '2025-03-18', '15:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 10:47:03', 13, 0, 36, 4, 37, 71),
(68, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 10:47:05', 13, 0, 4, 4, 3, 70),
(69, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 10:47:11', 13, 0, 4, 4, 3, 69),
(70, 'Spring boot', 'developpement web', '2025-03-13', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 10:48:15', 38, 0, 4, 4, 0, 0),
(71, 'entretien java', 'fkhefhefjhefkuh', '2025-03-20', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 11:26:01', 39, 0, 36, 4, 0, 0),
(72, 'entretien java', 'fkhefhefjhefkuh', '2025-03-20', '12:00:00', 'EN_PRESENTIEL', 0, 'suppression', '2025-03-06 12:25:26', 39, 0, 36, 4, 37, 73),
(73, 'entretien spring boot ', 'test test test ', '2025-03-07', '12:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 12:26:05', 38, 0, 36, 4, 0, 0),
(74, 'rtyyyttututu', 'tutuuttuuttujujtgjg', '2025-03-12', '11:00:00', 'EN_PRESENTIEL', 0, 'ajout', '2025-03-06 12:26:47', 13, 0, 4, 4, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `meeting_url` text DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `notification_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `message`, `created_at`, `is_read`, `notification_type`) VALUES
(68, 46, 'Votre candidature pour l\'offre \"Medior QA Engineer\" a été acceptée.', '2025-04-30 01:57:24', 1, 'candidature_accepted'),
(69, 46, 'Votre candidature pour l\'offre \"Techniciens Support\" a été acceptée.', '2025-05-01 15:27:40', 1, 'candidature_accepted'),
(70, 46, 'Votre candidature pour l\'offre \"Medior QA Engineer\" a été acceptée.', '2025-05-04 19:36:25', 1, 'candidature_accepted');

-- --------------------------------------------------------

--
-- Table structure for table `offre`
--

CREATE TABLE `offre` (
  `id_offre` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  `type_contrat` varchar(50) NOT NULL,
  `salaire` int(11) NOT NULL,
  `lieu_travail` varchar(50) NOT NULL,
  `date_publication` date NOT NULL,
  `date_limite` date NOT NULL,
  `statut_offre` varchar(50) NOT NULL,
  `experience` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offre`
--

INSERT INTO `offre` (`id_offre`, `titre`, `description`, `type_contrat`, `salaire`, `lieu_travail`, `date_publication`, `date_limite`, `statut_offre`, `experience`) VALUES
(19, 'java', 'Java DeveloperLocation; NYC,NYExpertise in Java,J2EE Spring Boot, Spring Cloud, Eureka, Spring Cloud Gateway,Spring SecurityDeep Understanding of service design for Cloud environment and related technologies Dockers, Kubernetes, AWS and OpenShift.Experience with web servers Nginx and application server Tomcat. Knowledge of TLS, SSL certs.o Unix, Linux and Windowso Databases (Oracle and SQL)o High-Availability, Redundancy, Clustering, Disaster Recovery, Load Balancing', 'CDD', 3000, 'tunis', '2025-03-06', '2025-04-05', 'ouverte', '5ans'),
(22, 'Techniciens Support', 'Netcom Active Services,  un centre d’appel filial d\'une société française ( opérateur en télécommunication ), basé au Centre Urbain Nord, cherche à recruter des techniciens informatiques spécialistes en réseaux  ayant un très bon niveau de français .  Exigences de l\'emploi -Une spécialisation dans le domaine réseaux (diplôme et/ou une expérience significative).  - Une expérience exigée dans les centres d\'appels   -Un très bon niveau de français à l\'oral comme à l\'écrit (sinon s\'abstenir).', 'CDI', 2500, 'Tunisie, Tunis', '2025-03-28', '2025-03-29', 'Ouverte', '3 à 5 ans'),
(26, 'test', 'test', 'CDI', 2500, 'test', '2025-04-15', '2025-04-05', 'test', '3 ans'),
(30, 'y1', 'Iis igitur est difficilius satis facere, qui se Latina scripta dicunt contemnere. in quibus hoc primum est in quo admirer, cur in gravissimis rebus kd,klzc non delectet eos sermo patrius, cum idem fabellas Latinas ad verbum e Graecis expressas non inviti legant. quis enim tam inimicus paene nomini Romano est, qui Ennii Medeam aut Antiopam Pacuvii spernat aut reiciat, quod se isdem Euripidis fabulis delectari dicat, Latinas kskq,kqk, litteras oderit?', 'CDD', 1500, 'Algérie', '2025-04-15', '2025-04-28', 'Ouverte', '2 ans'),
(34, 'JAVA DEVELOPER', 'OYOYOYOOOYO', 'CDD', 1200, 'Emirats, Dubai', '2025-04-15', '2025-04-26', 'Ouverte', '2 ans'),
(45, 'Full-Stuck Developer', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'FreeLancer', 25000, 'Emirats,France et Tunisie', '2025-04-15', '2025-04-16', 'Ouverte', '4 ans'),
(48, 'Medior QA Engineer', 'Devops, Agile, scrum , C++, PostgreSql , Openshift, Jenkins, Ansible , Kafka, Junit, Git, Rest, Eureka  , Spring , Java, Angular, Hibernate, Python\nCommunication, Contact, Sérieux', 'CDI', 3500, 'Tunisie', '2025-04-27', '2025-04-30', 'Ouverte', '3 ans'),
(51, 'Engineer Q/A', 'gfhdfhkjghkfjsfjfjsfhd sgfhsxf se qet dh qeth qe qeth   het qfgq e th thq eh t tqdqey ty dfgdfh dshsdtq.', 'CDD', -1000, 'Tunis', '2025-05-12', '2025-06-11', 'En cours', '3ans'),
(52, 'Software Engineering', 'Language de programmation C , C++ , Python , Java , Spring Boot , Angular , Flutter , Flutter Flow , HTML , CSS , Javascript , Symfony , php , SQL', 'CDI', 5000, 'France', '2025-05-12', '2025-05-26', 'En cours', '5 ans');

-- --------------------------------------------------------

--
-- Table structure for table `projet`
--

CREATE TABLE `projet` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `datecréation` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `etat` enum('Terminé','Annulé','EnCours') DEFAULT NULL,
  `imageProjet` varchar(255) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projet`
--

INSERT INTO `projet` (`id`, `nom`, `description`, `datecréation`, `deadline`, `etat`, `imageProjet`, `id_user`) VALUES
(38, 'symfony', 'un projet devlopper avec le framework symfony basé sur le langage symfony', '2025-04-29', '2025-06-28', 'EnCours', 'img/68112d0dd67fa.png', 27),
(39, 'Angular', 'projet angular', '2025-04-29', '2025-05-04', 'Annulé', 'img/68112d4b41e5d.jpg', 27);

-- --------------------------------------------------------

--
-- Table structure for table `reclamation`
--

CREATE TABLE `reclamation` (
  `id_reclamation` int(11) NOT NULL,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `datedepot` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reclamation`
--

INSERT INTO `reclamation` (`id_reclamation`, `titre`, `description`, `type`, `datedepot`, `id_user`) VALUES
(37, 'tester', 'here goes', 'Technique', '2025-03-02 13:13:33', 30),
(38, 'recmlamtion', 'done in al', 'Administratif', '2025-03-06 12:16:18', 46);

-- --------------------------------------------------------

--
-- Table structure for table `reponse`
--

CREATE TABLE `reponse` (
  `id_reponse` int(11) NOT NULL,
  `message` text NOT NULL,
  `datedepot` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('en attente','résolu','en cours','') NOT NULL,
  `id_reclamation` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reponse`
--

INSERT INTO `reponse` (`id_reponse`, `message`, `datedepot`, `status`, `id_reclamation`, `id_user`) VALUES
(19, 'the problem was resolver with success', '2025-03-02 13:15:29', 'en cours', 37, 13);

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id_r` int(11) NOT NULL,
  `date` date NOT NULL,
  `id_f` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `motif_r` varchar(100) DEFAULT NULL,
  `attente` varchar(100) DEFAULT NULL,
  `langue` enum('Français','Anglais','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id_r`, `date`, `id_f`, `id_user`, `motif_r`, `attente`, `langue`) VALUES
(0, '2025-05-11', 31, 39, '25000', 'hhhhhhhh', 'Français');

-- --------------------------------------------------------

--
-- Table structure for table `sponsor`
--

CREATE TABLE `sponsor` (
  `idSponsor` int(11) NOT NULL,
  `nomSponso` varchar(100) DEFAULT NULL,
  `prenomSponso` varchar(100) DEFAULT NULL,
  `emailSponso` varchar(100) DEFAULT NULL,
  `budgetSponso` double DEFAULT NULL,
  `classement` enum('Or','Argent','Bronze') DEFAULT 'Bronze',
  `BudgetApresReduction` double DEFAULT 0,
  `secteurSponsor` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsor`
--

INSERT INTO `sponsor` (`idSponsor`, `nomSponso`, `prenomSponso`, `emailSponso`, `budgetSponso`, `classement`, `BudgetApresReduction`, `secteurSponsor`) VALUES
(51, 'actia', 'Ines ', 'actiaines@gmail.com', 15000, 'Argent', 14250, ''),
(52, 'Lilas', 'molka', 'lilas@gmail.com', 100, 'Bronze', 100, ''),
(53, 'Jadida', 'asma', 'jadida25@gmail.com', 2500000000, 'Or', 2250000000, '');

-- --------------------------------------------------------

--
-- Table structure for table `tache`
--

CREATE TABLE `tache` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'À faire',
  `priorite` int(11) NOT NULL DEFAULT 1,
  `projet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tache`
--

INSERT INTO `tache` (`id`, `titre`, `description`, `date_creation`, `deadline`, `statut`, `priorite`, `projet_id`, `user_id`) VALUES
(6, 'Gestion equipe', 'Ajouter l\'interface de l\'ajout de equipe', '2025-04-29', '2025-05-01', 'À faire', 3, 39, 39);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(200) NOT NULL,
  `role` enum('Employe','Manager','Candidat','RH') NOT NULL,
  `adresse` text NOT NULL,
  `sexe` enum('Femme','Homme') NOT NULL,
  `image_profil` varchar(255) DEFAULT NULL,
  `status` enum('Candidature','Entretien','programmé','Embauché','Refusé') DEFAULT NULL,
  `salaire_attendu` decimal(10,2) DEFAULT NULL,
  `poste` varchar(50) DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `experience_travail` int(11) DEFAULT NULL,
  `departement` text DEFAULT NULL,
  `competence` text DEFAULT NULL,
  `nombreProjet` int(11) DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `departement_géré` varchar(100) DEFAULT NULL,
  `ans_experience` int(11) DEFAULT NULL,
  `specialisation` varchar(100) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `messagereclamation` text DEFAULT NULL,
  `numt_tel` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nom`, `prenom`, `email`, `mdp`, `role`, `adresse`, `sexe`, `image_profil`, `status`, `salaire_attendu`, `poste`, `salaire`, `experience_travail`, `departement`, `competence`, `nombreProjet`, `budget`, `departement_géré`, `ans_experience`, `specialisation`, `banned`, `messagereclamation`, `numt_tel`) VALUES
(4, 'yassine', 'bouras', 'yassin18.gmail', '$2a$10$wf/OuuiIFCiXNxfthXhvBesJkdYOuJBvC2dCmPhks1I..84kYmfCO', 'Candidat', '', 'Homme', '', 'Candidature', 500.00, NULL, 0.00, 0, NULL, NULL, 0, 0.00, NULL, 0, NULL, 1, NULL, NULL),
(13, 'yassine', 'bouras', 'yassinbouras@Candidat.com', '$2a$10$DtnSrGlg5vFkKH4iLQ0i1.QmS0V9SbkH15a1roarIL01w6WciehVm', 'Employe', 'tunis', 'Homme', 'htdocs/images/1741246189131_yassinbouras@Candidat.com.png', 'Refusé', 0.00, 'financier', 1000.00, 444, 'finance', 'compete', 0, 0.00, NULL, 0, NULL, 0, NULL, 50797128),
(22, 'yassine', 'bouras', 'yassinbouras18@gmail.com', '$2a$10$XgxUy5V6UF2ddcfVMNqeyOToCscrzrD8QmKw1H9Z0q2V/C/NU0ytS', 'Employe', 'aezazeaze', 'Homme', 'htdocs/images/1741204261948_DALL·E 2025-01-29 20.23.44 - A professional HR (Human Resources) poster with a clean and modern design. The poster includes key HR elements like a magnifying glass over a resume, .jpg', 'Refusé', 0.00, 'financier', 1000.00, 4, 'finance', 'azeazeaze', 0, 0.00, NULL, 0, NULL, 0, NULL, NULL),
(23, 'yassine', 'bouras', 'yassinbouras0@rh.com', '$2a$10$BfkeEyK96mEw26jfncTUxOIi.YXwelZEKW51/sghILaI0elPLKvvK', 'RH', '', 'Homme', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 'conflicts manager', 0, NULL, NULL),
(25, 'jacem', 'test', 'test@gmail.com', '$2a$10$paCw9k.tsWquyIZsokngXOtsbIIzr7ZRp8tYKbZ9BILGI05g371QC', 'Manager', 'any', 'Homme', '10000000', 'Refusé', 0.00, NULL, 0.00, 0, NULL, NULL, 1500, 5000000.00, 'finance', 0, NULL, 0, NULL, NULL),
(26, 'Jacem', 'Hbaieb', 'jacemhbaieb@gmail.com', '$2a$10$xr5Lp0eHwsV2mdVDMtAM3.8jbmfuA1fi8aEyeQLSOrZd.Yp2T1VF6', 'Manager', '', 'Homme', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 5000000.00, 'finance', NULL, NULL, 0, NULL, NULL),
(27, 'asma', 'sallemi', 'asma@gmail.com', '$2a$10$J8W8yaD4CmaAP39Smyl/eurBnFe83b0r3VEv8uE79Nh421/Fskm4m', 'Manager', 'Ariana', 'Femme', 'htdocs/images/1741255922369_asma.jpeg', 'Refusé', 0.00, NULL, 0.00, 0, NULL, NULL, 10, 5000000.00, 'finance', 0, NULL, 0, NULL, NULL),
(28, 'kassous', 'eya', 'eyakassous0@rh.com', '$2y$13$DhoIQRbYQfTZu5SATz6wXu5wnY5IjoDiK8XSW5kXRl0NdBS/rTmfu', 'RH', 'ariana 2080', 'Femme', 'htdocs/images/1741255537912_eya.jpg', 'Refusé', 0.00, NULL, 0.00, 0, NULL, NULL, 0, 0.00, NULL, 5, 'conflicts manager', 0, NULL, NULL),
(29, 'yassin', 'bouras', 'yassinbouras100@gmail.com', '$2a$10$At7RgzkoZfhU9fbEst.DeueCWqaUQQfF9.N.obsASEHRU9Ww0xM9W', 'Candidat', 'tunis ', 'Homme', 'htdocs/images/1740920665757_DALL·E 2025-01-29 20.23.44 - A professional HR (Human Resources) poster with a clean and modern design. The poster includes key HR elements like a magnifying glass over a resume, .jpg', 'Candidature', 1500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 50797128),
(30, 'yassin', 'bouras12', 'yassinbouras12@gmail.com', '$2a$10$p3Hc/EFrAsg.aeYAFkkuiuKpa38FfqBN3BbYttubT9IpeLEW02Pmq', 'Manager', 'tunis', 'Homme', 'htdocs/images/1740920744563_DALL·E 2025-01-29 20.23.44 - A professional HR (Human Resources) poster with a clean and modern design. The poster includes key HR elements like a magnifying glass over a resume, .jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 150.00, 'tech', NULL, NULL, 0, NULL, NULL),
(32, 'tester', 'any', 'anytester@gmail.com', '$2a$10$NnxPVVstzq44A.OgMpUftes2tGmkM9JkKSpRhQIrZjUUJPTiarWM6', 'Candidat', '100', 'Homme', 'htdocs/images/1741008849698_DALL·E 2025-01-29 20.23.44 - A professional HR (Human Resources) poster with a clean and modern design. The poster includes key HR elements like a magnifying glass over a resume, .jpg', 'Candidature', 1500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 50797128),
(33, 'asma', 'soltani', 'asmasoltatni@gmail.com', '$2a$10$/5jQGxtOrXYQuuoHBJ7bWe0C76SxtCUdcgTxwy2XreAfolYEcTzgG', 'Candidat', '1rue mohmed  bey', 'Homme', 'htdocs/images/1741185279597_yassinbouras@Candidat.com.png', 'Candidature', 1500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(34, 'yassi', 'azeazeaz', 'azeazeazeaze@gmail.com', '$2a$10$uZ2/4Q3wcibhtjnxU5H13eCYsnlqlT4Y1XzMyq40mFbKvJRpsWt1e', 'Candidat', 'yassinbouras', 'Homme', 'htdocs/images/1741215676904_DALL·E 2025-01-29 20.23.44 - A professional HR (Human Resources) poster with a clean and modern design. The poster includes key HR elements like a magnifying glass over a resume, .jpg', 'Candidature', 1500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(35, 'Johnson', 'Emily', 'emily.johnson@rhcorp.com', '$2a$10$E1KGmNovyAKD3JKb/EKq.Oa7zmnTrX7Ysn3Ob6tn7JAvnux8s1raq', 'RH', '01/05/1985', 'Femme', 'New York', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 'Senior HR Manager', 0, NULL, NULL),
(36, 'Smith', 'James', 'james.smith24@gmail.com', '$2a$10$pIOs/Hs2GZYWaKI6PuHv5e9Er8bsu.AYyiJvIWGVV7EvQ3Z3Zuh3y', 'Candidat', '12/11/1992', 'Homme', 'Los Angeles', 'Candidature', 750.50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 50797128),
(37, 'Davis', 'Sophia', 'sophia.davis@financepro.com', '$2a$10$NVra.PwdS8fl9v3nA45GNuD2aicczmRFJZQnDmk9.8HdKKzPZSCti', 'Manager', '23/03/1980', 'Femme', 'Chicago', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15, 2500000.00, 'Finance', NULL, NULL, 0, NULL, NULL),
(38, 'Miller', 'Daniel', 'daniel.miller@corp.com', '$2a$10$.W9PGTpYXs/SQwIRfe85S.vEj/JdW/yvER3GVItRigYRkJ9FQzD/C', 'Employe', '09/09/1990', 'Homme', 'San Francisco', NULL, NULL, 'IT Support', 1200.00, 8, 'Technology', 'Technical Support', NULL, NULL, NULL, NULL, NULL, 0, NULL, 50797128),
(39, 'Molka', 'Gharbi', 'gharbimolka4@gmail.com', '$2a$10$dxBGKVMcPQIlnX3129e4qOnGmGI2bVewcPS5ptBSR.56waaPkB6BG', 'Employe', 'Ariana', 'Homme', 'htdocs/images/1741256440713_Molka.jpg', 'Refusé', 0.00, 'IT Support', 1200.00, 8, 'Technology', 'Technical Support', 0, 0.00, NULL, 0, NULL, 0, NULL, NULL),
(46, 'jouili', 'jacem', 'jacemjouili@gmail.com', '$2a$10$gItVkL2wJdmWtSP9OPx5HuMCk4hODkVWSyRUMJ0lAS4W8WhDWAmeG', 'Candidat', 'rue 20', 'Homme', '', NULL, NULL, NULL, 1500.00, 10, 'azeazeaz', 'azea', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_candidat` (`id_candidat`,`id_offre`),
  ADD KEY `bookmark_offre_fk` (`id_offre`);

--
-- Indexes for table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`id_candidature`),
  ADD KEY `candidature_offre_fk` (`id_offre`),
  ADD KEY `candidature_candidat_fk` (`id_candidat`);

--
-- Indexes for table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id_c`),
  ADD KEY `fk_formation` (`id_f`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `entretiens`
--
ALTER TABLE `entretiens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feedbackId` (`feedbackId`),
  ADD KEY `employe_id` (`employe_id`),
  ADD KEY `fk_idOffre` (`idOffre`),
  ADD KEY `fk_idCandidature` (`idCandidature`);

--
-- Indexes for table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_equipe` (`id_user`);

--
-- Indexes for table `equipe_employee`
--
ALTER TABLE `equipe_employee`
  ADD PRIMARY KEY (`equipe_id`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `equipe_projet`
--
ALTER TABLE `equipe_projet`
  ADD PRIMARY KEY (`equipe_id`,`projet_id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Indexes for table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id_favori`),
  ADD KEY `fk_user-favori` (`id_user`),
  ADD KEY `fk_formationfavori` (`id_f`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `historique_entretien`
--
ALTER TABLE `historique_entretien`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id_offre`);

--
-- Indexes for table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_projet` (`id_user`);

--
-- Indexes for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id_reclamation`),
  ADD KEY `fk_reclamation_user` (`id_user`);

--
-- Indexes for table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id_reponse`),
  ADD KEY `fk_reponse_reclamation` (`id_reclamation`),
  ADD KEY `fk_reponse_user` (`id_user`);

--
-- Indexes for table `tache`
--
ALTER TABLE `tache`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `id_candidature` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `entretiens`
--
ALTER TABLE `entretiens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id_favori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `historique_entretien`
--
ALTER TABLE `historique_entretien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id_offre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `projet`
--
ALTER TABLE `projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id_reclamation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id_reponse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tache`
--
ALTER TABLE `tache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmark_candidat_fk` FOREIGN KEY (`id_candidat`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookmark_offre_fk` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_candidat_fk` FOREIGN KEY (`id_candidat`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `candidature_offre_fk` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`) ON DELETE CASCADE;

--
-- Constraints for table `entretiens`
--
ALTER TABLE `entretiens`
  ADD CONSTRAINT `entretiens_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `user` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_feedback` FOREIGN KEY (`feedbackId`) REFERENCES `feedback` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_idCandidature` FOREIGN KEY (`idCandidature`) REFERENCES `candidature` (`id_candidature`),
  ADD CONSTRAINT `fk_idOffre` FOREIGN KEY (`idOffre`) REFERENCES `offre` (`id_offre`);

--
-- Constraints for table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `fk_user_equipe` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `equipe_employee`
--
ALTER TABLE `equipe_employee`
  ADD CONSTRAINT `equipe_employee_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipe_employee_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `equipe_projet`
--
ALTER TABLE `equipe_projet`
  ADD CONSTRAINT `equipe_projet_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipe_projet_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `fk_user_projet` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `fk_reclamation_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `fk_reponse_reclamation` FOREIGN KEY (`id_reclamation`) REFERENCES `reclamation` (`id_reclamation`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reponse_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
