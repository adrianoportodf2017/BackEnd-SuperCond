-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 07-Jun-2023 às 14:56
-- Versão do servidor: 5.7.31
-- versão do PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `supercondo`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `areadisableddays`
--

DROP TABLE IF EXISTS `areadisableddays`;
CREATE TABLE IF NOT EXISTS `areadisableddays` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_area` int(11) NOT NULL,
  `day` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `areas`
--

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `allowed` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `days` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `areas`
--

INSERT INTO `areas` (`id`, `allowed`, `title`, `cover`, `days`, `start_time`, `end_time`) VALUES
(1, 1, 'Academia', 'gym.jpg', '1,2,4,5', '06:00:00', '22:00:00'),
(2, 1, 'Piscina', 'pool.jpg', '1,2,3,4,5', '07:00:00', '23:00:00'),
(3, 1, 'Churrasqueira', 'barbecue.jpg', '4,5,6', '09:00:00', '22:00:00'),
(4, 1, 'Academia', 'gym.jpg', '1,2,4,5', '06:00:00', '22:00:00'),
(5, 1, 'Piscina', 'pool.jpg', '1,2,3,4,5', '07:00:00', '23:00:00'),
(6, 1, 'Churrasqueira', 'barbecue.jpg', '4,5,6', '09:00:00', '22:00:00'),
(7, 1, 'Academia', 'gym.jpg', '1,2,4,5', '06:00:00', '22:00:00'),
(8, 1, 'Piscina', 'pool.jpg', '1,2,3,4,5', '07:00:00', '23:00:00'),
(9, 1, 'Churrasqueira', 'barbecue.jpg', '4,5,6', '09:00:00', '22:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `billets`
--

DROP TABLE IF EXISTS `billets`;
CREATE TABLE IF NOT EXISTS `billets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileurl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `condominios`
--

DROP TABLE IF EXISTS `condominios`;
CREATE TABLE IF NOT EXISTS `condominios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `cnpj` varchar(255) DEFAULT NULL,
  `description` text,
  `address` varchar(255) DEFAULT NULL,
  `adress_number` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `address_zip` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `billit` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `condominios`
--

INSERT INTO `condominios` (`id`, `name`, `cnpj`, `description`, `address`, `adress_number`, `city`, `district`, `address_zip`, `state`, `billit`, `code`, `thumb`) VALUES
(1, 'teste', 'teste', 'teste', 'teste', 'teste', 'teste', NULL, NULL, NULL, NULL, NULL, 'public/image/condominios/C4j7uOthCf0mZI7CNJXPAHXGZKmoVXCTjJoiWocX.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `docs`
--

DROP TABLE IF EXISTS `docs`;
CREATE TABLE IF NOT EXISTS `docs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileurl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datecreated` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `docs`
--

INSERT INTO `docs` (`id`, `title`, `fileurl`, `filename`, `datecreated`) VALUES
(17, 'testexx', 'https://devcondbackend.agenciatecnet.com.br/public/storage/HK5Dz1A8I531UJiNqTVQvW9svQqZSocOU5ArfE2p.jpg', 'public/HK5Dz1A8I531UJiNqTVQvW9svQqZSocOU5ArfE2p.jpg', '2022-10-19 14:10:13'),
(18, 'Teste Doc 1', 'https://devcondbackend.agenciatecnet.com.br/public/storage/S9OYJvoWV7lfi2iAkkYAn2UXr3Jqmic12hTQ0b44.pdf', 'public/S9OYJvoWV7lfi2iAkkYAn2UXr3Jqmic12hTQ0b44.pdf', '2022-10-25 16:10:47');

-- --------------------------------------------------------

--
-- Estrutura da tabela `foundandlost`
--

DROP TABLE IF EXISTS `foundandlost`;
CREATE TABLE IF NOT EXISTS `foundandlost` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LOST',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `where` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datacreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2022_08_17_201815_createalltables', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `reservetions`
--

DROP TABLE IF EXISTS `reservetions`;
CREATE TABLE IF NOT EXISTS `reservetions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `reservation_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `unitpeoples`
--

DROP TABLE IF EXISTS `unitpeoples`;
CREATE TABLE IF NOT EXISTS `unitpeoples` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `id_condominio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `units`
--

INSERT INTO `units` (`id`, `name`, `owner_id`, `id_condominio`) VALUES
(1, 'APT 100', 1, '1'),
(2, 'APT 101', 1, '2'),
(3, 'APT 200', 0, '3'),
(4, 'APT 201', 0, '4'),
(5, 'APT 100', 1, '5'),
(6, 'APT 101', 1, '6'),
(7, 'APT 200', 0, '1'),
(8, 'APT 201', 0, '2'),
(9, 'APT 100', 1, '2'),
(10, 'APT 101', 1, '3'),
(11, 'APT 200', 0, '4'),
(12, 'APT 201', 0, '5');

-- --------------------------------------------------------

--
-- Estrutura da tabela `unitspets`
--

DROP TABLE IF EXISTS `unitspets`;
CREATE TABLE IF NOT EXISTS `unitspets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `race` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `unitvehicles`
--

DROP TABLE IF EXISTS `unitvehicles`;
CREATE TABLE IF NOT EXISTS `unitvehicles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_cpf_unique` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `cpf`, `password`, `profile`) VALUES
(1, 'adriano', 'adrianobr00@gmail.com', '03751966170', '$2y$10$8eSnQRVjB2HDKEtpIixCRe5wF1wqvJztoqVtdSg.53nvBRhqkAthC', '1'),
(2, 'adriano', 'adrianobr000@gmail.com', '03751966171', '$2y$10$JkZUN8TZXvIJ8GIbZchSi.mU5ltv/R6gJAV7RNpMEn1EsR4eiNysy', NULL),
(3, 'adriano', 'adrianobr01010@gmail.com', '03751966270', '$2y$10$l57farfZCmCzBZackeBJD.DgODTCB67JHXGXPM5hb.09HTDrDIf86', NULL),
(4, 'Administrador', 'admin@gmail.com', '03751966175', '$2y$10$hV3gdL79pK.GDK2sej158e2wLteRfU8mA8/bCwVVEsMGTJ3firFXm', '1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `walllikes`
--

DROP TABLE IF EXISTS `walllikes`;
CREATE TABLE IF NOT EXISTS `walllikes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_wall` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `walllikes`
--

INSERT INTO `walllikes` (`id`, `id_wall`, `id_user`) VALUES
(2, 3, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `walls`
--

DROP TABLE IF EXISTS `walls`;
CREATE TABLE IF NOT EXISTS `walls` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `walls`
--

INSERT INTO `walls` (`id`, `title`, `body`, `datecreated`) VALUES
(9, 'Criando um novo aviso', 'avisando a todos os visitantes', '2022-10-04 19:10:40'),
(11, 'Alerta geral para todos', 'Cuidado todo muuundo', '2022-10-17 19:10:47'),
(12, 'Ola', 'Oi', '2022-10-18 21:10:52');

-- --------------------------------------------------------

--
-- Estrutura da tabela `warnings`
--

DROP TABLE IF EXISTS `warnings`;
CREATE TABLE IF NOT EXISTS `warnings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IN_REVIEW',
  `datacreated` datetime NOT NULL,
  `photos` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
