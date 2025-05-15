/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: minestore
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`rules`)),
  `is_2fa` tinyint(4) NOT NULL DEFAULT 0,
  `totp` varchar(200) DEFAULT NULL,
  `last_login_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES
(1,'admin','$2y$10$JOyPUgBBXPKOsrNXfhsvwOZ9I7OIf5IQ2Ou29opmK1tRzBKIXby7S','2Wk7r7YUomBT6Mhjqru89jVEBGSnsrhMb0MA3SC8PVidgojuED5PIshKCvc9','{\"isAdmin\": true}',0,NULL,NULL,'2025-02-18 11:50:06','2025-02-18 11:50:06');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adverts`
--

DROP TABLE IF EXISTS `adverts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `adverts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `button_name` varchar(255) NOT NULL DEFAULT '',
  `button_url` varchar(255) NOT NULL DEFAULT '',
  `is_index` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adverts`
--

LOCK TABLES `adverts` WRITE;
/*!40000 ALTER TABLE `adverts` DISABLE KEYS */;
INSERT INTO `adverts` VALUES
(1,'','','','',0,'2025-02-18 12:17:30','2025-02-18 12:17:30');
/*!40000 ALTER TABLE `adverts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_game`
--

DROP TABLE IF EXISTS `auth_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_game` (
  `id` varchar(50) NOT NULL,
  `username` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_game`
--

LOCK TABLES `auth_game` WRITE;
/*!40000 ALTER TABLE `auth_game` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bans`
--

DROP TABLE IF EXISTS `bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `uuid` char(32) DEFAULT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `bans_username_unique` (`username`),
  UNIQUE KEY `bans_ip_unique` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bans`
--

LOCK TABLES `bans` WRITE;
/*!40000 ALTER TABLE `bans` DISABLE KEYS */;
/*!40000 ALTER TABLE `bans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_item_vars`
--

DROP TABLE IF EXISTS `cart_item_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_item_vars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_item_id` bigint(20) unsigned NOT NULL,
  `var_id` bigint(20) unsigned NOT NULL,
  `var_value` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `var_item_cart_item_id_var_id_unique` (`cart_item_id`,`var_id`),
  KEY `cart_item_vars_vars_id_foreign` (`var_id`),
  CONSTRAINT `cart_item_vars_cart_item_id_foreign` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_item_vars_var_id_foreign` FOREIGN KEY (`var_id`) REFERENCES `vars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_item_vars`
--

LOCK TABLES `cart_item_vars` WRITE;
/*!40000 ALTER TABLE `cart_item_vars` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_item_vars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `payment_type` tinyint(4) NOT NULL DEFAULT 0,
  `is_promoted` tinyint(4) NOT NULL DEFAULT 0,
  `coupon_applied` tinyint(4) NOT NULL DEFAULT 0,
  `price` double NOT NULL,
  `initial_price` double DEFAULT NULL,
  `variable_price` double DEFAULT NULL,
  `virtual_currency` tinyint(1) NOT NULL DEFAULT 0,
  `initial_variable_price` double DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_cart_id_foreign` (`cart_id`),
  KEY `cart_items_item_id_foreign` (`item_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES
(1,2,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-18 13:40:07','2025-02-18 13:40:07'),
(2,1,25,0,0,0,299,299,NULL,0,NULL,1,'2025-02-18 13:46:17','2025-02-18 13:46:17'),
(4,1,2,0,0,0,179,179,NULL,0,NULL,1,'2025-02-18 14:21:49','2025-02-18 14:21:49'),
(5,4,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-18 14:22:37','2025-02-18 14:22:37'),
(6,5,21,0,0,0,99,99,NULL,0,NULL,1,'2025-02-18 14:30:14','2025-02-18 14:30:14'),
(7,6,1,0,0,0,119,119,NULL,0,NULL,1,'2025-02-18 17:40:45','2025-02-18 17:40:45'),
(8,6,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-18 17:45:49','2025-02-18 17:45:49'),
(9,8,20,0,0,0,199,199,NULL,0,NULL,1,'2025-02-18 18:03:05','2025-02-18 18:03:05'),
(10,7,13,0,0,0,100,100,NULL,0,NULL,1,'2025-02-18 18:09:53','2025-02-18 18:09:53'),
(11,9,15,0,0,0,500,500,NULL,0,NULL,1,'2025-02-18 18:13:58','2025-02-18 18:13:58'),
(12,3,23,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 11:58:03','2025-02-19 11:58:03'),
(13,12,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 13:33:34','2025-02-19 13:33:34'),
(14,14,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 14:13:39','2025-02-19 14:13:39'),
(15,15,21,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 14:24:05','2025-02-19 14:24:05'),
(16,16,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 14:29:08','2025-02-19 14:29:08'),
(17,18,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 14:37:18','2025-02-19 14:37:18'),
(18,19,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 14:41:32','2025-02-19 14:41:32'),
(19,20,1,0,0,0,119,119,NULL,0,NULL,1,'2025-02-19 14:43:43','2025-02-19 14:43:43'),
(20,11,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 15:02:48','2025-02-19 15:02:48'),
(23,22,26,0,0,0,599,599,NULL,0,NULL,1,'2025-02-19 15:29:15','2025-02-19 15:29:15'),
(24,23,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-19 15:52:09','2025-02-19 15:52:09'),
(25,24,2,0,0,0,179,179,NULL,0,NULL,1,'2025-02-19 16:35:10','2025-02-19 16:35:10'),
(26,26,4,0,0,0,449,449,NULL,0,NULL,1,'2025-02-19 17:26:14','2025-02-19 17:26:14'),
(27,27,4,0,0,0,449,449,NULL,0,NULL,1,'2025-02-19 17:29:38','2025-02-19 17:29:38'),
(35,28,4,0,0,0,449,449,NULL,0,NULL,1,'2025-02-20 09:43:07','2025-02-20 09:43:07'),
(36,33,4,0,0,0,449,449,NULL,0,NULL,1,'2025-02-20 09:54:30','2025-02-20 09:54:30'),
(37,37,10,0,0,0,2749,2749,NULL,0,NULL,1,'2025-02-20 13:09:07','2025-02-20 13:09:07'),
(38,8,1,0,0,0,119,119,NULL,0,NULL,1,'2025-02-21 10:43:39','2025-02-21 10:43:39'),
(39,42,19,0,0,0,99,99,NULL,0,NULL,1,'2025-02-21 11:02:48','2025-02-21 11:02:48'),
(41,45,8,0,0,0,1349,1349,NULL,0,NULL,1,'2025-04-22 16:30:49','2025-04-22 16:30:49'),
(42,46,8,0,0,0,1349,1349,NULL,0,NULL,1,'2025-04-22 17:42:46','2025-04-22 17:43:13'),
(43,49,13,0,0,0,100,100,NULL,0,NULL,1,'2025-05-02 13:43:02','2025-05-02 13:43:02'),
(44,51,9,0,0,0,1899,1899,NULL,0,NULL,1,'2025-05-02 14:20:07','2025-05-02 14:20:07'),
(48,62,13,0,0,0,100,100,NULL,0,NULL,1,'2025-05-03 18:55:27','2025-05-03 18:55:27'),
(49,63,13,0,0,0,100,100,NULL,0,NULL,1,'2025-05-03 19:39:41','2025-05-03 19:39:41'),
(50,65,13,0,0,0,100,100,NULL,0,NULL,1,'2025-05-03 19:40:05','2025-05-03 19:40:05'),
(51,69,27,0,0,0,799,799,NULL,0,NULL,1,'2025-05-03 21:55:00','2025-05-03 21:55:00'),
(52,70,11,0,0,0,6949,6949,NULL,0,NULL,1,'2025-05-03 22:12:27','2025-05-03 22:12:27'),
(53,66,13,0,0,0,100,100,NULL,0,NULL,1,'2025-05-04 09:01:55','2025-05-04 09:01:55'),
(54,74,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 11:13:05','2025-05-04 11:13:05'),
(56,75,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 11:16:45','2025-05-04 11:16:45'),
(58,76,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 13:22:28','2025-05-04 13:22:28'),
(62,78,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 13:33:41','2025-05-04 13:33:41'),
(64,79,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 13:46:02','2025-05-04 13:46:02'),
(65,80,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 13:54:44','2025-05-04 13:54:44'),
(66,82,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 13:58:13','2025-05-04 13:58:13'),
(67,83,24,0,0,0,299,299,NULL,0,NULL,1,'2025-05-04 14:00:01','2025-05-04 14:00:01'),
(69,44,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-07 14:46:17','2025-05-07 14:46:17'),
(70,84,19,0,0,0,99,99,NULL,0,NULL,2,'2025-05-07 14:57:43','2025-05-10 09:32:41'),
(71,39,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-08 13:23:23','2025-05-08 13:23:43'),
(72,88,1,0,0,0,119,119,NULL,0,NULL,1,'2025-05-10 08:58:57','2025-05-10 08:58:57'),
(73,91,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:24:27','2025-05-10 11:24:27'),
(74,93,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:43:38','2025-05-10 11:43:38'),
(75,94,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:44:20','2025-05-10 11:44:20'),
(76,96,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:46:49','2025-05-10 11:46:49'),
(77,95,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:48:04','2025-05-10 11:48:04'),
(78,97,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:48:40','2025-05-10 11:48:40'),
(79,101,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:53:35','2025-05-10 11:53:35'),
(80,100,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-10 11:58:45','2025-05-10 11:58:45'),
(84,99,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 09:49:21','2025-05-12 09:49:21'),
(85,98,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 09:53:52','2025-05-12 09:53:52'),
(87,105,21,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 10:10:06','2025-05-12 10:10:06'),
(88,107,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 10:20:36','2025-05-12 10:20:36'),
(89,108,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 11:07:08','2025-05-12 11:07:08'),
(90,110,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-12 11:42:30','2025-05-12 11:42:30'),
(91,112,19,0,0,0,99,99,NULL,0,NULL,1,'2025-05-13 08:28:18','2025-05-13 08:28:18'),
(92,113,14,0,0,0,250,250,NULL,0,NULL,1,'2025-05-13 08:31:32','2025-05-13 08:31:32'),
(93,115,21,0,0,0,99,99,NULL,0,NULL,1,'2025-05-13 09:32:47','2025-05-13 09:32:47'),
(94,117,1,0,0,0,119,119,NULL,0,NULL,1,'2025-05-13 10:42:05','2025-05-13 10:42:05'),
(96,116,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-13 11:05:08','2025-05-13 11:05:08'),
(97,119,21,0,0,0,99,99,NULL,0,NULL,1,'2025-05-13 11:09:19','2025-05-13 11:09:19'),
(98,120,37,0,0,0,4,4,NULL,0,NULL,1,'2025-05-13 11:32:25','2025-05-13 11:33:39'),
(101,125,14,0,0,0,250,250,NULL,0,NULL,1,'2025-05-13 22:02:06','2025-05-13 22:02:06'),
(102,121,1,0,0,0,119,119,NULL,0,NULL,1,'2025-05-14 19:21:43','2025-05-14 19:21:43'),
(103,129,23,0,0,0,99,99,NULL,0,NULL,1,'2025-05-14 20:07:00','2025-05-14 20:07:00');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_select_servers`
--

DROP TABLE IF EXISTS `cart_select_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_select_servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `server_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_select_servers_item_id_foreign` (`item_id`),
  KEY `cart_select_servers_server_id_foreign` (`server_id`),
  KEY `cart_select_servers_cart_id_foreign` (`cart_id`),
  CONSTRAINT `cart_select_servers_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_select_servers_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_select_servers_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_select_servers`
--

LOCK TABLES `cart_select_servers` WRITE;
/*!40000 ALTER TABLE `cart_select_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_select_servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `items` int(11) NOT NULL DEFAULT 0,
  `price` double NOT NULL DEFAULT 0,
  `clear_price` double NOT NULL DEFAULT 0,
  `tax` float NOT NULL DEFAULT 0,
  `virtual_price` double NOT NULL DEFAULT 0,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `gift_id` bigint(20) unsigned DEFAULT NULL,
  `gift_sum` double NOT NULL DEFAULT 0,
  `referral` int(11) DEFAULT NULL,
  `discord_sync` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_id_foreign` (`user_id`),
  KEY `carts_coupon_id_foreign` (`coupon_id`),
  KEY `carts_gift_id_foreign` (`gift_id`),
  CONSTRAINT `carts_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carts_gift_id_foreign` FOREIGN KEY (`gift_id`) REFERENCES `gifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES
(1,1,2,478,478,0,0,NULL,NULL,0,NULL,0,0,'2025-02-18 12:19:36','2025-02-18 14:22:03'),
(2,2,1,99,99,0,0,NULL,NULL,0,NULL,0,1,'2025-02-18 13:40:02','2025-02-18 13:40:07'),
(3,3,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-18 13:53:39','2025-02-19 13:32:52'),
(4,1,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-18 14:22:27','2025-02-18 14:22:43'),
(5,1,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-18 14:22:57','2025-02-18 14:36:11'),
(6,1,2,218,218,0,0,NULL,NULL,0,NULL,0,1,'2025-02-18 14:36:17','2025-02-18 17:45:49'),
(7,4,1,100,100,0,0,NULL,NULL,0,NULL,0,1,'2025-02-18 14:43:49','2025-02-18 18:09:53'),
(8,5,2,318,318,0,0,NULL,NULL,0,NULL,0,0,'2025-02-18 18:03:01','2025-02-21 10:43:46'),
(9,6,1,500,500,0,0,NULL,NULL,0,NULL,0,1,'2025-02-18 18:11:32','2025-02-18 18:13:58'),
(10,7,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-18 19:15:28','2025-02-18 19:15:28'),
(11,3,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 13:33:21','2025-02-19 15:12:35'),
(12,3,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 13:33:21','2025-02-19 13:33:46'),
(13,8,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 13:56:59','2025-02-19 13:56:59'),
(14,9,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 13:57:06','2025-02-19 14:13:49'),
(15,9,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 14:23:59','2025-02-19 14:24:14'),
(16,9,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 14:26:09','2025-02-19 14:29:15'),
(17,10,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 14:26:59','2025-02-19 14:26:59'),
(18,9,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 14:35:32','2025-02-19 14:37:27'),
(19,9,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 14:41:28','2025-02-19 14:41:44'),
(20,9,1,119,119,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 14:43:37','2025-02-19 14:43:52'),
(21,3,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 15:14:10','2025-02-19 15:14:10'),
(22,11,1,599,599,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 15:22:35','2025-02-19 15:32:21'),
(23,12,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 15:51:14','2025-02-19 15:52:21'),
(24,9,1,179,179,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 16:18:13','2025-02-19 16:35:18'),
(25,9,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 16:38:04','2025-02-19 16:38:04'),
(26,11,1,449,449,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 17:26:03','2025-02-19 17:26:30'),
(27,11,1,449,449,0,0,NULL,NULL,0,NULL,0,0,'2025-02-19 17:29:29','2025-02-19 17:30:17'),
(28,11,1,449,449,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 17:41:17','2025-02-20 09:43:07'),
(29,13,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 20:32:18','2025-02-19 20:32:53'),
(30,14,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-19 20:33:03','2025-02-19 20:33:03'),
(31,15,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 09:15:06','2025-02-20 09:15:06'),
(32,16,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 09:19:08','2025-02-20 09:19:08'),
(33,17,1,449,449,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 09:54:26','2025-02-20 09:54:30'),
(34,18,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 10:55:16','2025-02-20 10:55:16'),
(35,19,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 11:48:46','2025-02-20 11:48:46'),
(36,20,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 12:41:45','2025-02-20 12:41:45'),
(37,21,1,2749,2749,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 13:08:03','2025-02-20 13:09:07'),
(38,22,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 16:24:03','2025-02-20 16:24:03'),
(39,23,1,99,99,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 17:19:51','2025-05-08 13:23:43'),
(40,24,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-20 23:53:53','2025-02-20 23:53:53'),
(41,25,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-02-21 03:46:46','2025-02-21 03:46:46'),
(42,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-02-21 11:02:43','2025-02-21 11:02:54'),
(43,26,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-03-08 12:29:55','2025-03-08 12:29:55'),
(44,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-04-22 12:30:40','2025-05-10 11:46:05'),
(45,27,1,1349,1349,0,0,NULL,NULL,0,NULL,0,0,'2025-04-22 16:26:12','2025-04-22 16:31:14'),
(46,27,1,1349,1349,0,0,NULL,NULL,0,NULL,0,0,'2025-04-22 16:31:43','2025-04-22 17:43:25'),
(47,27,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-04-22 17:43:33','2025-04-22 17:43:33'),
(48,28,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-04-23 04:05:41','2025-04-23 04:05:41'),
(49,29,1,100,100,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 13:40:56','2025-05-02 13:43:02'),
(50,30,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 14:17:51','2025-05-02 14:17:51'),
(51,31,1,1899,1899,0,0,NULL,NULL,0,NULL,0,0,'2025-05-02 14:19:57','2025-05-02 14:21:08'),
(52,31,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 14:22:14','2025-05-02 14:22:14'),
(53,32,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 16:20:53','2025-05-02 16:20:53'),
(54,33,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 17:53:09','2025-05-02 17:53:09'),
(55,34,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 19:29:04','2025-05-02 19:29:04'),
(56,35,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-02 21:47:14','2025-05-02 21:47:14'),
(57,36,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 10:15:18','2025-05-03 10:15:18'),
(58,37,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 10:43:09','2025-05-03 10:43:09'),
(59,38,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 16:37:13','2025-05-03 16:37:13'),
(60,38,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 16:37:13','2025-05-03 16:38:31'),
(61,39,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 16:58:48','2025-05-03 16:58:48'),
(62,40,1,100,100,0,0,NULL,NULL,0,NULL,0,0,'2025-05-03 18:54:28','2025-05-03 18:55:48'),
(63,40,1,100,100,0,0,NULL,NULL,0,NULL,0,0,'2025-05-03 18:56:07','2025-05-03 19:39:53'),
(64,41,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 19:16:04','2025-05-03 19:16:04'),
(65,40,1,100,100,0,0,NULL,NULL,0,NULL,0,0,'2025-05-03 19:39:57','2025-05-03 19:40:31'),
(66,40,1,100,100,0,0,NULL,NULL,0,NULL,0,0,'2025-05-03 19:40:55','2025-05-04 09:02:10'),
(67,42,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 20:35:54','2025-05-03 20:35:54'),
(68,43,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 20:51:06','2025-05-03 20:51:06'),
(69,44,1,799,799,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 21:53:41','2025-05-03 21:55:00'),
(70,45,1,6949,6949,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 22:11:50','2025-05-03 22:12:27'),
(71,46,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-03 22:31:29','2025-05-03 22:31:29'),
(72,40,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-04 09:04:34','2025-05-04 09:04:34'),
(73,47,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-04 09:34:57','2025-05-04 09:34:57'),
(74,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 11:12:37','2025-05-04 11:15:34'),
(75,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 11:15:52','2025-05-04 11:17:00'),
(76,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 11:17:19','2025-05-04 13:22:48'),
(77,49,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-04 12:16:26','2025-05-04 12:16:26'),
(78,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 13:22:58','2025-05-04 13:33:56'),
(79,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 13:45:52','2025-05-04 13:46:12'),
(80,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 13:53:22','2025-05-04 13:54:52'),
(81,50,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-04 13:54:05','2025-05-04 13:54:05'),
(82,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 13:57:52','2025-05-04 13:58:28'),
(83,48,1,299,299,0,0,NULL,NULL,0,NULL,0,0,'2025-05-04 13:59:50','2025-05-04 14:00:19'),
(84,51,2,198,198,0,0,NULL,NULL,0,NULL,0,0,'2025-05-06 12:06:37','2025-05-10 11:46:12'),
(85,52,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-07 13:28:02','2025-05-07 13:28:02'),
(86,53,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-08 14:32:35','2025-05-08 14:32:35'),
(87,54,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-08 17:49:12','2025-05-08 17:49:12'),
(88,55,1,119,119,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 08:58:55','2025-05-10 11:43:06'),
(89,56,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-10 09:03:00','2025-05-10 09:03:00'),
(90,57,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-10 10:13:09','2025-05-10 10:13:09'),
(91,58,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:24:20','2025-05-10 11:43:17'),
(92,55,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-10 11:43:07','2025-05-10 11:43:07'),
(93,58,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:43:18','2025-05-10 11:43:47'),
(94,58,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:43:48','2025-05-10 11:44:29'),
(95,51,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:46:13','2025-05-10 11:50:23'),
(96,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:46:41','2025-05-10 11:47:00'),
(97,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:47:22','2025-05-10 11:48:47'),
(98,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:49:38','2025-05-12 09:55:01'),
(99,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:49:38','2025-05-12 09:49:40'),
(100,51,1,99,99,0,0,NULL,NULL,0,NULL,0,1,'2025-05-10 11:50:24','2025-05-10 11:58:45'),
(101,58,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-10 11:53:22','2025-05-12 09:04:28'),
(102,59,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-11 19:17:06','2025-05-11 19:17:06'),
(103,60,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-11 19:18:15','2025-05-11 19:18:15'),
(105,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-12 09:56:13','2025-05-12 10:10:24'),
(106,58,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-12 10:08:12','2025-05-12 10:08:12'),
(107,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-12 10:11:19','2025-05-12 10:20:54'),
(108,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-12 10:21:42','2025-05-12 11:07:49'),
(109,61,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-12 10:54:52','2025-05-12 10:54:52'),
(110,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-12 11:08:25','2025-05-12 11:42:46'),
(111,62,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-12 11:37:04','2025-05-12 11:37:04'),
(112,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-12 11:43:31','2025-05-13 09:28:11'),
(113,48,1,250,250,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 08:31:24','2025-05-13 08:45:22'),
(114,63,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 08:48:51','2025-05-13 08:48:51'),
(115,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 09:28:48','2025-05-13 09:33:43'),
(116,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 09:34:39','2025-05-13 11:05:18'),
(117,64,1,119,119,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 10:41:51','2025-05-13 10:42:23'),
(118,64,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 10:42:28','2025-05-13 10:42:28'),
(119,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 11:06:02','2025-05-13 11:09:57'),
(120,5,1,4,4,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 11:10:11','2025-05-13 11:34:08'),
(121,5,1,119,119,0,0,NULL,NULL,0,NULL,0,0,'2025-05-13 11:34:45','2025-05-14 19:21:50'),
(122,65,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 12:17:00','2025-05-13 12:17:00'),
(123,66,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 13:44:40','2025-05-13 13:44:40'),
(124,67,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 17:11:00','2025-05-13 17:11:00'),
(125,48,1,250,250,0,0,NULL,NULL,0,NULL,0,1,'2025-05-13 22:02:00','2025-05-13 22:02:06'),
(126,68,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-14 04:55:48','2025-05-14 04:55:48'),
(127,69,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-14 16:18:52','2025-05-14 16:18:52'),
(128,70,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-14 18:51:29','2025-05-14 18:51:29'),
(129,5,1,99,99,0,0,NULL,NULL,0,NULL,0,0,'2025-05-14 19:22:08','2025-05-14 20:34:18'),
(130,71,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-14 19:24:51','2025-05-14 19:24:51'),
(131,5,0,0,0,0,0,NULL,NULL,0,NULL,0,1,'2025-05-14 20:35:10','2025-05-14 20:35:10');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `sorting` int(11) NOT NULL DEFAULT 0,
  `is_enable` tinyint(4) NOT NULL DEFAULT 1,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `gui_item_id` varchar(200) DEFAULT 'minecraft:chest',
  `is_cumulative` tinyint(4) NOT NULL DEFAULT 0,
  `is_listing` tinyint(4) NOT NULL DEFAULT 0,
  `is_comparison` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id_idx` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,0,'Магическое выживание','1.png','magic','<p><br></p>',0,1,0,'minecraft:experience_bottle',0,0,0,'2025-02-18 12:17:53','2025-02-18 13:08:26'),
(2,1,'Привилегии','2.png','magic/ranks','<p><br></p>',0,1,0,NULL,1,0,0,'2025-02-18 12:20:35','2025-05-13 12:27:45'),
(3,0,'Валюта','3.png','currency','<p><br></p>',1,1,0,NULL,0,0,0,'2025-02-18 12:55:24','2025-02-18 13:10:12'),
(4,0,'[DELETED] Прочее',NULL,'deleted-4','<p><br></p>',2,1,1,NULL,0,0,0,'2025-02-18 13:07:16','2025-02-18 13:10:15'),
(5,1,'Снятие наказаний',NULL,'magic/punishments','<p><br></p>',1,1,0,NULL,0,0,0,'2025-02-18 13:09:25','2025-05-13 12:21:14'),
(6,0,'Bedrock выживание','6.png','bedrock','<p><br></p>',0,1,0,NULL,0,0,0,'2025-02-18 13:11:37','2025-05-07 13:38:28'),
(7,6,'Снятие наказаний',NULL,'bedrock/punishments','<p><br></p>',1,1,0,NULL,0,0,0,'2025-02-18 13:11:50','2025-02-18 13:16:11'),
(8,6,'Привилегии',NULL,'bedrock/ranks','<p><br></p>',0,1,0,NULL,1,0,0,'2025-02-18 13:16:05','2025-05-13 12:27:58');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chargebacks`
--

DROP TABLE IF EXISTS `chargebacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chargebacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `sid` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`)),
  PRIMARY KEY (`id`),
  KEY `chargebacks_payment_id_foreign` (`payment_id`),
  CONSTRAINT `chargebacks_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chargebacks`
--

LOCK TABLES `chargebacks` WRITE;
/*!40000 ALTER TABLE `chargebacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `chargebacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmd_queue`
--

DROP TABLE IF EXISTS `cmd_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cmd_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` bigint(20) unsigned NOT NULL,
  `commands_history_id` bigint(20) unsigned DEFAULT NULL,
  `command` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`command`)),
  `pending` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `cmd_queue_command_history_id_foreign` (`commands_history_id`),
  KEY `cmd_queue_server_id_foreign` (`server_id`),
  CONSTRAINT `cmd_queue_commands_history_id_foreign` FOREIGN KEY (`commands_history_id`) REFERENCES `commands_history` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cmd_queue_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmd_queue`
--

LOCK TABLES `cmd_queue` WRITE;
/*!40000 ALTER TABLE `cmd_queue` DISABLE KEYS */;
INSERT INTO `cmd_queue` VALUES
(4,2,1,'{\"username\":\"testest\",\"is_online_required\":true,\"command\":\"unmute testest\",\"package_name\":\"\\u0420\\u0430\\u0437\\u043c\\u0443\\u0442\"}',0),
(37,3,28,'{\"username\":\"dronsyy\",\"is_online_required\":true,\"command\":\"unmute dronsyy\",\"package_name\":\"\\u0420\\u0430\\u0437\\u043c\\u0443\\u0442\"}',1),
(40,3,NULL,'{\"username\":\"admin\",\"is_online_required\":false,\"command\":\"say Official MineStoreCMS.com Plugin Connected Successfully\",\"package_name\":null}',1),
(52,4,NULL,'{\"username\":\"admin\",\"is_online_required\":false,\"command\":\"say Official MineStoreCMS.com Plugin Connected Successfully\",\"package_name\":null}',1),
(56,4,NULL,'{\"username\":\"admin\",\"is_online_required\":false,\"command\":\"say Official MineStoreCMS.com Plugin Connected Successfully\",\"package_name\":null}',1);
/*!40000 ALTER TABLE `cmd_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commands`
--

DROP TABLE IF EXISTS `commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `commands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_type` tinyint(4) NOT NULL,
  `item_id` int(11) NOT NULL,
  `command` text NOT NULL,
  `event` tinyint(4) NOT NULL DEFAULT 0,
  `is_online_required` tinyint(4) NOT NULL DEFAULT 0,
  `execute_once_on_any_server` tinyint(4) NOT NULL DEFAULT 0,
  `delay_value` int(11) NOT NULL DEFAULT 0,
  `delay_unit` tinyint(4) NOT NULL DEFAULT 0,
  `repeat_value` int(11) NOT NULL DEFAULT 0,
  `repeat_unit` tinyint(4) NOT NULL DEFAULT 0,
  `repeat_cycles` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_type_id_idx` (`item_type`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commands`
--

LOCK TABLES `commands` WRITE;
/*!40000 ALTER TABLE `commands` DISABLE KEYS */;
INSERT INTO `commands` VALUES
(91,0,23,'lp user {username} parent addtemp vip 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(92,0,24,'lp user {username} parent add vip',0,1,0,0,0,0,1,0,NULL,NULL),
(93,0,25,'lp user {username} parent addtemp premium 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(94,0,26,'lp user {username} parent add premium',0,1,0,0,0,0,1,0,NULL,NULL),
(95,0,27,'lp user {username} parent addtemp deluxe 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(96,0,28,'lp user {username} parent add deluxe',0,1,0,0,0,0,1,0,NULL,NULL),
(97,0,29,'lp user {username} parent addtemp ultimate 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(98,0,30,'lp user {username} parent add ultimate',0,1,0,0,0,0,1,0,NULL,NULL),
(99,0,31,'lp user {username} parent addtemp special 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(100,0,32,'lp user {username} parent add special',0,1,0,0,0,0,1,0,NULL,NULL),
(101,0,33,'lp user {user} parent addtemp private 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(102,0,34,'lp user {username} parent add private',0,1,0,0,0,0,1,0,NULL,NULL),
(103,0,35,'lp user {username} parent addtemp limited 30d',0,1,0,0,0,0,1,0,NULL,NULL),
(104,0,36,'lp user {username} parent add limited',0,1,0,0,0,0,1,0,NULL,NULL),
(119,0,19,'unmute {username}',0,1,0,0,0,0,1,0,NULL,NULL),
(120,0,20,'unban {username}',0,1,0,0,0,0,1,0,NULL,NULL),
(121,0,1,'lp user {username} parent addtemp iron 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(122,0,1,'adonate {username} Rank1.5',0,1,0,0,0,0,1,0,NULL,NULL),
(123,0,2,'lp user {username} parent add iron',0,1,0,0,0,0,1,0,NULL,NULL),
(124,0,2,'adonate {username} Rank1',0,1,0,0,0,0,1,0,NULL,NULL),
(125,0,3,'lp user {username} parent addtemp gold 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(126,0,3,'adonate {username} Rank2.5',0,1,0,0,0,0,1,0,NULL,NULL),
(127,0,4,'lp user {username} parent add gold',0,1,0,0,0,0,1,0,NULL,NULL),
(128,0,4,'adonate {username} Rank2',0,1,0,0,0,0,1,0,NULL,NULL),
(129,0,5,'lp user {username} parent addtemp redstone 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(130,0,5,'adonate {username} Rank3.5',0,1,0,0,0,0,1,0,NULL,NULL),
(131,0,6,'lp user {username} parent add redstone',0,1,0,0,0,0,1,0,NULL,NULL),
(132,0,6,'adonate {username} Rank3',0,1,0,0,0,0,1,0,NULL,NULL),
(133,0,7,'lp user {username} parent addtemp diamond 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(134,0,7,'adonate {username} Rank4.5',0,1,0,0,0,0,1,0,NULL,NULL),
(135,0,8,'lp user {username} parent add diamond',0,1,0,0,0,0,1,0,NULL,NULL),
(136,0,8,'adonate {username} Rank4',0,1,0,0,0,0,1,0,NULL,NULL),
(137,0,9,'lp user {username} parent addtemp emerald 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(138,0,9,'adonate {username} Rank5.5',0,1,0,0,0,0,1,0,NULL,NULL),
(139,0,10,'lp user {username} parent add emerald',0,1,0,0,0,0,1,0,NULL,NULL),
(140,0,10,'adonate {username} Rank5',0,1,0,0,0,0,1,0,NULL,NULL),
(141,0,11,'lp user {username} parent addtemp god 30day',0,1,0,0,0,0,1,0,NULL,NULL),
(142,0,11,'adonate {username} Rank6.5',0,1,0,0,0,0,1,0,NULL,NULL),
(143,0,12,'lp user {username} parent add god',0,1,0,0,0,0,1,0,NULL,NULL),
(144,0,12,'adonate {username} Rank6',0,1,0,0,0,0,1,0,NULL,NULL),
(145,0,13,'points give {username} 500',0,1,0,0,0,0,1,0,NULL,NULL),
(146,0,13,'adonate {username} D1',0,1,0,0,0,0,1,0,NULL,NULL),
(147,0,14,'points give {username} 1500',0,1,0,0,0,0,1,0,NULL,NULL),
(148,0,14,'adonate {username} D2',0,1,0,0,0,0,1,0,NULL,NULL),
(149,0,15,'points give {username} 3000',0,1,0,0,0,0,1,0,NULL,NULL),
(150,0,15,'adonate {username} D3',0,1,0,0,0,0,1,0,NULL,NULL),
(151,0,16,'points give {username} 6000',0,1,0,0,0,0,1,0,NULL,NULL),
(152,0,16,'adonate {username} D4',0,1,0,0,0,0,1,0,NULL,NULL),
(153,0,17,'points give {username} 8500',0,1,0,0,0,0,1,0,NULL,NULL),
(154,0,17,'adonate {username} D5',0,1,0,0,0,0,1,0,NULL,NULL),
(155,0,18,'points give {username} 11000',0,1,0,0,0,0,1,0,NULL,NULL),
(156,0,18,'adonate {username} D6',0,1,0,0,0,0,1,0,NULL,NULL),
(157,0,21,'unmute {username}',0,1,0,0,0,0,1,0,NULL,NULL),
(159,0,22,'unban {username}',0,1,0,0,0,0,1,0,NULL,NULL);
/*!40000 ALTER TABLE `commands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commands_history`
--

DROP TABLE IF EXISTS `commands_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `commands_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `cmd` text NOT NULL,
  `username` varchar(255) NOT NULL,
  `server_id` bigint(20) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_online_required` tinyint(4) NOT NULL DEFAULT 0,
  `execute_once_on_any_server` tinyint(4) NOT NULL DEFAULT 0,
  `initiated` tinyint(4) DEFAULT 0,
  `package_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commands_history`
--

LOCK TABLES `commands_history` WRITE;
/*!40000 ALTER TABLE `commands_history` DISABLE KEYS */;
INSERT INTO `commands_history` VALUES
(1,166,19,0,'unmute testest','testest',2,2,1,0,0,NULL,'2025-05-12 10:08:35','2025-05-13 11:58:53','2025-05-12 10:08:35'),
(2,169,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-12 11:23:05','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(3,169,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-12 11:23:05','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(4,170,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-12 11:43:29','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(5,170,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-12 11:43:29','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(6,164,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-12 11:50:39','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(7,164,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-12 11:50:39','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(8,164,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-12 11:50:39','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(9,164,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-12 11:50:39','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(10,165,19,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-12 11:56:18','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(11,167,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-12 12:11:18','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(12,167,21,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-12 12:11:18','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(13,167,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-12 12:11:18','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(14,167,21,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-12 12:11:18','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(15,168,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-12 12:21:47','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(16,168,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-12 12:21:47','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(17,170,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-13 08:26:02','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(18,170,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-13 08:26:17','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(19,172,19,0,'unmute dronsyy','dronsyy',2,4,1,0,0,NULL,'2025-05-13 09:28:48','2025-05-13 09:31:53','2025-05-13 09:28:48'),
(20,172,19,0,'unmute dronsyy','dronsyy',2,4,1,0,0,NULL,'2025-05-13 09:28:48','2025-05-13 09:31:55','2025-05-13 09:28:48'),
(21,173,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-13 09:34:20','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(22,173,21,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-13 09:34:20','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(23,173,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-13 09:34:20','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(24,173,21,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-13 09:34:20','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(25,173,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-13 11:01:17','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(26,173,21,0,'unmute dronsyy','dronsyy',1,0,1,0,1,NULL,'2025-05-13 11:01:35','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(27,173,21,0,'unmute dronsyy','dronsyy',2,0,1,0,1,NULL,'2025-05-13 11:01:35','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(28,173,21,0,'unmute dronsyy','dronsyy',3,2,1,0,0,NULL,'2025-05-13 11:01:35','2025-05-13 11:02:07','2025-05-13 11:01:35'),
(29,176,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',1,0,1,0,1,NULL,'2025-05-13 11:06:02','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(30,176,23,0,'lp user dronsyy parent addtemp vip 30d','dronsyy',2,0,1,0,1,NULL,'2025-05-13 11:06:02','2025-05-13 11:59:14','2025-05-13 11:59:14'),
(32,177,21,0,'unmute dronsyy','dronsyy',4,0,1,0,1,NULL,'2025-05-13 11:11:40','2025-05-13 11:41:54','2025-05-13 11:41:54'),
(33,178,37,0,'say done','dronsyy',5,4,1,0,0,NULL,'2025-05-13 11:34:45','2025-05-13 11:52:01','2025-05-13 11:35:45'),
(34,178,37,0,'say done','dronsyy',5,4,1,0,0,NULL,'2025-05-13 11:34:45','2025-05-13 11:52:03','2025-05-13 11:35:45'),
(35,178,37,0,'say done','dronsyy',5,4,1,0,0,NULL,'2025-05-13 11:42:05','2025-05-13 11:52:03','2025-05-13 11:35:45'),
(36,178,37,0,'say done','dronsyy',5,0,1,0,1,NULL,'2025-05-13 11:50:15','2025-05-13 11:55:43','2025-05-13 11:55:43'),
(37,178,37,0,'say done','dronsyy',5,0,1,0,1,NULL,'2025-05-13 11:53:50','2025-05-13 11:55:43','2025-05-13 11:55:43');
/*!40000 ALTER TABLE `commands_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comparisons`
--

DROP TABLE IF EXISTS `comparisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comparisons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `type` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `sorting` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comparisons_category_id_foreign` (`category_id`),
  CONSTRAINT `comparisons_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comparisons`
--

LOCK TABLES `comparisons` WRITE;
/*!40000 ALTER TABLE `comparisons` DISABLE KEYS */;
/*!40000 ALTER TABLE `comparisons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_apply`
--

DROP TABLE IF EXISTS `coupon_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_apply` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) unsigned NOT NULL,
  `apply_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_apply_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `coupon_apply_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_apply`
--

LOCK TABLES `coupon_apply` WRITE;
/*!40000 ALTER TABLE `coupon_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `discount` double NOT NULL,
  `uses` int(11) DEFAULT 0,
  `available` int(11) DEFAULT NULL,
  `limit_per_user` int(11) DEFAULT 0,
  `min_basket` double NOT NULL DEFAULT 0,
  `apply_type` tinyint(4) NOT NULL DEFAULT 0,
  `note` text NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `expire_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `name` char(3) NOT NULL,
  `value` decimal(16,8) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES
('AED',3.93830800),
('AFN',75.91165000),
('ALL',100.49485300),
('AMD',416.86454500),
('ANG',1.93202400),
('AOA',914.06490700),
('ARS',1200.00000000),
('AUD',1.62539400),
('AWG',1.93002700),
('AZN',1.80430400),
('BAM',1.95764800),
('BBD',2.16454300),
('BDT',125.96889700),
('BGN',1.95597000),
('BHD',0.40417400),
('BIF',3081.00804400),
('BMD',1.07223700),
('BND',1.45046900),
('BOB',7.40799200),
('BRL',5.80927100),
('BSD',1.07201200),
('BTN',89.54651900),
('BWP',14.54572900),
('BYN',3.50831100),
('BYR',21015.84604100),
('BZD',2.16084000),
('CAD',1.47459400),
('CDF',3050.51419300),
('CHF',0.95581400),
('CLF',0.03606800),
('CLP',995.27176500),
('CNH',7.79709600),
('CNY',7.78026000),
('COP',4444.79782600),
('CRC',563.33170700),
('CUC',1.07223700),
('CUP',28.41428200),
('CVE',110.36917300),
('CZK',24.72857200),
('DJF',190.87015500),
('DKK',7.46016400),
('DOP',63.51995400),
('DZD',144.56671300),
('EGP',51.15696600),
('ERN',16.08355600),
('ETB',61.82825700),
('EUR',1.00000000),
('FJD',2.40476000),
('FKP',0.84127900),
('GBP',0.84512700),
('GEL',3.07691200),
('GGP',0.84127900),
('GHS',16.07830100),
('GIP',0.84127900),
('GMD',72.64425000),
('GNF',9228.71062100),
('GTQ',8.32986200),
('GYD',224.25552300),
('HKD',8.37496500),
('HNL',26.50201400),
('HRK',7.52542900),
('HTG',142.20422100),
('HUF',396.17497100),
('IDR',17674.80902700),
('ILS',3.99251800),
('IMP',0.84127900),
('INR',89.57066200),
('IQD',1404.32548800),
('IRR',45141.17963100),
('ISK',149.51259200),
('JEP',0.84127900),
('JMD',167.02765100),
('JOD',0.76010800),
('JPY',169.21133700),
('KES',138.83346600),
('KGS',94.20663700),
('KHR',4415.16730500),
('KMF',490.54560800),
('KPW',965.01347900),
('KRW',1482.35168700),
('KWD',0.32889800),
('KYD',0.89334300),
('KZT',486.36906500),
('LAK',23483.16484700),
('LBP',96004.21464900),
('LKR',325.65737500),
('LRD',207.97630100),
('LSL',19.53563900),
('LTL',3.16603700),
('LVL',0.64858600),
('LYD',5.19790600),
('MAD',10.71241100),
('MDL',19.14106700),
('MGA',4804.53481400),
('MKD',61.54913500),
('MMK',2244.94042600),
('MNT',3699.21790300),
('MOP',8.62293400),
('MRO',384.43294600),
('MRU',42.21684700),
('MUR',50.51269300),
('MVR',16.52328300),
('MWK',1858.25393400),
('MXN',19.83145700),
('MYR',5.06042400),
('MZN',68.29615100),
('NAD',19.53563900),
('NGN',1590.17046000),
('NIO',39.45724200),
('NOK',11.47521500),
('NPR',143.27423100),
('NZD',1.75312400),
('OMR',0.41275800),
('PAB',1.07201200),
('PEN',4.04301600),
('PGK',4.12189000),
('PHP',62.92798600),
('PKR',298.61234900),
('PLN',4.35568400),
('PYG',8073.62037700),
('QAR',3.90999000),
('RON',4.97678500),
('RSD',117.08686700),
('RUB',94.86622000),
('RWF',1400.95898900),
('SAR',4.02317100),
('SBD',9.07284100),
('SCR',16.11908300),
('SDG',628.33121200),
('SEK',11.27416500),
('SGD',1.45068300),
('SHP',1.35471800),
('SLE',24.49772400),
('SLL',22484.27672600),
('SOS',612.67828300),
('SRD',33.63339700),
('SSP',633.29532700),
('STD',22193.14195000),
('SVC',9.37985300),
('SYP',2694.02819400),
('SZL',19.52843200),
('THB',39.51298200),
('TJS',11.53904600),
('TMT',3.75283000),
('TND',3.35246400),
('TOP',2.53144300),
('TRY',35.21306800),
('TTD',7.28587700),
('TWD',34.70854400),
('TZS',2812.49280100),
('UAH',43.57763100),
('UGX',3971.74877900),
('USD',1.07223700),
('UYU',42.12976500),
('UZS',13536.77683800),
('VEF',3884235.01724900),
('VES',38.99999600),
('VND',25365.00000000),
('VUV',127.29810000),
('WST',3.00292300),
('XAF',656.57671700),
('XAG',0.03673900),
('XAU',0.00046400),
('XCD',2.89777400),
('XDR',0.81356800),
('XOF',656.57671700),
('XPF',119.33174200),
('YER',268.38927500),
('ZAR',19.58349900),
('ZMK',9651.41793700),
('ZMW',27.68513100),
('ZWL',345.25989000);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_role_queue`
--

DROP TABLE IF EXISTS `discord_role_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_role_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `discord_id` varchar(255) NOT NULL,
  `action` tinyint(4) NOT NULL DEFAULT 0,
  `role_id` varchar(255) NOT NULL,
  `internal_role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `processed` tinyint(4) NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discord_role_queue_internal_role_id_foreign` (`internal_role_id`),
  KEY `discord_role_queue_user_id_foreign` (`user_id`),
  KEY `discord_role_queue_payment_id_foreign` (`payment_id`),
  CONSTRAINT `discord_role_queue_internal_role_id_foreign` FOREIGN KEY (`internal_role_id`) REFERENCES `discord_roles` (`id`),
  CONSTRAINT `discord_role_queue_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`),
  CONSTRAINT `discord_role_queue_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_role_queue`
--

LOCK TABLES `discord_role_queue` WRITE;
/*!40000 ALTER TABLE `discord_role_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_role_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discord_roles`
--

DROP TABLE IF EXISTS `discord_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `discord_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` varchar(60) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_roles`
--

LOCK TABLES `discord_roles` WRITE;
/*!40000 ALTER TABLE `discord_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donation_goals`
--

DROP TABLE IF EXISTS `donation_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `donation_goals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `is_enabled` tinyint(4) NOT NULL DEFAULT 1,
  `automatic_disabling` tinyint(4) NOT NULL DEFAULT 0,
  `current_amount` double NOT NULL,
  `goal_amount` double NOT NULL,
  `cmdExecute` tinyint(4) NOT NULL DEFAULT 0,
  `commands_to_execute` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`commands_to_execute`)),
  `servers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`servers`)),
  `reached_at` timestamp NULL DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `disable_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donation_goals`
--

LOCK TABLES `donation_goals` WRITE;
/*!40000 ALTER TABLE `donation_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `donation_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gifts`
--

DROP TABLE IF EXISTS `gifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_balance` double NOT NULL,
  `end_balance` double NOT NULL,
  `expire_at` datetime DEFAULT NULL,
  `note` text NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gifts_payment_id_foreign` (`payment_id`),
  CONSTRAINT `gifts_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gifts`
--

LOCK TABLES `gifts` WRITE;
/*!40000 ALTER TABLE `gifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `gifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `global_cmds`
--

DROP TABLE IF EXISTS `global_cmds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `global_cmds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `price` double NOT NULL,
  `is_online` tinyint(4) NOT NULL DEFAULT 1,
  `cmd` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `global_cmds`
--

LOCK TABLES `global_cmds` WRITE;
/*!40000 ALTER TABLE `global_cmds` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_cmds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_comparison`
--

DROP TABLE IF EXISTS `item_comparison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_comparison` (
  `item_id` bigint(20) unsigned NOT NULL,
  `comparison_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`item_id`,`comparison_id`),
  KEY `item_comparison_comparison_id_foreign` (`comparison_id`),
  CONSTRAINT `item_comparison_comparison_id_foreign` FOREIGN KEY (`comparison_id`) REFERENCES `comparisons` (`id`),
  CONSTRAINT `item_comparison_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_comparison`
--

LOCK TABLES `item_comparison` WRITE;
/*!40000 ALTER TABLE `item_comparison` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_comparison` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_discord_roles`
--

DROP TABLE IF EXISTS `item_discord_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_discord_roles` (
  `item_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`item_id`,`role_id`),
  KEY `item_discord_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `item_discord_roles_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_discord_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `discord_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_discord_roles`
--

LOCK TABLES `item_discord_roles` WRITE;
/*!40000 ALTER TABLE `item_discord_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_discord_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_servers`
--

DROP TABLE IF EXISTS `item_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_servers` (
  `type` tinyint(4) NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `cmd_id` bigint(20) unsigned DEFAULT NULL,
  `server_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `item_servers_server_id_foreign` (`server_id`),
  KEY `item_servers_cmd_id_foreign` (`cmd_id`),
  CONSTRAINT `item_servers_cmd_id_foreign` FOREIGN KEY (`cmd_id`) REFERENCES `commands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_servers_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_servers`
--

LOCK TABLES `item_servers` WRITE;
/*!40000 ALTER TABLE `item_servers` DISABLE KEYS */;
INSERT INTO `item_servers` VALUES
(0,27,95,1,NULL,NULL),
(0,34,102,1,NULL,NULL),
(0,35,103,1,NULL,NULL),
(0,19,119,2,NULL,NULL),
(0,20,120,2,NULL,NULL),
(0,1,121,2,NULL,NULL),
(0,1,122,2,NULL,NULL),
(0,2,123,2,NULL,NULL),
(0,2,124,2,NULL,NULL),
(0,3,125,2,NULL,NULL),
(0,3,126,2,NULL,NULL),
(0,4,127,2,NULL,NULL),
(0,4,128,2,NULL,NULL),
(0,5,129,2,NULL,NULL),
(0,5,130,2,NULL,NULL),
(0,6,131,2,NULL,NULL),
(0,6,132,2,NULL,NULL),
(0,7,133,2,NULL,NULL),
(0,7,134,2,NULL,NULL),
(0,8,135,2,NULL,NULL),
(0,8,136,2,NULL,NULL),
(0,9,137,2,NULL,NULL),
(0,9,138,2,NULL,NULL),
(0,10,139,2,NULL,NULL),
(0,10,140,2,NULL,NULL),
(0,11,141,2,NULL,NULL),
(0,11,142,2,NULL,NULL),
(0,12,143,2,NULL,NULL),
(0,12,144,2,NULL,NULL),
(0,13,145,2,NULL,NULL),
(0,13,146,2,NULL,NULL),
(0,14,147,2,NULL,NULL),
(0,14,148,2,NULL,NULL),
(0,15,149,2,NULL,NULL),
(0,15,150,2,NULL,NULL),
(0,16,151,2,NULL,NULL),
(0,16,152,2,NULL,NULL),
(0,17,153,2,NULL,NULL),
(0,17,154,2,NULL,NULL),
(0,18,155,2,NULL,NULL),
(0,18,156,2,NULL,NULL),
(0,21,157,4,NULL,NULL),
(0,22,159,1,NULL,NULL);
/*!40000 ALTER TABLE `item_servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_vars`
--

DROP TABLE IF EXISTS `item_vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_vars` (
  `item_id` bigint(20) unsigned NOT NULL,
  `var_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`item_id`,`var_id`),
  KEY `item_vars_var_id_foreign` (`var_id`),
  CONSTRAINT `item_vars_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_vars_var_id_foreign` FOREIGN KEY (`var_id`) REFERENCES `vars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_vars`
--

LOCK TABLES `item_vars` WRITE;
/*!40000 ALTER TABLE `item_vars` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_vars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `discount` double NOT NULL DEFAULT 0,
  `virtual_price` double DEFAULT NULL,
  `giftcard_price` double NOT NULL DEFAULT 0,
  `description` longtext NOT NULL,
  `expireAfter` int(11) NOT NULL DEFAULT 0,
  `expireUnit` tinyint(4) NOT NULL DEFAULT 0,
  `publishAt` datetime DEFAULT NULL,
  `showUntil` datetime DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `sorting` int(11) NOT NULL DEFAULT 1,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `req_type` tinyint(4) NOT NULL DEFAULT 1,
  `required_items` varchar(100) DEFAULT NULL,
  `featured` tinyint(4) NOT NULL DEFAULT 0,
  `is_subs` tinyint(4) NOT NULL DEFAULT 0,
  `chargePeriodValue` int(11) NOT NULL DEFAULT 1,
  `chargePeriodUnit` int(11) NOT NULL DEFAULT 3,
  `is_virtual_currency_only` tinyint(4) NOT NULL DEFAULT 0,
  `is_any_price` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `is_server_choice` tinyint(4) NOT NULL DEFAULT 0,
  `item_id` varchar(45) DEFAULT NULL,
  `item_lore` longtext DEFAULT NULL,
  `quantityUserLimit` int(11) DEFAULT NULL,
  `quantityUserPeriodValue` int(11) NOT NULL DEFAULT -1,
  `quantityUserPeriodUnit` int(11) NOT NULL DEFAULT 0,
  `quantityGlobalLimit` int(11) DEFAULT NULL,
  `quantityGlobalPeriodUnit` int(11) NOT NULL DEFAULT 0,
  `quantityGlobalPeriodValue` int(11) NOT NULL DEFAULT -1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES
(1,'Ученик [30 ДНЕЙ]','1.png',119,0,NULL,0,'<p>После покупки вы получаете привилегию \"Ученик\" на срок 30 дней на Магическом выживании.</p><p>Возможности:</p><p>Количество /sethome: 3</p><p>Количество мест в аукционе: 6</p><p>Открыть верстак: /wb</p><p>Отключить телепортацию: /tptoggle</p><p>Открыть меню улучшения снаряжения: /smithtable</p>',0,4,NULL,NULL,2,0,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:28:52','2025-02-19 13:39:04'),
(2,'Ученик [НАВСЕГДА]','2.png',179,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Ученик\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 3</p><p>Количество мест в аукционе: 6</p><p>Открыть верстак: /wb</p><p>Отключить телепортацию: /tptoggle</p><p>Открыть меню улучшения снаряжения: /smithtable</p>',0,4,NULL,NULL,2,3,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:32:06','2025-02-19 13:39:22'),
(3,'Воин [30 ДНЕЙ]','3.png',299,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Воин\" на срок 30 дней на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 4</p><p>Количество мест в аукционе: 7</p><p>Возможности предыдущих привилегий</p><p>Телепортироваться обратно: /back</p><p>Утилизация предметов: /trash</p><p>Открыть эндер сундук: /ec</p><p>Открыть наковальню: /anvil</p>',0,4,NULL,NULL,2,4,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:35:21','2025-02-19 13:39:36'),
(4,'Воин [НАВСЕГДА]','4.png',449,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Воин\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 4</p><p>Количество мест в аукционе: 7</p><p>Возможности предыдущих привилегий</p><p>Телепортироваться обратно: /back</p><p>Утилизация предметов: /trash</p><p>Открыть эндер сундук: /ec</p><p>Открыть наковальню: /anvil</p>',0,4,NULL,NULL,2,5,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:36:15','2025-02-19 13:40:06'),
(5,'Мастер [30 ДНЕЙ]','5.png',599,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Мастер\" на срок 30 дней на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 5</p><p>Количество мест в аукционе: 8</p><p>Возможности предыдущих привилегий</p><p>Очистить чат для себя: /clearchat</p><p>Восстановить голод: /feed</p><p>Изменить время для себя: /ptime</p><p>Изменить погоду для себя: /pweather</p><p>Включить ночное видение: /nv</p>',0,4,NULL,NULL,2,6,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:38:15','2025-02-19 13:40:25'),
(6,'Мастер [НАВСЕГДА]','6.png',899,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Мастер\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 5</p><p>Количество мест в аукционе: 8</p><p>Возможности предыдущих привилегий</p><p>Очистить чат для себя: /clearchat</p><p>Восстановить голод: /feed</p><p>Изменить время для себя: /ptime</p><p>Изменить погоду для себя: /pweather</p><p>Включить ночное видение: /nv</p>',0,4,NULL,NULL,2,7,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:39:40','2025-02-19 13:40:55'),
(7,'Царь [30 ДНЕЙ]','7.png',899,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Царь\" на срок 30 дней на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 6</p><p>Количество мест в аукционе: 9</p><p>Возможности предыдущих привилегий</p><p>Починить предмет: /repair</p><p>Найти игроков поблизости: /near</p><p>Посмотреть сколько время: /time</p><p>Посмотреть список игроков на сервере: /list</p><p>Изменить ник: /nick</p>',0,4,NULL,NULL,2,8,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:41:21','2025-02-19 13:41:07'),
(8,'Царь [НАВСЕГДА]','8.png',1349,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Царь\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 6</p><p>Количество мест в аукционе: 9</p><p>Возможности предыдущих привилегий</p><p>Починить предмет: /repair</p><p>Найти игроков поблизости: /near</p><p>Посмотреть сколько время: /time</p><p>Посмотреть список игроков на сервере: /list</p><p>Изменить ник: /nick</p>',0,4,NULL,NULL,2,9,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:43:01','2025-02-19 13:41:21'),
(9,'Повелитель [30 ДНЕЙ]','9.png',1899,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Повелитель\" на срок 30 дней на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 7</p><p>Количество мест в аукционе: 10</p><p>Возможности предыдущих привилегий</p><p>Вылечить себя: /heal</p><p>Починить все предметы: /repair all</p><p>Надеть блок на голову: /hat</p><p>Покормить другого игрока: /feed (ник)</p><p>Изменить цвет ника: /gradientplus name (цвет)</p><p>Изменить цвет сообщений в чате: /gradientplus chat (цвет)</p>',0,4,NULL,NULL,2,10,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:44:14','2025-02-19 13:41:37'),
(10,'Повелитель [НАВСЕГДА]','10.png',2749,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Повелитель\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 7</p><p>Количество мест в аукционе: 10</p><p>Возможности предыдущих привилегий</p><p>Вылечить себя: /heal</p><p>Починить все предметы: /repair all</p><p>Надеть блок на голову: /hat</p><p>Покормить другого игрока: /feed (ник)</p><p>Изменить цвет ника: /gradientplus name (цвет)</p><p>Изменить цвет сообщений в чате: /gradientplus chat (цвет)</p>',0,4,NULL,NULL,2,11,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:45:58','2025-02-19 13:41:57'),
(11,'Бог [30 ДНЕЙ]','11.png',6949,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Бог\" на срок 30 дней на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 10</p><p>Количество мест в аукционе: 15</p><p>Возможности предыдущих привилегий</p><p>Вылечить другого игрока: /heal (ник)</p><p>Включить режим полёта: /fly</p><p>Включить режим бессмертия: /god</p><p>Включить полную невидимость: /v</p><p>Телепортироваться к игроку: /tp</p><p>Изменить градиентный цвет ника: /gradientplus name (градиент)</p><p>Изменить градиентный цвет сообщений в чате: /gradientplus chat (градиент)</p>',0,4,NULL,NULL,2,12,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:47:29','2025-02-19 13:42:08'),
(12,'Бог [НАВСЕГДА]','12.png',9749,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"Бог\" навсегда на Магическом выживании.</span></p><p>Возможности:</p><p>Количество /sethome: 10</p><p>Количество мест в аукционе: 15</p><p>Возможности предыдущих привилегий</p><p>Вылечить другого игрока: /heal (ник)</p><p>Включить режим полёта: /fly</p><p>Включить режим бессмертия: /god</p><p>Включить полную невидимость: /v</p><p>Телепортироваться к игроку: /tp</p><p>Изменить градиентный цвет ника: /gradientplus name (градиент)</p><p>Изменить градиентный цвет сообщений в чате: /gradientplus chat (градиент)</p>',0,4,NULL,NULL,2,13,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:49:19','2025-02-19 13:42:26'),
(13,'500 Алмазов','13.png',100,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 500 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,13,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:56:07','2025-02-19 13:47:28'),
(14,'1250 Алмазов [БОНУС - 250 алмазов]','14.png',250,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 1500 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,14,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 12:57:21','2025-02-19 13:47:46'),
(15,'2500 Алмазов [БОНУС - 500 Алмазов]','15.png',500,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 3000 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,15,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:03:58','2025-02-19 13:47:57'),
(16,'5000 Алмазов [БОНУС - 1000 Алмазов]','16.png',1000,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 6000 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,16,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:04:54','2025-02-19 13:48:08'),
(17,'7500 Алмазов [БОНУС - 1000 Алмазов]','17.png',1500,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 8500 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,17,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:05:50','2025-02-19 13:48:19'),
(18,'10000 Алмазов [БОНУС - 1000 Алмазов]','18.png',2000,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете 11000 алмазов на всех режимах.</span></p>',0,4,NULL,NULL,3,18,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:06:34','2025-02-19 13:48:37'),
(19,'Размут','19.png',99,0,NULL,0,'<p>После покупки вы получаете услугу \"Размут\" на Магическом выживании.</p>',0,4,NULL,NULL,5,19,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:10:53','2025-05-13 11:32:16'),
(20,'Разбан','20.png',199,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете услугу \"Разбан\" на Магическом выживании. </span></p>',0,4,NULL,NULL,5,20,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:11:17','2025-05-13 11:32:16'),
(21,'Размут','19.png',99,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете услугу \"Размут\" на Бедрок выживании. </span></p>',0,4,NULL,NULL,7,0,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:11:20','2025-02-19 13:47:04'),
(22,'Разбан','20.png',199,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете услугу \"Разбан\" на Бедрок выживании. </span></p>',0,4,NULL,NULL,7,1,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:14:58','2025-02-19 13:47:12'),
(23,'VIP [30 ДНЕЙ]','23.png',99,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"VIP\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit vip - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /workbench - Верстак</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /hat - Надеть предмет на голову</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /gstone - Точило</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /loom - Ткацкий станок</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ctable - Стол картографа</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /scutter - Камнерез</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /stable - Стол кузнеца</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /anvil - Наковальня</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [15] Слотов на аукционе, [5] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [5] Регионов привата по 500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$200]</p>',0,4,NULL,NULL,8,20,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:19:41','2025-02-19 13:43:35'),
(24,'VIP [НАВСЕГДА]','24.png',299,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"VIP\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit vip - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /workbench - Верстак</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /hat - Надеть предмет на голову</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /gstone - Точило</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /loom - Ткацкий станок</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ctable - Стол картографа</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /scutter - Камнерез</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /stable - Стол кузнеца</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /anvil - Наковальня</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [15] Слотов на аукционе, [5] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [5] Регионов привата по 500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$200]</p>',0,4,NULL,NULL,8,21,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:20:25','2025-02-19 13:43:47'),
(25,'PREMIUM [30 ДНЕЙ]','25.png',299,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"PREMIUM\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit premium - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ptime - Установить личное время</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /pweather - Установить личную погоду</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ext - Потушить себя</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ec - Эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /clear - Очистить инвентарь</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /seen [ник]- Когда игрок был в сети</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rtp - Телепорт в нижний мир</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [25] Слотов на аукционе, [7] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [8] Регионов привата по 600.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.2</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$400]</p>',0,4,NULL,NULL,8,22,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:21:49','2025-02-19 13:44:01'),
(26,'PREMIUM [НАВСЕГДА]','26.png',599,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"PREMIUM\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit premium - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ptime - Установить личное время</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /pweather - Установить личную погоду</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ext - Потушить себя</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ec - Эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /clear - Очистить инвентарь</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /seen [ник]- Когда игрок был в сети</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rtp - Телепорт в нижний мир</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [25] Слотов на аукционе, [7] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [8] Регионов привата по 600.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.2</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$400]</p>',0,4,NULL,NULL,8,23,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:23:54','2025-02-19 13:44:22'),
(27,'DELUXE [30 ДНЕЙ]','27.png',799,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"DELUXE\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit deluxe - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rg setpriority - Изменить приоритет региона</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /feed - Утолить голод</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /near - Посмотреть игроков рядом</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /dback - Вернуться на место смерти</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /notarget - Сделать себя невидимым для мобов</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /sun /rain /storm [ник]- Изменить погоду</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /setwarp - Установить варп</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rtp - Телепорт в мир энда</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /clan create - Создать клан</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступный флаг fall-damage в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать цветным текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [30] Слотов на аукционе, [10] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [12] Регионов привата по 800.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.3</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [1] Варп доступен для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$600]</p>',0,4,NULL,NULL,8,24,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:25:41','2025-02-19 13:44:38'),
(28,'DELUXE [НАВСЕГДА]','28.png',1599,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"DELUXE\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit deluxe - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rg setpriority - Изменить приоритет региона</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /feed - Утолить голод</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /near - Посмотреть игроков рядом</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /dback - Вернуться на место смерти</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /notarget - Сделать себя невидимым для мобов</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /sun /rain /storm [ник]- Изменить погоду</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /setwarp - Установить варп</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /rtp - Телепорт в мир энда</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /clan create - Создать клан</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступный флаг fall-damage в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать цветным текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [30] Слотов на аукционе, [10] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [12] Регионов привата по 800.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.3</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [1] Варп доступен для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$600]</p>',0,4,NULL,NULL,8,25,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:26:29','2025-02-19 13:44:53'),
(29,'ULTIMATE [30 ДНЕЙ]','29.png',1099,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"ULTIMATE\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit ultimate - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /heal - Вылечить себя</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /repair - Починить вещь в руке</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /itemname - Изменить название предмета</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /itemlore - Изменить описание предмета</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /day /night - Изменить время суток</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /inv [ник] - Посмотреть чужой инвентарь</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ec [ник] - Посмотреть чужой эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать &oкурсивом текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [35] Слотов на аукционе, [12] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [16] Регионов привата по 900.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.4</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [2] Варпа доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$800]</p>',0,4,NULL,NULL,8,26,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:27:40','2025-02-19 13:45:07'),
(30,'ULTIMATE [НАВСЕГДА]','30.png',2099,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"ULTIMATE\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit ultimate - Набор (Каждые 15 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /heal - Вылечить себя</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /repair - Починить вещь в руке</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /itemname - Изменить название предмета</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /itemlore - Изменить описание предмета</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /day /night - Изменить время суток</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /inv [ник] - Посмотреть чужой инвентарь</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /ec [ник] - Посмотреть чужой эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>&nbsp;➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать &oкурсивом текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [35] Слотов на аукционе, [12] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [16] Регионов привата по 900.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.4</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [2] Варпа доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$800]</p>',0,4,NULL,NULL,8,27,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:28:41','2025-02-19 13:45:23'),
(31,'SPECIAL [30 ДНЕЙ]','31.png',1499,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"SPECIAL\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit special - Набор (Каждый 21 день)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /vanish - Режим невидимки</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /god - Режим бессмертия</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /fly - Включить полёт</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /heal [ник] - Вылечить игрока</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /feed [ник] - Покормить игрока</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /repair [ник]- Починить игроку вещи</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступный флаг entry в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать &lжирным текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Отсутствует задержка в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [40] Слотов на аукционе, [15] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [25] Регионов привата по 1.100.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.5</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [3] Варпа доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$1000]</p>',0,4,NULL,NULL,8,28,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:30:53','2025-02-19 13:45:33'),
(32,'SPECIAL [НАВСЕГДА]','32.png',2999,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"SPECIAL\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit special - Набор (Каждый 21 день)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /vanish - Режим невидимки</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /god - Режим бессмертия</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /fly - Включить полёт</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /heal [ник] - Вылечить игрока</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /feed [ник] - Покормить игрока</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /repair [ник]- Починить игроку вещи</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступный флаг entry в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать &lжирным текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Отсутствует задержка в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [40] Слотов на аукционе, [15] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [25] Регионов привата по 1.100.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.5</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [3] Варпа доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$1000]</p>',0,4,NULL,NULL,8,29,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:31:36','2025-02-19 13:45:50'),
(33,'PRIVATE [30 ДНЕЙ]','33.png',2299,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"PRIVATE\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit private - Набор (Каждые 30 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Косметика - Питомцы (Доступны все питомцы)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /top - Телепорт вверх сквозь блоки</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступны большинство флагов в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать любым текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [50] Слотов на аукционе, [30] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [35] Регионов привата по 1.500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.7</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [4] Варпов доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$1500]</p>',0,4,NULL,NULL,8,30,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:32:53','2025-02-19 13:46:02'),
(34,'PRIVATE [НАВСЕГДА]','34.png',4499,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"PRIVATE\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit private - Набор (Каждые 30 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Косметика - Питомцы (Доступны все питомцы)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /top - Телепорт вверх сквозь блоки</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Доступны большинство флагов в вашем регионе</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Возможность писать любым текстом в чате</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [50] Слотов на аукционе, [30] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [35] Регионов привата по 1.500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт х1.7</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [4] Варпов доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$1500]</p>',0,4,NULL,NULL,8,31,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:42:54','2025-02-19 13:46:15'),
(35,'LIMITED [30 ДНЕЙ]','35.png',4199,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"LIMITED\" на срок 30 дней на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit limited - Набор (Каждые 30 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /back - Вернуться на предыдущее место</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /flyspeed - Скорость полёта</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /walkspeed - Эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /glow - Подсветка персонажа</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [100] Слотов на аукционе, [∞] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [40] Регионов привата по 2.500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт x2.0</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [5] Варпов доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$2000]</p>',0,4,NULL,NULL,8,32,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:44:24','2025-02-19 13:46:25'),
(36,'LIMITED [НАВСЕГДА]','36.png',8199,0,NULL,0,'<p><span style=\"background-color: rgb(50, 50, 50);\">После покупки вы получаете привилегию \"LIMITED\" навсегда на Бедрок выживании.</span></p><p>➥ Доступные команды</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /kit limited - Набор (Каждые 30 дней)</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /back - Вернуться на предыдущее место</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /flyspeed - Скорость полёта</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /walkspeed - Эндер-сундук</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» /glow - Подсветка персонажа</p><p>&nbsp;&nbsp;&nbsp;&nbsp;</p><p>➥ Игра</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [100] Слотов на аукционе, [∞] точек дома</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [40] Регионов привата по 2.500.000 блоков</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Множитель работ на опыт x2.0</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» [5] Варпов доступно для создания</p><p>&nbsp;&nbsp;&nbsp;&nbsp;» Зарплата каждые 30 минут [$2000]</p>',0,4,NULL,NULL,8,33,0,1,'',0,0,1,3,0,0,1,0,0,NULL,NULL,0,0,-1,0,-1,0,'2025-02-18 13:46:00','2025-02-19 13:46:42'),
(37,'[DELETED] test',NULL,4,0,NULL,0,'<p><br></p>',0,4,NULL,NULL,5,0,0,1,'',0,0,1,3,0,0,1,1,0,NULL,NULL,0,0,-1,0,-1,0,'2025-05-13 11:31:51','2025-05-13 11:59:37');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` longtext NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2023_08_22_112705_create_site_visits_table',1),
(2,'2023_08_29_193605_create_admins_table',1),
(3,'2023_08_29_194200_create_adverts_table',1),
(4,'2023_08_29_195055_create_auth_game_table',1),
(5,'2023_08_29_195406_create_bans_table',1),
(6,'2023_08_29_203043_create_users_table',1),
(7,'2023_08_29_203733_create_gifts_table',1),
(8,'2023_08_29_204335_create_coupons_table',1),
(9,'2023_08_29_210438_create_carts_table',1),
(10,'2023_08_30_085225_create_items_table',1),
(11,'2023_08_30_091240_create_cart_items_table',1),
(12,'2023_08_30_091727_create_categories_table',1),
(13,'2023_08_30_092948_create_payments_table',1),
(14,'2023_08_30_093648_create_chargebacks_table',1),
(15,'2023_08_30_093910_create_commands_history_table',1),
(16,'2023_08_30_093911_create_servers_table',1),
(17,'2023_08_30_093912_create_cmd_queue_table',1),
(18,'2023_08_30_094323_create_coupon_apply_table',1),
(19,'2023_08_30_094546_create_currencies_table',1),
(20,'2023_08_30_095253_create_global_cmds_table',1),
(21,'2023_08_30_095535_create_links_table',1),
(22,'2023_08_30_095911_create_pages_table',1),
(23,'2023_08_30_101733_create_payment_methods_table',1),
(24,'2023_08_30_103434_create_playerdata_table',1),
(25,'2023_08_30_111213_create_ref_cmd_table',1),
(26,'2023_08_30_111314_create_ref_codes_table',1),
(27,'2023_08_30_120336_create_sales_table',1),
(28,'2023_08_30_120624_create_sale_apply_table',1),
(29,'2023_08_30_121336_create_settings_table',1),
(30,'2023_08_30_133350_create_subscriptions_table',1),
(31,'2023_08_30_133622_create_taxes_table',1),
(32,'2023_08_30_133806_create_themes_table',1),
(33,'2023_08_30_134454_create_vars_table',1),
(34,'2023_08_30_134817_create_whitelist_table',1),
(35,'2023_09_10_083427_create_promoted_items_table',1),
(36,'2023_09_25_142350_create_notifications_table',1),
(37,'2023_10_01_111114_create_commands_table',1),
(38,'2023_10_24_114044_create_cart_item_vars_table',1),
(39,'2023_10_24_114052_create_cart_select_servers_table',1),
(40,'2023_10_24_124657_create_item_servers_table',1),
(41,'2023_10_24_125019_create_item_vars_table',1),
(42,'2023_10_24_131036_create_required_items_table',1),
(43,'2023_11_28_143453_create_donation_goals_table',1),
(44,'2023_11_28_233131_create_security_logs_table',1),
(45,'2024_01_05_143207_create_comparisons_table',1),
(46,'2024_01_05_143220_create_item_comparison_table',1),
(47,'2024_01_28_181126_create_jobs_table',1),
(48,'2024_06_11_212957_create_sales_commands_table',1),
(49,'2024_07_10_114011_add_initial_variable_price_to_cart_items_table',1),
(50,'2024_07_13_194458_add_cmd_id_to_items_server_table',1),
(51,'2024_07_14_011650_create_failed_jobs_table',1),
(52,'2024_07_27_215939_add_virtual_currency_to_cart_items_table',1),
(53,'2024_07_28_192958_add_categories_level_to_settings_table',1),
(54,'2024_07_28_231727_add_processed_to_sales_table',1),
(55,'2024_07_29_163238_add_required_items_to_items_table',1),
(56,'2024_07_29_170526_add_developer_mode_to_settings_table',1),
(57,'2024_08_22_213401_add_customer_to_subscriptions_table',1),
(58,'2024_09_12_141621_update_column_type_in_pages',1),
(59,'2024_12_06_002359_add_youtube_link_to_settings_table',1),
(60,'2024_12_21_171800_add_internal_id_to_payments_table',1),
(61,'2025_01_17_152611_add_discord_data_to_settings_table',1),
(62,'2025_01_18_160328_create_discord_roles_table',1),
(63,'2025_01_19_0160915_create_item_discord_roles_table',1),
(64,'2025_01_19_150915_create_discord_role_queue_table',1),
(65,'2025_01_20_010923_add_discord_id_to_users_table',1),
(66,'2025_01_20_171512_add_discord_sync_to_carts_table',1),
(67,'2025_01_21_211022_add_discord_id_table_to_payments_table',1),
(68,'2025_01_22_001329_add_delayed_timestamps_to_donation_goals_table',1),
(69,'2025_02_25_180136_rename_owner_to_user_id_in_gifts_table',2),
(70,'2025_02_25_191947_add_payment_id_to_gifts_table',2),
(71,'2025_03_16_163149_add_patrons_to_settings_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES
('0ae71856-243d-406b-8695-712136dfcca2','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #164\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/164\"}',NULL,'2025-05-12 11:50:39','2025-05-12 11:50:39'),
('26b98368-73e2-4ca5-9584-d7fa5ec49e6e','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #164\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/164\"}','2025-05-13 09:32:22','2025-05-12 11:50:39','2025-05-13 09:32:22'),
('3740c2a8-bc12-4ebf-a816-d617d7067d7a','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #167\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/167\"}','2025-05-13 09:32:24','2025-05-12 12:11:18','2025-05-13 09:32:24'),
('63449edf-61d3-4fbc-8be4-3c7ff18ead6f','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #178\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (4 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/178\"}','2025-05-14 18:32:32','2025-05-13 11:34:45','2025-05-14 18:32:32'),
('7483f34a-e2d1-4bdf-997e-f2249457379b','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #165\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/165\"}','2025-05-13 09:32:22','2025-05-12 11:56:18','2025-05-13 09:32:22'),
('7a759183-5fec-4ea0-9448-9f7e43292899','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #172\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/172\"}','2025-05-13 09:32:21','2025-05-13 09:28:48','2025-05-13 09:32:21'),
('7aacb922-ff03-47e9-bb40-03fb6a54c41a','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #170\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/170\"}',NULL,'2025-05-12 11:43:29','2025-05-12 11:43:29'),
('7f9f7740-4d6c-449e-ad9e-b3ba7dde08b0','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #172\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/172\"}','2025-05-13 09:32:23','2025-05-13 09:28:48','2025-05-13 09:32:23'),
('88bc6b49-8a67-40fb-8a56-77d8af9b7716','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #169\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/169\"}',NULL,'2025-05-12 11:23:05','2025-05-12 11:23:05'),
('96b3fb9b-05a9-4153-bba2-905379f611e8','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #168\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/168\"}','2025-05-13 09:32:22','2025-05-12 12:21:47','2025-05-13 09:32:22'),
('b2180af4-af43-4c4b-94fe-f3fb3ace72c0','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #167\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/167\"}','2025-05-13 09:32:22','2025-05-12 12:11:18','2025-05-13 09:32:22'),
('bc0d8500-2237-4e12-9ca8-625c164ac7fe','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #173\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/173\"}',NULL,'2025-05-13 09:34:20','2025-05-13 09:34:20'),
('c78d5c25-af89-437f-83a6-42ad2c949887','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #177\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/177\"}',NULL,'2025-05-13 11:10:12','2025-05-13 11:10:12'),
('dd3b6147-b92d-4151-9602-2042c9a755d0','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #176\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/176\"}',NULL,'2025-05-13 11:06:02','2025-05-13 11:06:02'),
('e02b5b95-2f78-4fd4-8ca3-a1dfba53c0d3','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #173\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/173\"}',NULL,'2025-05-13 09:34:20','2025-05-13 09:34:20'),
('f6aafcf7-d266-491a-afdb-0453995e6ef3','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #178\",\"description\":\"You got a successful transaction by <b>dronsyy<\\/b> (4 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/178\"}',NULL,'2025-05-13 11:34:45','2025-05-13 11:34:45'),
('feed07e8-9fff-48e8-8961-f0d41563a39d','App\\Notifications\\NewPayment','App\\Models\\Admin',1,'{\"title\":\"New Transaction #166\",\"description\":\"You got a successful transaction by <b>testest<\\/b> (99 RUB).\",\"icon\":\"bx-cart\",\"color\":\"success\",\"link\":\"https:\\/\\/alumenator.net\\/admin\\/payments\\/166\"}',NULL,'2025-05-12 10:08:35','2025-05-12 10:08:35');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT 0,
  `can_subs` tinyint(4) NOT NULL DEFAULT 0,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES
(1,'PayPal',0,0,'{\"test\": \"0\", \"paypal_user\": \"\", \"paypal_password\": \"\", \"paypal_signature\": \"\", \"paypal_currency_code\": \"\"}','2025-02-18 11:50:06','2025-02-18 14:39:56'),
(2,'PayPalIPN',0,1,'{\"test\": \"0\", \"paypal_business\": \"\", \"paypal_currency_code\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(3,'Cordarium',0,0,'{\"server_id\":\"49643002\",\"public_key\":\"pk_api_Br7mASjlLG\",\"secret_key\":\"sk_api_Ke5ipa9C7EjNXR\"}','2025-02-18 11:50:06','2025-02-18 19:27:17'),
(4,'PayPal (Checkout)',0,1,'{\"client_id\": \"\", \"client_secret\": \"\", \"currency\": \"USD\", \"sandbox\": \"0\", \"payment_methods\": [\"card\", \"paypal\"]}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(5,'CoinPayments',0,0,'{\"currency_code\": \"\", \"secret_coinpayments\": \"\", \"merchant_coinpayments\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(6,'G2APay',0,0,'{\"hash\": \"\", \"email\": \"\", \"secret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(7,'Stripe',0,1,'{\"whsec\": \"\", \"public\": \"\", \"private\": \"\", \"payment_methods\": [\"card\"]}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(8,'Terminal3',0,1,'{\"public\": \"\", \"private\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(9,'Mollie',0,0,'{\"apiKey\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(10,'CashFree',0,0,'{\"appId\": \"\", \"secret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(11,'MercadoPago',0,0,'{\"test\": \"0\", \"token\": \"\", \"currency\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(12,'Paytm',0,0,'{\"mid\": \"\", \"mkey\": \"\", \"test\": \"0\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(13,'GoPay',0,1,'{\"goid\": \"\", \"test\": \"0\", \"ClientID\": \"\", \"ClientSecret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(14,'PayTR',0,0,'{\"merchant_id\": \"\", \"merchant_key\": \"\", \"merchant_salt\": \"\", \"currency\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(15,'RazorPay',0,0,'{\"test\": \"0\", \"api_key\": \"\", \"api_secret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(16,'UnitPay',0,0,'{\"id\":\"443693-66c20\",\"key\":\"f3769f148ebfaf2fc1f01cf3a80fa7a5\"}','2025-02-18 11:50:06','2025-05-07 14:46:40'),
(17,'FreeKassa',0,0,'{\"id\": \"\", \"secret\": \"\"}','2025-02-18 11:50:06','2025-02-18 19:27:40'),
(18,'Qiwi',0,0,'{\"public_key\": \"\", \"private_key\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(19,'Enot',0,0,'{\"id\": \"\", \"secret1\": \"\", \"secret2\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(20,'PayU',0,0,'{\"key\": \"\", \"pos_id\": \"\", \"currency\": \"\", \"oauth_id\": \"\", \"oauth_secret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(21,'HotPay',0,0,'{\"sekret\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(22,'InterKassa',0,0,'{\"cashbox_id\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(23,'Coinbase',0,0,'{\"api_key\": \"\", \"webhookSecret\": \"\", \"coinbase_currency\": \"USD\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(24,'PayUIndia',0,0,'{\"key\": \"\", \"salt\": \"\", \"sandbox\": \"0\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(25,'Skrill',0,0,'{\"email\": \"\", \"signature\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(26,'Coinpayments',0,0,'{\"currency\": \"\", \"secret\": \"\", \"merchant\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(27,'Fondy',0,0,'{\"currency\": \"\", \"merchant_id\": \"\", \"password\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(28,'Midtrans',0,0,'{\"serverKey\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(29,'SePay',0,0,'{\"bank\": \"\", \"bank_account\": \"\", \"bank_owner\": \"\", \"paycode_prefix\": \"\", \"webhook_apikey\": \"\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(30,'PhonePe',0,0,'{\"merchant_id\": \"\", \"salt_key\": \"\", \"salt_index\": \"1\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(31,'Virtual Currency',0,0,'{\"currency\": \"QQ\"}','2025-02-18 11:50:06','2025-02-18 11:50:06'),
(32,'TBank',1,0,'{}',NULL,NULL);
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `internal_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `cart_id` bigint(20) unsigned NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `currency` text NOT NULL,
  `ref` int(11) DEFAULT NULL,
  `details` text NOT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `gateway` varchar(255) NOT NULL DEFAULT '',
  `transaction` varchar(255) NOT NULL DEFAULT '',
  `note` text DEFAULT NULL,
  `discord_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_cart_id_foreign` (`cart_id`),
  CONSTRAINT `payments_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,'MS-c9c0a638-bd58-4e39-9220-3e999d7eaaec',1,1,5.4,0,'USD',NULL,'','46.53.251.116','Cordarium','',NULL,NULL,'2025-02-18 14:22:03','2025-02-18 14:22:03'),
(2,'MS-73a318c4-0c5d-4a86-9af1-dd4aa6cc9a44',1,4,1.12,0,'USD',NULL,'','46.53.251.116','Cordarium','',NULL,NULL,'2025-02-18 14:22:43','2025-02-18 14:22:43'),
(3,'MS-c4be4286-07ad-46c5-8f4b-bd0de8a822be',1,5,1.12,0,'USD',NULL,'','46.53.251.116','Cordarium','',NULL,NULL,'2025-02-18 14:36:11','2025-02-18 14:36:11'),
(4,'MS-3221d4dd-4b5e-4808-b72e-7e3dca54fc39',3,3,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.243.40.85','UnitPay','',NULL,NULL,'2025-02-19 13:32:52','2025-02-19 13:32:52'),
(5,'MS-33a6102e-c840-4d42-88ed-8091c983172b',3,12,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.243.40.85','PayPal','',NULL,NULL,'2025-02-19 13:33:41','2025-02-19 13:33:41'),
(6,'MS-3e80edbd-4ebd-42da-90d0-9972bec1ae0f',3,12,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.243.40.85','UnitPay','',NULL,NULL,'2025-02-19 13:33:46','2025-02-19 13:33:46'),
(7,'MS-512ebaad-925e-44fd-b401-35adf2e18090',9,14,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:13:49','2025-02-19 14:13:49'),
(8,'MS-b2aeb7dd-f8c8-4d62-8995-7b1a302adab2',9,15,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:24:14','2025-02-19 14:24:14'),
(9,'MS-47d9cb66-259f-4991-adea-3265c874e1ba',9,16,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:29:15','2025-02-19 14:29:15'),
(10,'MS-3658f4eb-ffe2-4132-89d2-93b15b347ad0',9,18,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:37:27','2025-02-19 14:37:27'),
(11,'MS-18482395-ad6c-4f94-8866-279ee0d4a881',9,19,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:41:44','2025-02-19 14:41:44'),
(12,'MS-988d6dad-c631-4b9d-ae35-c502936e57b0',9,20,119,0,'RUB',NULL,'\"{\\\"email\\\":\\\"bnjmnjessen@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 14:43:52','2025-02-19 14:43:52'),
(13,'MS-a5e5f306-4ad2-443a-adcf-133e1080a6ac',3,11,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.243.40.85','UnitPay','',NULL,NULL,'2025-02-19 15:12:35','2025-02-19 15:12:35'),
(14,'MS-9bc55085-d081-49b2-99fd-6142423a9ad1',11,22,599,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 15:30:15','2025-02-19 15:30:15'),
(15,'MS-73da8ddb-0b8f-4335-8aed-4b5b1e443132',11,22,599,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 15:30:27','2025-02-19 15:30:27'),
(16,'MS-4d1c9798-33db-40e8-81fc-0d49d6049bdb',11,22,599,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 15:30:45','2025-02-19 15:30:45'),
(17,'MS-56a08701-8d1f-4156-a279-e77424fdcdfb',11,22,599,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 15:32:21','2025-02-19 15:32:21'),
(18,'MS-fa080ad3-8bd0-4d43-a7e9-4a0f035beb39',12,23,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"test@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','212.30.36.75','UnitPay','',NULL,NULL,'2025-02-19 15:52:21','2025-02-19 15:52:21'),
(19,'MS-94924481-a17e-4391-b1e8-fd74e02f64da',9,24,179,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.251.116','UnitPay','',NULL,NULL,'2025-02-19 16:35:18','2025-02-19 16:35:18'),
(20,'MS-d66d1984-1fad-42f3-8721-b9b9ae4c0222',11,26,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:26:25','2025-02-19 17:26:25'),
(21,'MS-3d6ec9d4-118e-4ae2-9277-b2466bd25351',11,26,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:26:30','2025-02-19 17:26:30'),
(22,'MS-f0a216be-de71-4786-9cfe-f4c0c13ac21f',11,27,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:30:17','2025-02-19 17:30:17'),
(23,'MS-1e4d61f9-85a3-4cbb-940f-bbb4e8509c80',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:42:26','2025-02-19 17:42:26'),
(24,'MS-d62888f6-683b-4d39-b6b9-40f5f38b089e',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:42:32','2025-02-19 17:42:32'),
(25,'MS-a0ca0b3f-ab77-4b65-b8bd-b32f4a34ea90',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:42:40','2025-02-19 17:42:40'),
(26,'MS-8682f09d-2e68-48e3-a790-f5d4eb78249e',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:42:44','2025-02-19 17:42:44'),
(27,'MS-64646cf4-9bb6-43b1-a41f-3d30a4920bb7',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','UnitPay','',NULL,NULL,'2025-02-19 17:43:00','2025-02-19 17:43:00'),
(28,'MS-16815dcd-0a3b-4dc6-af3a-a88afa630815',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:43:40','2025-02-19 17:43:40'),
(29,'MS-dd587f49-aec3-4d14-9602-7fa05c37903e',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:45:31','2025-02-19 17:45:31'),
(30,'MS-1bae9c77-efa4-424d-8592-6b122b7031d2',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:50:55','2025-02-19 17:50:55'),
(31,'MS-56a0e2f5-763c-4b37-a719-930b4a13a835',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:56:58','2025-02-19 17:56:58'),
(32,'MS-c6b71a23-f766-4f39-a818-f7ff0bd57dfd',11,28,549,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 17:57:50','2025-02-19 17:57:50'),
(33,'MS-8f1249fe-0c85-40b7-978c-292e3f18918c',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 20:14:38','2025-02-19 20:14:38'),
(34,'MS-7fbf6cde-4fec-4fbb-b16a-a897c7a67047',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 20:31:01','2025-02-19 20:31:01'),
(35,'MS-659e5952-1f72-43d1-a6fa-b03eaafb9d93',11,28,449,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 20:31:54','2025-02-19 20:31:54'),
(36,'MS-86b6a419-9900-469e-8b62-de8dc475c758',13,29,250,0,'RUB',NULL,'\"{\\\"email\\\":\\\"larionovamargarita696@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','188.225.123.50','PayPal','',NULL,NULL,'2025-02-19 20:32:49','2025-02-19 20:32:49'),
(37,'MS-c001e841-7eba-4b30-ae34-35f870e7fcee',5,8,318,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.250.196','UnitPay','',NULL,NULL,'2025-02-21 10:43:46','2025-02-21 10:43:46'),
(38,'MS-1cf36f75-94a3-4adc-b3eb-0ac7dcc8d13c',5,42,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','46.53.250.196','UnitPay','',NULL,NULL,'2025-02-21 11:02:54','2025-02-21 11:02:54'),
(39,'MS-cc4f7f5e-98eb-4bc2-827a-84f861d8a25c',27,45,1349,0,'RUB',NULL,'\"{\\\"email\\\":\\\"DYak00@yandex.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','93.181.249.63','UnitPay','',NULL,NULL,'2025-04-22 16:31:14','2025-04-22 16:31:14'),
(40,'MS-7479e4a4-dc8e-42de-a4ef-8cff092823ca',27,46,1349,0,'RUB',NULL,'\"{\\\"email\\\":\\\"DYak00@yandex.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','93.181.249.63','UnitPay','',NULL,NULL,'2025-04-22 17:43:25','2025-04-22 17:43:25'),
(41,'MS-4a1b4175-4d7c-4c2a-89d5-49b2eb70ec31',31,51,1899,0,'RUB',NULL,'\"{\\\"email\\\":\\\"Zentro@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','217.66.156.221','UnitPay','',NULL,NULL,'2025-05-02 14:21:08','2025-05-02 14:21:08'),
(42,'MS-486695a4-2861-40b8-8738-7edb8dee8b2d',40,62,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteika.koteika@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','PayPal','',NULL,NULL,'2025-05-03 18:55:47','2025-05-03 18:55:47'),
(43,'MS-a1409dc1-f303-4c84-8c02-9bcd474521ae',40,62,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteika.koteika@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','UnitPay','',NULL,NULL,'2025-05-03 18:55:48','2025-05-03 18:55:48'),
(44,'MS-55386c5d-75f3-4ace-9baa-428f79a54854',40,63,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteika.koteika@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','PayPal','',NULL,NULL,'2025-05-03 19:39:51','2025-05-03 19:39:51'),
(45,'MS-df72f0ba-8d37-4cc1-a15e-6a7ab1540141',40,63,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteika.koteika@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','UnitPay','',NULL,NULL,'2025-05-03 19:39:53','2025-05-03 19:39:53'),
(46,'MS-c2894f6d-97a7-4277-8257-423ee23141e1',40,65,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteikakoteika81@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','UnitPay','',NULL,NULL,'2025-05-03 19:40:31','2025-05-03 19:40:31'),
(47,'MS-7f1d2b0e-bc9c-4b29-8b46-e39300acc2dd',40,66,100,0,'RUB',NULL,'\"{\\\"email\\\":\\\"koteikakoteika81@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','95.26.118.14','UnitPay','',NULL,NULL,'2025-05-04 09:02:10','2025-05-04 09:02:10'),
(48,'MS-5d5d4a1c-888e-4fcf-8d2c-89e821081011',48,74,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 11:15:34','2025-05-04 11:15:34'),
(49,'MS-1815c6bd-d9e3-47f7-9449-e381b2ef684e',48,75,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','PayPal','',NULL,NULL,'2025-05-04 11:16:58','2025-05-04 11:16:58'),
(50,'MS-3338eca8-30cd-4721-b08b-e5ceabb7c25d',48,75,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 11:17:00','2025-05-04 11:17:00'),
(51,'MS-a2263406-c6cb-420d-b9bf-c94b05949804',48,76,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','PayPal','',NULL,NULL,'2025-05-04 13:22:46','2025-05-04 13:22:46'),
(52,'MS-1eb0a378-93c1-4f3c-9d1c-2e147c541c51',48,76,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 13:22:48','2025-05-04 13:22:48'),
(53,'MS-b8154f5b-240e-418f-8b21-17cc33876af3',48,78,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 13:33:56','2025-05-04 13:33:56'),
(54,'MS-af48520b-65cb-47e7-ac1b-5517a5df919e',48,79,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 13:46:12','2025-05-04 13:46:12'),
(55,'MS-912a857b-456b-42ed-866a-7745652ac7e6',48,80,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kisee.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 13:54:52','2025-05-04 13:54:52'),
(56,'MS-484003a0-bb05-4d57-9395-9ee3c815c62c',48,82,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kiseev.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 13:58:28','2025-05-04 13:58:28'),
(57,'MS-e2c15812-ed3a-45ab-abc2-b2fb720d1427',48,83,299,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kiseev.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','UnitPay','',NULL,NULL,'2025-05-04 14:00:19','2025-05-04 14:00:19'),
(86,'MS-f3328b39-bcc0-4801-8ab6-9d42d2b3bed9',51,84,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 09:30:15','2025-05-10 09:30:15'),
(87,'MS-7b045e09-18fc-49fa-a816-840348625ae8',51,84,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 09:30:25','2025-05-10 09:30:25'),
(88,'MS-b9273de8-c248-47a9-a3d3-52cd3f718ac9',51,84,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 09:31:50','2025-05-10 09:31:50'),
(89,'MS-cb4c7d5c-3915-4970-aa39-c2f874aa1a02',51,84,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 09:32:03','2025-05-10 09:32:03'),
(105,'MS-70fe6919-0e72-4454-9c8f-3e575ecbd0a6',51,84,198,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":198,\\\"quantity\\\":2,\\\"amount\\\":198,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:39:50','2025-05-10 11:39:50'),
(106,'MS-236bf8a0-a00c-4d69-935f-9c77908003c7',51,84,198,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":198,\\\"quantity\\\":2,\\\"amount\\\":198,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:40:06','2025-05-10 11:40:06'),
(107,'MS-33c243f0-060f-4289-b3ed-621d7d2547a9',51,84,198,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":198,\\\"quantity\\\":2,\\\"amount\\\":198,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:41:06','2025-05-10 11:41:06'),
(108,'MS-2e5f310f-3f41-44ce-9965-2fdc383cf933',51,84,198,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":198,\\\"quantity\\\":2,\\\"amount\\\":198,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:41:07','2025-05-10 11:41:07'),
(109,'MS-73279fff-07e7-46e2-b6e8-c4481c952efb',55,88,119,0,'RUB',NULL,'\"{\\\"email\\\":\\\"zcop1289@gmail.com\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0423\\\\u0447\\\\u0435\\\\u043d\\\\u0438\\\\u043a [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":119,\\\"quantity\\\":1,\\\"amount\\\":119,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 11:43:06','2025-05-10 11:43:06'),
(110,'MS-63fccb92-611f-4976-af46-aafbbcdafa57',58,91,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:43:17','2025-05-10 11:43:17'),
(111,'MS-8788e0c6-6219-4557-8e7f-cc0199d2278b',58,93,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:43:46','2025-05-10 11:43:46'),
(112,'MS-a031142c-1139-41a1-bd77-a729ae3a7b6e',58,94,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:44:29','2025-05-10 11:44:29'),
(113,'MS-245f77b3-269e-47a6-90bb-659b639ae3a7',5,44,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','PayPal','',NULL,NULL,'2025-05-10 11:46:02','2025-05-10 11:46:02'),
(114,'MS-86527984-a3a4-4ab5-908f-ef6d44d55488',5,44,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 11:46:05','2025-05-10 11:46:05'),
(115,'MS-bc84e906-263e-457d-bdb8-9dcb1eb9f4f6',51,84,198,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":198,\\\"quantity\\\":2,\\\"amount\\\":198,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:46:11','2025-05-10 11:46:11'),
(116,'MS-bac3f2ab-547a-496f-aa99-b04fa7770558',5,96,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 11:47:00','2025-05-10 11:47:00'),
(117,'MS-53fb8c8b-3145-41e9-b061-9f07f0f520ca',5,97,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 11:48:47','2025-05-10 11:48:47'),
(118,'MS-aa006cfa-5a40-4f29-afc7-ffffcbd5c3b0',51,95,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:50:23','2025-05-10 11:50:23'),
(120,'MS-5d5f9a18-e838-49df-a874-ce670067dde5',51,100,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:58:51','2025-05-10 11:58:51'),
(121,'MS-dbc2e3b6-24fe-4d70-a313-1dc8d1c05ffa',51,100,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 11:59:26','2025-05-10 11:59:26'),
(122,'MS-4d7215c4-7861-4a78-9943-86aa644e75fc',51,100,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 12:00:39','2025-05-10 12:00:39'),
(123,'MS-6cee09a9-6480-41a3-a2f2-53c270e79633',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 12:55:58','2025-05-10 12:55:58'),
(124,'MS-dd95dd07-ac4e-4e6a-b864-9cf6c5120e2c',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:46:22','2025-05-10 13:46:22'),
(125,'MS-1d213b1a-ccd8-4621-9d3d-d78af8ce3c04',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:47:08','2025-05-10 13:47:08'),
(126,'MS-894d4115-2642-45ce-b563-4029de8eb2f3',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:47:22','2025-05-10 13:47:22'),
(127,'MS-9cbdb685-a70e-4196-a3df-a54d1a761029',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:47:31','2025-05-10 13:47:31'),
(128,'MS-7a3e69f8-a4f1-4ef0-a1df-f78440fc8315',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:48:35','2025-05-10 13:48:35'),
(129,'MS-74bf5b84-8d83-4d4b-9e83-95b951b59544',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 13:51:23','2025-05-10 13:51:23'),
(130,'MS-3cb9df31-ec70-443b-86c0-eabe19c2827f',51,100,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 18:22:03','2025-05-10 18:22:03'),
(131,'MS-43aa76fc-ff3c-4c25-b748-b380670ac457',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 18:26:32','2025-05-10 18:26:32'),
(132,'MS-b831d495-0ba0-4f07-9955-81d2bcb06fb2',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-10 18:27:09','2025-05-10 18:27:09'),
(133,'MS-d4020519-8ec8-43b8-b245-7738cbf97355',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','PayPal','',NULL,NULL,'2025-05-10 18:41:59','2025-05-10 18:41:59'),
(134,'MS-01f6762b-4d34-4f44-985f-50c2bd9ddb9a',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.250.248','TBank','',NULL,NULL,'2025-05-10 18:42:00','2025-05-10 18:42:00'),
(155,'MS-e346d3a3-dadb-4cad-8eb6-6aa4f1ee35a4',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-12 08:14:43','2025-05-12 08:14:43'),
(156,'MS-13829511-5919-4ee9-a2fd-4a6d449591f2',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','188.243.237.17','TBank','',NULL,NULL,'2025-05-12 08:17:11','2025-05-12 08:17:11'),
(157,'MS-39740d84-f285-475b-b426-4d4284b8bdca',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.188.137.137','TBank','',NULL,NULL,'2025-05-12 08:37:54','2025-05-12 08:37:54'),
(158,'MS-f203a893-e434-4730-ba79-8ad982afa9ff',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.188.137.137','TBank','',NULL,NULL,'2025-05-12 08:40:38','2025-05-12 08:40:38'),
(159,'MS-d9d8079e-8876-4490-8a12-3ffdb2691519',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.188.137.137','TBank','',NULL,NULL,'2025-05-12 08:40:44','2025-05-12 08:40:44'),
(160,'MS-9c9f5b68-4409-464e-9e37-e038f92fa6c0',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.188.137.137','TBank','',NULL,NULL,'2025-05-12 08:43:45','2025-05-12 08:43:45'),
(161,'MS-496b80cd-3dc5-4544-9c50-816088ecf94f',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.173.166.35','TBank','',NULL,NULL,'2025-05-12 08:53:28','2025-05-12 08:53:28'),
(162,'MS-ce9e10b8-e3c6-4487-a9d5-ae2fdc8c23b6',58,101,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"qwe@qwe.qwe\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0420\\\\u0430\\\\u0437\\\\u043c\\\\u0443\\\\u0442\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.188.137.137','TBank','',NULL,NULL,'2025-05-12 09:04:28','2025-05-12 09:04:28'),
(163,'MS-589d9767-6b63-4cd6-bb5e-43babd5a19da',5,99,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','5.173.166.35','PayPal','',NULL,NULL,'2025-05-12 09:49:38','2025-05-12 09:49:38'),
(171,'MS-546f8806-48c4-41e5-a2e6-8a9c7d9112e9',48,113,250,0,'RUB',NULL,'\"{\\\"email\\\":\\\"kiseev.06@mail.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"1250 \\\\u0410\\\\u043b\\\\u043c\\\\u0430\\\\u0437\\\\u043e\\\\u0432 [\\\\u0411\\\\u041e\\\\u041d\\\\u0423\\\\u0421 - 250 \\\\u0430\\\\u043b\\\\u043c\\\\u0430\\\\u0437\\\\u043e\\\\u0432]\\\",\\\"price\\\":250,\\\"quantity\\\":1,\\\"amount\\\":250,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','92.115.5.222','TBank','',NULL,NULL,'2025-05-13 08:45:22','2025-05-13 08:45:22'),
(174,'MS-aeccc5b8-c937-4c37-95c5-b7c0e378661d',64,117,119,0,'RUB',NULL,'\"{\\\"email\\\":\\\"zentro@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0423\\\\u0447\\\\u0435\\\\u043d\\\\u0438\\\\u043a [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":119,\\\"quantity\\\":1,\\\"amount\\\":119,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','217.66.158.166','PayPal','',NULL,NULL,'2025-05-13 10:42:20','2025-05-13 10:42:20'),
(175,'MS-b450bac2-4968-4b67-84bb-1129b894fb0e',64,117,119,0,'RUB',NULL,'\"{\\\"email\\\":\\\"zentro@bk.ru\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0423\\\\u0447\\\\u0435\\\\u043d\\\\u0438\\\\u043a [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":119,\\\"quantity\\\":1,\\\"amount\\\":119,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','217.66.158.166','TBank','',NULL,NULL,'2025-05-13 10:42:22','2025-05-13 10:42:22'),
(179,'MS-d4a46a9b-a778-4ef8-8fa0-13de2db7aa04',5,121,119,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"\\\\u0423\\\\u0447\\\\u0435\\\\u043d\\\\u0438\\\\u043a [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":119,\\\"quantity\\\":1,\\\"amount\\\":119,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 19:21:50','2025-05-14 19:21:50'),
(180,'MS-ee8076f0-2758-4139-bb4c-e7e671ee4f83',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:08:50','2025-05-14 20:08:50'),
(181,'MS-c4d8775e-0562-42ea-a391-b008948ff4a5',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:08:53','2025-05-14 20:08:53'),
(182,'MS-5048206c-18fd-4898-b4cc-3bcd29af70f9',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:09:33','2025-05-14 20:09:33'),
(183,'MS-5272d965-500d-4d27-a812-1d5d5d375996',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:09:46','2025-05-14 20:09:46'),
(184,'MS-b5ac9793-2689-48bf-9be6-1e320db9ab3f',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:10:14','2025-05-14 20:10:14'),
(185,'MS-2d078dfc-07e7-4449-88dc-4712dfa62b71',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:13:53','2025-05-14 20:13:53'),
(186,'MS-7ee6809c-7e65-4120-a055-5e7453776bf3',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:22:06','2025-05-14 20:22:06'),
(187,'MS-31ac7028-acab-4462-a83f-ab5836727ad0',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:28:18','2025-05-14 20:28:18'),
(188,'MS-37f2e475-5be0-4a36-981e-955528c1d3ad',5,129,99,0,'RUB',NULL,'\"{\\\"email\\\":\\\"corporate@alumenator.net\\\",\\\"fullname\\\":\\\"John Doe\\\",\\\"address1\\\":\\\"123 Main Street\\\",\\\"city\\\":\\\"New York\\\",\\\"region\\\":\\\"NY\\\",\\\"country\\\":\\\"United States\\\",\\\"zipcode\\\":\\\"10001\\\",\\\"items\\\":[{\\\"name\\\":\\\"VIP [30 \\\\u0414\\\\u041d\\\\u0415\\\\u0419]\\\",\\\"price\\\":99,\\\"quantity\\\":1,\\\"amount\\\":99,\\\"paymentMethod\\\":\\\"full_prepayment\\\",\\\"paymentObject\\\":\\\"commodity\\\",\\\"tax\\\":\\\"none\\\",\\\"measurementUnit\\\":\\\"pc\\\"}],\\\"address2\\\":\\\"\\\"}\"','46.53.253.21','TBank','',NULL,NULL,'2025-05-14 20:34:17','2025-05-14 20:34:17');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playerdata`
--

DROP TABLE IF EXISTS `playerdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `playerdata` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `prefix` varchar(255) NOT NULL DEFAULT '',
  `suffix` varchar(255) NOT NULL DEFAULT '',
  `balance` double NOT NULL DEFAULT 0,
  `player_group` varchar(255) NOT NULL DEFAULT '0',
  `sorting` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playerdata_username_unique` (`username`),
  UNIQUE KEY `playerdata_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playerdata`
--

LOCK TABLES `playerdata` WRITE;
/*!40000 ALTER TABLE `playerdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `playerdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promoted_items`
--

DROP TABLE IF EXISTS `promoted_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `promoted_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) unsigned NOT NULL,
  `price` double NOT NULL,
  `order` int(11) NOT NULL DEFAULT 1,
  `is_featured_offer` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `promoted_items_item_id_foreign` (`item_id`),
  CONSTRAINT `promoted_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promoted_items`
--

LOCK TABLES `promoted_items` WRITE;
/*!40000 ALTER TABLE `promoted_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `promoted_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_cmd`
--

DROP TABLE IF EXISTS `ref_cmd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_cmd` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_cmd`
--

LOCK TABLES `ref_cmd` WRITE;
/*!40000 ALTER TABLE `ref_cmd` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_cmd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref_codes`
--

DROP TABLE IF EXISTS `ref_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referer` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `percent` int(11) NOT NULL DEFAULT 0,
  `cmd` tinyint(4) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `commands` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`commands`)),
  `server_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_codes_referer_unique` (`referer`),
  UNIQUE KEY `ref_codes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref_codes`
--

LOCK TABLES `ref_codes` WRITE;
/*!40000 ALTER TABLE `ref_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `required_items`
--

DROP TABLE IF EXISTS `required_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `required_items` (
  `item_id` bigint(20) unsigned NOT NULL,
  `required_item_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`,`required_item_id`),
  KEY `required_items_required_item_id` (`required_item_id`),
  CONSTRAINT `required_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `required_items_required_item_id_foreign` FOREIGN KEY (`required_item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `required_items`
--

LOCK TABLES `required_items` WRITE;
/*!40000 ALTER TABLE `required_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `required_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_apply`
--

DROP TABLE IF EXISTS `sale_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_apply` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `apply_type` varchar(255) NOT NULL,
  `sale_id` bigint(20) unsigned NOT NULL,
  `apply_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_apply_sale_id_foreign` (`sale_id`),
  CONSTRAINT `sale_apply_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_apply`
--

LOCK TABLES `sale_apply` WRITE;
/*!40000 ALTER TABLE `sale_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `discount` double NOT NULL,
  `apply_type` tinyint(4) NOT NULL DEFAULT 0,
  `packages_commands` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`packages_commands`)),
  `min_basket` double NOT NULL DEFAULT 0,
  `start_at` datetime NOT NULL,
  `expire_at` datetime NOT NULL,
  `is_enable` tinyint(4) NOT NULL DEFAULT 1,
  `is_advert` tinyint(4) NOT NULL DEFAULT 0,
  `advert_title` varchar(255) DEFAULT '',
  `advert_description` longtext DEFAULT NULL,
  `button_name` varchar(255) DEFAULT '',
  `button_url` varchar(255) DEFAULT '',
  `processed` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_commands`
--

DROP TABLE IF EXISTS `sales_commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_commands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `command` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_commands_sale_id_foreign` (`sale_id`),
  CONSTRAINT `sales_commands_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_commands`
--

LOCK TABLES `sales_commands` WRITE;
/*!40000 ALTER TABLE `sales_commands` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_commands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_logs`
--

DROP TABLE IF EXISTS `security_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint(20) unsigned NOT NULL,
  `method` tinyint(4) NOT NULL,
  `action` tinyint(4) NOT NULL,
  `action_id` bigint(20) unsigned DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_logs`
--

LOCK TABLES `security_logs` WRITE;
/*!40000 ALTER TABLE `security_logs` DISABLE KEYS */;
INSERT INTO `security_logs` VALUES
(1,1,0,50,NULL,NULL,'2025-02-18 11:50:08','2025-02-18 11:50:08'),
(2,1,0,39,1,NULL,'2025-02-18 11:51:28','2025-02-18 11:51:28'),
(3,1,1,3,NULL,'edited webstore favicon.','2025-02-18 11:53:29','2025-02-18 11:53:29'),
(4,1,1,18,1,NULL,'2025-02-18 11:54:07','2025-02-18 11:54:07'),
(5,1,1,18,1,NULL,'2025-02-18 11:54:10','2025-02-18 11:54:10'),
(6,1,1,18,1,NULL,'2025-02-18 11:55:14','2025-02-18 11:55:14'),
(7,1,1,3,NULL,'edited webstore logo.','2025-02-18 11:59:39','2025-02-18 11:59:39'),
(8,1,0,39,1,NULL,'2025-02-18 12:06:48','2025-02-18 12:06:48'),
(9,1,1,18,1,NULL,'2025-02-18 12:09:54','2025-02-18 12:09:54'),
(10,1,1,18,1,NULL,'2025-02-18 12:10:47','2025-02-18 12:10:47'),
(11,1,0,45,1,NULL,'2025-02-18 12:17:53','2025-02-18 12:17:53'),
(12,1,1,45,1,NULL,'2025-02-18 12:19:27','2025-02-18 12:19:27'),
(13,1,0,45,2,NULL,'2025-02-18 12:20:35','2025-02-18 12:20:35'),
(14,1,0,1,1,'Ученик [30 ДНЕЙ]','2025-02-18 12:28:52','2025-02-18 12:28:52'),
(15,1,0,1,2,'Ученик [НАВСЕГДА]','2025-02-18 12:32:06','2025-02-18 12:32:06'),
(16,1,1,1,1,'Ученик [30 ДНЕЙ]','2025-02-18 12:32:13','2025-02-18 12:32:13'),
(17,1,1,1,2,'Ученик [НАВСЕГДА]','2025-02-18 12:32:23','2025-02-18 12:32:23'),
(18,1,1,3,NULL,'изменён фавикон веб-магазина.','2025-02-18 12:33:29','2025-02-18 12:33:29'),
(19,1,0,1,3,'Воин [30 ДНЕЙ]','2025-02-18 12:35:21','2025-02-18 12:35:21'),
(20,1,0,1,4,'Воин [НАВСЕГДА]','2025-02-18 12:36:15','2025-02-18 12:36:15'),
(21,1,1,1,4,'Воин [НАВСЕГДА]','2025-02-18 12:36:29','2025-02-18 12:36:29'),
(22,1,0,1,5,'Мастер [30 ДНЕЙ]','2025-02-18 12:38:15','2025-02-18 12:38:15'),
(23,1,0,1,6,'Мастер [НАВСЕГДА]','2025-02-18 12:39:40','2025-02-18 12:39:40'),
(24,1,0,1,7,'Царь [30 ДНЕЙ]','2025-02-18 12:41:21','2025-02-18 12:41:21'),
(25,1,0,1,8,'Царь [НАВСЕГДА]','2025-02-18 12:43:01','2025-02-18 12:43:01'),
(26,1,0,1,9,'Повелитель [30 ДНЕЙ]','2025-02-18 12:44:14','2025-02-18 12:44:14'),
(27,1,0,1,10,'Повелитель [НАВСЕГДА]','2025-02-18 12:45:58','2025-02-18 12:45:58'),
(28,1,0,1,11,'Бог [30 ДНЕЙ]','2025-02-18 12:47:29','2025-02-18 12:47:29'),
(29,1,0,1,12,'Бог [НАВСЕГДА]','2025-02-18 12:49:19','2025-02-18 12:49:19'),
(30,1,0,48,1,NULL,'2025-02-18 12:50:42','2025-02-18 12:50:42'),
(31,1,0,45,3,NULL,'2025-02-18 12:55:24','2025-02-18 12:55:24'),
(32,1,0,1,13,'500 Алмазов','2025-02-18 12:56:07','2025-02-18 12:56:07'),
(33,1,0,1,14,'1250 Алмазов [БОНУС - 250 алмазов]','2025-02-18 12:57:21','2025-02-18 12:57:21'),
(34,1,1,1,1,'Ученик [30 ДНЕЙ]','2025-02-18 12:59:27','2025-02-18 12:59:27'),
(35,1,1,45,2,NULL,'2025-02-18 13:00:15','2025-02-18 13:00:15'),
(36,1,1,45,2,NULL,'2025-02-18 13:00:41','2025-02-18 13:00:41'),
(37,1,1,45,2,NULL,'2025-02-18 13:00:57','2025-02-18 13:00:57'),
(38,1,0,1,15,'2500 Алмазов [БОНУС - 500 Алмазов]','2025-02-18 13:03:58','2025-02-18 13:03:58'),
(39,1,0,1,16,'5000 Алмазов [БОНУС - 1000 Алмазов]','2025-02-18 13:04:54','2025-02-18 13:04:54'),
(40,1,0,1,17,'7500 Алмазов [БОНУС - 1000 Алмазов]','2025-02-18 13:05:50','2025-02-18 13:05:50'),
(41,1,0,1,18,'10000 Алмазов [БОНУС - 1000 Алмазов]','2025-02-18 13:06:34','2025-02-18 13:06:34'),
(42,1,0,45,4,NULL,'2025-02-18 13:07:16','2025-02-18 13:07:16'),
(43,1,0,45,5,NULL,'2025-02-18 13:09:25','2025-02-18 13:09:25'),
(44,1,2,45,4,NULL,'2025-02-18 13:10:15','2025-02-18 13:10:15'),
(45,1,0,1,19,'Размут','2025-02-18 13:10:53','2025-02-18 13:10:53'),
(46,1,0,1,20,'Разбан','2025-02-18 13:11:17','2025-02-18 13:11:17'),
(47,1,0,1,21,'duplicated','2025-02-18 13:11:20','2025-02-18 13:11:20'),
(48,1,0,45,6,NULL,'2025-02-18 13:11:37','2025-02-18 13:11:37'),
(49,1,0,45,7,NULL,'2025-02-18 13:11:50','2025-02-18 13:11:50'),
(50,1,1,45,1,NULL,'2025-02-18 13:13:09','2025-02-18 13:13:09'),
(51,1,1,45,1,NULL,'2025-02-18 13:13:18','2025-02-18 13:13:18'),
(52,1,1,45,6,NULL,'2025-02-18 13:14:02','2025-02-18 13:14:02'),
(53,1,1,1,21,'Размут','2025-02-18 13:14:26','2025-02-18 13:14:26'),
(54,1,1,1,21,'Размут','2025-02-18 13:14:37','2025-02-18 13:14:37'),
(55,1,0,1,22,'duplicated','2025-02-18 13:14:58','2025-02-18 13:14:58'),
(56,1,1,1,22,'Разбан','2025-02-18 13:15:11','2025-02-18 13:15:11'),
(57,1,0,45,8,NULL,'2025-02-18 13:16:05','2025-02-18 13:16:05'),
(58,1,1,3,NULL,'включен режим обслуживания.','2025-02-18 13:18:00','2025-02-18 13:18:00'),
(59,1,1,3,NULL,'включен режим обслуживания.','2025-02-18 13:18:29','2025-02-18 13:18:29'),
(60,1,0,1,23,'VIP [30 ДНЕЙ]','2025-02-18 13:19:41','2025-02-18 13:19:41'),
(61,1,0,1,24,'VIP [НАВСЕГДА]','2025-02-18 13:20:25','2025-02-18 13:20:25'),
(62,1,0,1,25,'PREMIUM [30 ДНЕЙ]','2025-02-18 13:21:49','2025-02-18 13:21:49'),
(63,1,0,1,26,'PREMIUM [НАВСЕГДА]','2025-02-18 13:23:54','2025-02-18 13:23:54'),
(64,1,0,1,27,'DELUXE [30 ДНЕЙ]','2025-02-18 13:25:41','2025-02-18 13:25:41'),
(65,1,0,1,28,'DELUXE [НАВСЕГДА]','2025-02-18 13:26:29','2025-02-18 13:26:29'),
(66,1,0,1,29,'ULTIMATE [30 ДНЕЙ]','2025-02-18 13:27:40','2025-02-18 13:27:40'),
(67,1,0,1,30,'ULTIMATE [НАВСЕГДА]','2025-02-18 13:28:41','2025-02-18 13:28:41'),
(68,1,1,3,NULL,'включен режим обслуживания.','2025-02-18 13:30:01','2025-02-18 13:30:01'),
(69,1,1,3,NULL,'включен режим обслуживания.','2025-02-18 13:30:17','2025-02-18 13:30:17'),
(70,1,1,3,NULL,'изменено название веб-магазина.','2025-02-18 13:30:19','2025-02-18 13:30:19'),
(71,1,0,1,31,'SPECIAL [30 ДНЕЙ]','2025-02-18 13:30:53','2025-02-18 13:30:53'),
(72,1,0,1,32,'SPECIAL [НАВСЕГДА]','2025-02-18 13:31:36','2025-02-18 13:31:36'),
(73,1,0,1,33,'PRIVATE [30 ДНЕЙ]','2025-02-18 13:32:53','2025-02-18 13:32:53'),
(74,1,0,1,34,'PRIVATE [НАВСЕГДА]','2025-02-18 13:42:54','2025-02-18 13:42:54'),
(75,1,0,1,35,'LIMITED [30 ДНЕЙ]','2025-02-18 13:44:24','2025-02-18 13:44:24'),
(76,1,0,1,36,'LIMITED [НАВСЕГДА]','2025-02-18 13:46:01','2025-02-18 13:46:01'),
(77,1,1,49,3,'enabled','2025-02-18 14:19:17','2025-02-18 14:19:17'),
(78,1,1,49,3,'disabled','2025-02-18 14:19:19','2025-02-18 14:19:19'),
(79,1,1,49,3,'enabled','2025-02-18 14:21:08','2025-02-18 14:21:08'),
(80,1,1,49,3,'изменил настройки продавца','2025-02-18 14:21:09','2025-02-18 14:21:09'),
(81,1,1,3,NULL,'изменен порт сервера.','2025-02-18 14:32:38','2025-02-18 14:32:38'),
(82,1,1,28,NULL,'edited currency settings','2025-02-18 14:35:48','2025-02-18 14:35:48'),
(83,1,1,28,NULL,'edited currency settings','2025-02-18 14:35:55','2025-02-18 14:35:55'),
(84,1,1,49,1,'enabled','2025-02-18 14:39:55','2025-02-18 14:39:55'),
(85,1,1,49,1,'disabled','2025-02-18 14:39:56','2025-02-18 14:39:56'),
(86,1,1,49,3,'disabled','2025-02-18 15:13:08','2025-02-18 15:13:08'),
(87,1,1,3,NULL,'изменен порт сервера.','2025-02-18 17:47:41','2025-02-18 17:47:41'),
(88,1,1,3,NULL,'изменен IP сервера.','2025-02-18 17:52:08','2025-02-18 17:52:08'),
(89,1,1,3,NULL,'изменен IP сервера.','2025-02-18 17:52:11','2025-02-18 17:52:11'),
(90,1,1,23,NULL,'изменены настройки дискорда','2025-02-18 17:52:35','2025-02-18 17:52:35'),
(91,1,1,23,NULL,'изменены настройки дискорда','2025-02-18 17:52:44','2025-02-18 17:52:44'),
(92,1,1,3,NULL,'изменен порт сервера.','2025-02-18 18:07:26','2025-02-18 18:07:26'),
(93,1,1,3,NULL,'изменен IP сервера.','2025-02-18 18:07:27','2025-02-18 18:07:27'),
(94,1,1,3,NULL,'изменен IP сервера.','2025-02-18 18:52:37','2025-02-18 18:52:37'),
(95,1,1,49,3,'enabled','2025-02-18 19:27:14','2025-02-18 19:27:14'),
(96,1,1,49,3,'disabled','2025-02-18 19:27:17','2025-02-18 19:27:17'),
(97,1,1,49,17,'enabled','2025-02-18 19:27:38','2025-02-18 19:27:38'),
(98,1,1,49,17,'disabled','2025-02-18 19:27:40','2025-02-18 19:27:40'),
(99,1,1,49,16,'enabled','2025-02-19 12:35:13','2025-02-19 12:35:13'),
(100,1,1,49,16,'изменил настройки продавца','2025-02-19 12:36:31','2025-02-19 12:36:31'),
(101,1,1,49,16,'изменил настройки продавца','2025-02-19 12:36:58','2025-02-19 12:36:58'),
(102,1,1,49,16,'disabled','2025-02-19 12:37:47','2025-02-19 12:37:47'),
(103,1,1,49,16,'enabled','2025-02-19 12:37:48','2025-02-19 12:37:48'),
(104,1,1,1,19,'Размут','2025-02-19 13:38:06','2025-02-19 13:38:06'),
(105,1,1,1,20,'Разбан','2025-02-19 13:38:28','2025-02-19 13:38:28'),
(106,1,1,1,1,'Ученик [30 ДНЕЙ]','2025-02-19 13:39:04','2025-02-19 13:39:04'),
(107,1,1,1,2,'Ученик [НАВСЕГДА]','2025-02-19 13:39:22','2025-02-19 13:39:22'),
(108,1,1,1,3,'Воин [30 ДНЕЙ]','2025-02-19 13:39:36','2025-02-19 13:39:36'),
(109,1,1,1,4,'Воин [НАВСЕГДА]','2025-02-19 13:40:07','2025-02-19 13:40:07'),
(110,1,1,1,5,'Мастер [30 ДНЕЙ]','2025-02-19 13:40:25','2025-02-19 13:40:25'),
(111,1,1,1,6,'Мастер [НАВСЕГДА]','2025-02-19 13:40:55','2025-02-19 13:40:55'),
(112,1,1,1,7,'Царь [30 ДНЕЙ]','2025-02-19 13:41:07','2025-02-19 13:41:07'),
(113,1,1,1,8,'Царь [НАВСЕГДА]','2025-02-19 13:41:21','2025-02-19 13:41:21'),
(114,1,1,1,9,'Повелитель [30 ДНЕЙ]','2025-02-19 13:41:37','2025-02-19 13:41:37'),
(115,1,1,1,10,'Повелитель [НАВСЕГДА]','2025-02-19 13:41:57','2025-02-19 13:41:57'),
(116,1,1,1,11,'Бог [30 ДНЕЙ]','2025-02-19 13:42:08','2025-02-19 13:42:08'),
(117,1,1,1,12,'Бог [НАВСЕГДА]','2025-02-19 13:42:26','2025-02-19 13:42:26'),
(118,1,1,1,23,'VIP [30 ДНЕЙ]','2025-02-19 13:43:35','2025-02-19 13:43:35'),
(119,1,1,1,24,'VIP [НАВСЕГДА]','2025-02-19 13:43:47','2025-02-19 13:43:47'),
(120,1,1,1,25,'PREMIUM [30 ДНЕЙ]','2025-02-19 13:44:01','2025-02-19 13:44:01'),
(121,1,1,1,26,'PREMIUM [НАВСЕГДА]','2025-02-19 13:44:22','2025-02-19 13:44:22'),
(122,1,1,1,27,'DELUXE [30 ДНЕЙ]','2025-02-19 13:44:38','2025-02-19 13:44:38'),
(123,1,1,1,28,'DELUXE [НАВСЕГДА]','2025-02-19 13:44:53','2025-02-19 13:44:53'),
(124,1,1,1,29,'ULTIMATE [30 ДНЕЙ]','2025-02-19 13:45:07','2025-02-19 13:45:07'),
(125,1,1,1,30,'ULTIMATE [НАВСЕГДА]','2025-02-19 13:45:23','2025-02-19 13:45:23'),
(126,1,1,1,31,'SPECIAL [30 ДНЕЙ]','2025-02-19 13:45:33','2025-02-19 13:45:33'),
(127,1,1,1,32,'SPECIAL [НАВСЕГДА]','2025-02-19 13:45:50','2025-02-19 13:45:50'),
(128,1,1,1,33,'PRIVATE [30 ДНЕЙ]','2025-02-19 13:46:02','2025-02-19 13:46:02'),
(129,1,1,1,34,'PRIVATE [НАВСЕГДА]','2025-02-19 13:46:15','2025-02-19 13:46:15'),
(130,1,1,1,35,'LIMITED [30 ДНЕЙ]','2025-02-19 13:46:25','2025-02-19 13:46:25'),
(131,1,1,1,36,'LIMITED [НАВСЕГДА]','2025-02-19 13:46:42','2025-02-19 13:46:42'),
(132,1,1,1,21,'Размут','2025-02-19 13:47:04','2025-02-19 13:47:04'),
(133,1,1,1,22,'Разбан','2025-02-19 13:47:12','2025-02-19 13:47:12'),
(134,1,1,1,13,'500 Алмазов','2025-02-19 13:47:28','2025-02-19 13:47:28'),
(135,1,1,1,14,'1250 Алмазов [БОНУС - 250 алмазов]','2025-02-19 13:47:46','2025-02-19 13:47:46'),
(136,1,1,1,15,'2500 Алмазов [БОНУС - 500 Алмазов]','2025-02-19 13:47:57','2025-02-19 13:47:57'),
(137,1,1,1,16,'5000 Алмазов [БОНУС - 1000 Алмазов]','2025-02-19 13:48:08','2025-02-19 13:48:08'),
(138,1,1,1,17,'7500 Алмазов [БОНУС - 1000 Алмазов]','2025-02-19 13:48:19','2025-02-19 13:48:19'),
(139,1,1,1,18,'10000 Алмазов [БОНУС - 1000 Алмазов]','2025-02-19 13:48:37','2025-02-19 13:48:37'),
(140,1,1,49,16,'изменил настройки продавца','2025-02-19 14:13:26','2025-02-19 14:13:26'),
(141,1,1,49,16,'изменил настройки продавца','2025-02-19 14:23:47','2025-02-19 14:23:47'),
(142,1,1,49,16,'изменил настройки продавца','2025-02-19 14:43:30','2025-02-19 14:43:30'),
(143,1,1,49,16,'изменил настройки продавца','2025-02-19 16:37:39','2025-02-19 16:37:39'),
(144,1,1,49,16,'disabled','2025-02-19 17:42:04','2025-02-19 17:42:04'),
(145,1,1,49,16,'enabled','2025-02-21 10:43:27','2025-02-21 10:43:27'),
(146,1,1,49,16,'изменил настройки продавца','2025-02-21 11:02:40','2025-02-21 11:02:40'),
(147,1,1,49,16,'изменил настройки продавца','2025-02-21 11:03:06','2025-02-21 11:03:06'),
(148,1,1,3,NULL,'изменён фавикон веб-магазина.','2025-04-22 12:09:16','2025-04-22 12:09:16'),
(149,1,1,3,NULL,'изменено название веб-магазина.','2025-05-07 13:20:49','2025-05-07 13:20:49'),
(150,1,1,3,NULL,'изменено название веб-магазина.','2025-05-07 13:23:31','2025-05-07 13:23:31'),
(151,1,1,3,NULL,'изменено описание веб-магазина.','2025-05-07 13:23:54','2025-05-07 13:23:54'),
(152,1,0,48,2,NULL,'2025-05-07 13:28:58','2025-05-07 13:28:58'),
(153,1,1,1,19,'Размут','2025-05-07 13:31:36','2025-05-07 13:31:36'),
(154,1,1,1,20,'Разбан','2025-05-07 13:31:55','2025-05-07 13:31:55'),
(155,1,1,1,1,'Ученик [30 ДНЕЙ]','2025-05-07 13:32:58','2025-05-07 13:32:58'),
(156,1,1,1,2,'Ученик [НАВСЕГДА]','2025-05-07 13:33:18','2025-05-07 13:33:18'),
(157,1,1,1,3,'Воин [30 ДНЕЙ]','2025-05-07 13:33:27','2025-05-07 13:33:27'),
(158,1,1,1,4,'Воин [НАВСЕГДА]','2025-05-07 13:33:35','2025-05-07 13:33:35'),
(159,1,1,1,5,'Мастер [30 ДНЕЙ]','2025-05-07 13:33:43','2025-05-07 13:33:43'),
(160,1,1,1,6,'Мастер [НАВСЕГДА]','2025-05-07 13:33:51','2025-05-07 13:33:51'),
(161,1,1,1,7,'Царь [30 ДНЕЙ]','2025-05-07 13:34:00','2025-05-07 13:34:00'),
(162,1,1,1,8,'Царь [НАВСЕГДА]','2025-05-07 13:34:08','2025-05-07 13:34:08'),
(163,1,1,1,9,'Повелитель [30 ДНЕЙ]','2025-05-07 13:34:21','2025-05-07 13:34:21'),
(164,1,1,1,10,'Повелитель [НАВСЕГДА]','2025-05-07 13:34:28','2025-05-07 13:34:28'),
(165,1,1,1,11,'Бог [30 ДНЕЙ]','2025-05-07 13:34:34','2025-05-07 13:34:34'),
(166,1,1,1,12,'Бог [НАВСЕГДА]','2025-05-07 13:34:40','2025-05-07 13:34:40'),
(167,1,1,1,13,'500 Алмазов','2025-05-07 13:34:54','2025-05-07 13:34:54'),
(168,1,1,1,14,'1250 Алмазов [БОНУС - 250 алмазов]','2025-05-07 13:35:01','2025-05-07 13:35:01'),
(169,1,1,1,15,'2500 Алмазов [БОНУС - 500 Алмазов]','2025-05-07 13:35:07','2025-05-07 13:35:07'),
(170,1,1,1,16,'5000 Алмазов [БОНУС - 1000 Алмазов]','2025-05-07 13:35:14','2025-05-07 13:35:14'),
(171,1,1,1,17,'7500 Алмазов [БОНУС - 1000 Алмазов]','2025-05-07 13:35:22','2025-05-07 13:35:22'),
(172,1,1,1,18,'10000 Алмазов [БОНУС - 1000 Алмазов]','2025-05-07 13:35:30','2025-05-07 13:35:30'),
(173,1,1,3,NULL,'изменено название веб-магазина.','2025-05-07 13:36:10','2025-05-07 13:36:10'),
(174,1,1,45,1,NULL,'2025-05-07 13:37:23','2025-05-07 13:37:23'),
(175,1,1,45,6,NULL,'2025-05-07 13:37:44','2025-05-07 13:37:44'),
(176,1,1,45,6,NULL,'2025-05-07 13:38:28','2025-05-07 13:38:28'),
(177,1,1,49,16,'disabled','2025-05-07 14:46:40','2025-05-07 14:46:40'),
(178,1,0,39,1,NULL,'2025-05-07 15:02:00','2025-05-07 15:02:00'),
(179,1,0,39,1,NULL,'2025-05-12 10:01:28','2025-05-12 10:01:28'),
(180,1,1,7,166,'Marked payment as paid. Payment #166','2025-05-12 10:08:37','2025-05-12 10:08:37'),
(181,1,1,7,4,'Resent command (lp user dronsyy parent addtemp vip 30d) for payment #170','2025-05-13 08:26:02','2025-05-13 08:26:02'),
(182,1,1,7,17,'Resent command (lp user dronsyy parent addtemp vip 30d) for payment #170','2025-05-13 08:26:17','2025-05-13 08:26:17'),
(183,1,2,7,19,'Deleted command (unmute dronsyy) from payment #172','2025-05-13 09:31:53','2025-05-13 09:31:53'),
(184,1,2,7,20,'Deleted command (unmute dronsyy) from payment #172','2025-05-13 09:31:55','2025-05-13 09:31:55'),
(185,1,0,48,3,NULL,'2025-05-13 10:56:45','2025-05-13 10:56:45'),
(186,1,1,7,21,'Resent command (unmute dronsyy) for payment #173','2025-05-13 11:01:17','2025-05-13 11:01:17'),
(187,1,0,47,173,NULL,'2025-05-13 11:01:35','2025-05-13 11:01:35'),
(188,1,2,48,3,NULL,'2025-05-13 11:03:14','2025-05-13 11:03:14'),
(189,1,0,48,4,NULL,'2025-05-13 11:08:32','2025-05-13 11:08:32'),
(190,1,1,1,21,'Размут','2025-05-13 11:09:13','2025-05-13 11:09:13'),
(191,1,1,7,177,'Resent all commands for payment #177','2025-05-13 11:11:40','2025-05-13 11:11:40'),
(192,1,0,48,5,NULL,'2025-05-13 11:27:53','2025-05-13 11:27:53'),
(193,1,0,1,37,'test','2025-05-13 11:31:51','2025-05-13 11:31:51'),
(194,1,1,1,37,'test','2025-05-13 11:34:00','2025-05-13 11:34:00'),
(195,1,1,7,33,'Resent command (say done) for payment #178','2025-05-13 11:42:05','2025-05-13 11:42:05'),
(196,1,1,7,35,'Resent command (say done) for payment #178','2025-05-13 11:50:15','2025-05-13 11:50:15'),
(197,1,2,7,33,'Deleted command (say done) from payment #178','2025-05-13 11:52:01','2025-05-13 11:52:01'),
(198,1,2,7,34,'Deleted command (say done) from payment #178','2025-05-13 11:52:03','2025-05-13 11:52:03'),
(199,1,2,7,35,'Deleted command (say done) from payment #178','2025-05-13 11:52:03','2025-05-13 11:52:03'),
(200,1,2,48,4,NULL,'2025-05-13 11:52:13','2025-05-13 11:52:13'),
(201,1,1,7,36,'Resent command (say done) for payment #178','2025-05-13 11:53:50','2025-05-13 11:53:50'),
(202,1,1,48,5,NULL,'2025-05-13 11:54:47','2025-05-13 11:54:47'),
(203,1,2,48,5,NULL,'2025-05-13 11:57:18','2025-05-13 11:57:18'),
(204,1,1,48,1,NULL,'2025-05-13 11:57:22','2025-05-13 11:57:22'),
(205,1,1,48,2,NULL,'2025-05-13 11:57:53','2025-05-13 11:57:53'),
(206,1,2,1,37,NULL,'2025-05-13 11:59:37','2025-05-13 11:59:37'),
(207,1,1,1,22,'Разбан','2025-05-13 12:00:19','2025-05-13 12:00:19'),
(208,1,2,7,166,NULL,'2025-05-13 12:08:57','2025-05-13 12:08:57'),
(209,1,1,3,NULL,'изменен IP сервера.','2025-05-13 12:09:14','2025-05-13 12:09:14'),
(210,1,1,3,NULL,'изменён баннер веб-магазина.','2025-05-13 12:13:14','2025-05-13 12:13:14'),
(211,1,1,3,NULL,'изменён баннер веб-магазина.','2025-05-13 12:13:20','2025-05-13 12:13:20'),
(212,1,1,45,2,NULL,'2025-05-13 12:27:45','2025-05-13 12:27:45'),
(213,1,1,45,8,NULL,'2025-05-13 12:27:58','2025-05-13 12:27:58'),
(214,1,1,3,NULL,'изменён баннер веб-магазина.','2025-05-13 13:29:45','2025-05-13 13:29:45'),
(215,1,1,41,NULL,NULL,'2025-05-14 19:24:09','2025-05-14 19:24:09');
/*!40000 ALTER TABLE `security_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `servers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `method` varchar(40) NOT NULL DEFAULT 'listener',
  `host` varchar(255) NOT NULL DEFAULT '',
  `port` int(11) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL DEFAULT '',
  `host_websocket` varchar(255) NOT NULL DEFAULT '',
  `port_websocket` varchar(255) NOT NULL DEFAULT '',
  `password_websocket` varchar(255) NOT NULL DEFAULT '',
  `secret_key` varchar(255) NOT NULL DEFAULT '',
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servers`
--

LOCK TABLES `servers` WRITE;
/*!40000 ALTER TABLE `servers` DISABLE KEYS */;
INSERT INTO `servers` VALUES
(1,'B-Survival','listener','',0,'','','','','0iwJjKbSwayKbXspKLiZ9ZWPoQnbK082Vt9aWNjpJSuhE9w8Fy',0,'2025-02-18 12:50:42','2025-05-13 11:57:22'),
(2,'M-Survival','listener','',0,'','','','','WEfO5h5iORPtAcxIJjQ4kbqlzeVnAPSo828nyDK1UcItMwKVNA',0,'2025-05-07 13:28:58','2025-05-13 11:57:53'),
(3,'[DELETED] Bedrock Survival','listener','',0,'','','','','deleted-VeFCTQElXm8XCZ6',1,'2025-05-13 10:56:45','2025-05-13 11:03:14'),
(4,'[DELETED] Bedrock Survival','listener','',0,'','','','','deleted-nERreVwPAG8JFmD',1,'2025-05-13 11:08:32','2025-05-13 11:52:13'),
(5,'[DELETED] test','listener','',0,'','','','','deleted-JDF1JV8kLm82H9Y',1,'2025-05-13 11:27:53','2025-05-13 11:57:18');
/*!40000 ALTER TABLE `servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL DEFAULT '',
  `site_desc` varchar(255) NOT NULL,
  `serverIP` varchar(255) NOT NULL DEFAULT '',
  `serverPort` varchar(255) NOT NULL DEFAULT '',
  `withdraw_game` varchar(255) NOT NULL DEFAULT 'minecraft',
  `webhook_url` varchar(255) NOT NULL DEFAULT '',
  `discord_guild_id` text NOT NULL,
  `discord_url` text NOT NULL,
  `discord_bot_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `discord_client_id` varchar(255) DEFAULT NULL,
  `discord_client_secret` varchar(255) DEFAULT NULL,
  `discord_bot_token` varchar(255) DEFAULT NULL,
  `index_content` text NOT NULL,
  `index_deal` text NOT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'en',
  `allow_langs` text NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `allow_currs` text NOT NULL,
  `is_virtual_currency` tinyint(4) NOT NULL DEFAULT 0,
  `virtual_currency` varchar(100) NOT NULL DEFAULT '',
  `virtual_currency_cmd` varchar(250) NOT NULL DEFAULT '',
  `block_1` text NOT NULL,
  `block_2` text NOT NULL,
  `facebook_link` text NOT NULL,
  `instagram_link` text NOT NULL,
  `discord_link` text NOT NULL,
  `twitter_link` text NOT NULL,
  `steam_link` text NOT NULL,
  `tiktok_link` text NOT NULL,
  `youtube_link` text NOT NULL,
  `auth_type` varchar(255) NOT NULL DEFAULT 'username',
  `is_staff_page_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `is_prefix_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `enabled_ranks` text NOT NULL,
  `is_profile_enable` tinyint(4) NOT NULL DEFAULT 1,
  `profile_display_format` varchar(255) NOT NULL DEFAULT '{username}',
  `is_profile_sync` tinyint(4) NOT NULL DEFAULT 0,
  `group_display_format` varchar(255) NOT NULL DEFAULT '{group}',
  `is_group_display` tinyint(4) NOT NULL DEFAULT 0,
  `is_ref` tinyint(4) NOT NULL DEFAULT 0,
  `details` tinyint(4) NOT NULL DEFAULT 0,
  `cb_threshold` int(11) NOT NULL DEFAULT 70,
  `cb_period` int(11) NOT NULL DEFAULT 0,
  `cb_username` tinyint(4) NOT NULL DEFAULT 1,
  `cb_ip` tinyint(4) NOT NULL DEFAULT 1,
  `cb_bypass` int(11) NOT NULL DEFAULT 80,
  `cb_local` int(11) NOT NULL DEFAULT 0,
  `cb_limit` int(11) NOT NULL DEFAULT 0,
  `cb_limit_period` int(11) NOT NULL DEFAULT 0,
  `cb_geoip` tinyint(4) NOT NULL DEFAULT 0,
  `cb_countries` text NOT NULL,
  `is_api` tinyint(4) NOT NULL DEFAULT 1,
  `api_secret` varchar(100) NOT NULL DEFAULT '',
  `smtp_enable` tinyint(4) NOT NULL DEFAULT 0,
  `smtp_host` varchar(255) NOT NULL DEFAULT '',
  `smtp_port` varchar(20) NOT NULL DEFAULT '',
  `smtp_ssl` tinyint(4) NOT NULL DEFAULT 1,
  `smtp_user` varchar(255) NOT NULL DEFAULT '',
  `smtp_pass` varchar(512) NOT NULL DEFAULT '',
  `smtp_from` varchar(255) NOT NULL DEFAULT '',
  `enable_globalcmd` tinyint(4) NOT NULL DEFAULT 0,
  `is_featured` tinyint(4) NOT NULL DEFAULT 0,
  `featured_items` varchar(255) NOT NULL DEFAULT '',
  `is_featured_offer` tinyint(4) NOT NULL DEFAULT 0,
  `theme` int(11) NOT NULL DEFAULT 1,
  `is_maintenance` tinyint(4) NOT NULL DEFAULT 0,
  `maintenance_ips` text NOT NULL,
  `developer_mode` tinyint(4) NOT NULL DEFAULT 0,
  `patrons_enabled` tinyint(4) NOT NULL DEFAULT 0,
  `patrons_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`patrons_groups`)),
  `patrons_description` text DEFAULT NULL,
  `is_sale_email_notify` tinyint(4) NOT NULL DEFAULT 0,
  `share_metrics` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `categories_level` int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'AlumenatoR','AlumenatoR - сервер с Магическим & Бедрок выживанием на версии 1.20.1. Заходи, будет весело!','play.AlumenatoR.net','','minecraft','','B-Survival','',0,NULL,NULL,'KvSynX5Nb1JozDXLMJiItLIdygTKOmxwypfn7ts6oygKZHx7x0','','0','ru','ru','RUB','RUB',0,'','','<p>To begin shopping, please select a category from the sidebar. Please note that ranks cost a one-time fee and are unlocked permanently!</p>','<div style=\"color:#ffae00; font-size:20px; font-weight:700; text-transform:uppercase\">\n                    <h2>SUPPORT / QUESTIONS</h2>\n                    </div>\n\n                    <div style=\"color:#ffae00; font-size:16px; line-height:normal; margin-top:5px\">\n                    <p>Need any questions answered before checkout? Waited more than 20 minutes but your package still has not arrived? Ask the community/staff on Discord, or for payment support, submit a support ticket on our website.</p>\n                    </div>\n\n                    <div style=\"color:#ff3c00; font-size:20px; font-weight:700; margin-top:35px; text-transform:uppercase\">\n                    <h2>REFUND POLICY</h2>\n                    </div>\n\n                    <div style=\"color:#ea6f05; font-size:16px; line-height:normal; margin-top:5px\">\n                    <p>All payments are final and non-refundable. Attempting a chargeback or opening a PayPal dispute will result in permanent and irreversible banishment from all of our servers, and other minecraft stores.</p>\n                    </div>\n\n                    <div style=\"color:#ff3c00; font-size:16px; line-height:normal; margin-top:30px\">\n                    <p>It could take between 1-20 minutes for your purchase to be credited in-game. If you are still not credited after this time period, please open a support ticket on our forums with proof of purchase and we will look into your issue.</p>\n                    </div>','','','','','','','','username',0,0,'',1,'{username}',0,'{group}',0,0,1,70,0,1,1,80,0,0,0,0,'',1,'v1f94pcm2NseCHKAMQoU',0,'','',1,'','','',0,0,'',0,1,0,'',0,0,'[]',NULL,0,1,'2025-02-18 11:50:06','2025-05-13 12:09:14',2);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_visits`
--

DROP TABLE IF EXISTS `site_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_visits`
--

LOCK TABLES `site_visits` WRITE;
/*!40000 ALTER TABLE `site_visits` DISABLE KEYS */;
INSERT INTO `site_visits` VALUES
(1,547,'2025-02-18 00:00:00'),
(2,551,'2025-02-19 00:00:00'),
(3,355,'2025-02-20 00:00:00'),
(4,98,'2025-02-21 00:00:00'),
(5,65,'2025-03-08 00:00:00'),
(6,162,'2025-04-22 00:00:00'),
(7,60,'2025-04-23 00:00:00'),
(8,156,'2025-05-02 00:00:00'),
(9,297,'2025-05-03 00:00:00'),
(10,315,'2025-05-04 00:00:00'),
(11,15,'2025-05-06 00:00:00'),
(12,93,'2025-05-07 00:00:00'),
(13,112,'2025-05-08 00:00:00'),
(14,397,'2025-05-10 00:00:00'),
(15,66,'2025-05-11 00:00:00'),
(16,201,'2025-05-12 00:00:00'),
(17,333,'2025-05-13 00:00:00'),
(18,143,'2025-05-14 00:00:00');
/*!40000 ALTER TABLE `site_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `sid` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `interval_days` int(11) NOT NULL,
  `renewal` date NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `subscriptions_payment_id_foreign` (`payment_id`),
  CONSTRAINT `subscriptions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `percent` double NOT NULL DEFAULT 0,
  `is_included` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `themes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `theme` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `is_custom` tinyint(4) NOT NULL,
  `version` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES
(1,1,'Default Theme','Default Theme for 3.x Version','https://i.imgur.com/EtTN8yO.png','','MineStoreCMS',0,'3.3.1');
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `system` varchar(255) NOT NULL,
  `identificator` varchar(255) NOT NULL,
  `uuid` varchar(40) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `discord_id` varchar(255) DEFAULT NULL,
  `api_token` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'JeremyKlarsoN','https://mc-heads.net/body/JeremyKlarsoN/150px','minecraft','JeremyKlarsoN',NULL,'Belarus','BY','46.53.251.116',NULL,'bOemrDfHewc2c7J9qzG4ugkH5gvRc2QmN2HqHV8ioYQyvxqjSnPA6oFwl2e6','2025-02-18 12:19:36','2025-02-18 12:19:36'),
(2,'test','https://mc-heads.net/body/test/150px','minecraft','test','d8d5a9237b2043d8883b1150148d6955','Czechia','CZ','146.120.146.214',NULL,'sPtz1vCKMtTg3JQuOHmMkTcKWMqdq1FJa8OG5Bm2wSQ9FVoD3UiHmZTWp3Y2','2025-02-18 13:40:01','2025-02-18 13:40:01'),
(3,'akoval','https://mc-heads.net/body/akoval/150px','minecraft','akoval','6edbab214ed644d895a2694e164991bd','Russia','RU','188.243.40.85',NULL,'Wvp4IOLh6SwGHpYv8OXEAuTKeCJIKwPYHdZGOg9OzCvxLlFcdUQBOwGItfL0','2025-02-18 13:53:35','2025-02-18 13:53:35'),
(4,'Lol','https://mc-heads.net/body/Lol/150px','minecraft','Lol','a97d646be8cb4bb49549a7aba64a7c52','Russia','RU','217.66.156.197',NULL,'c6rpalkZ8BNDofKdbaWywszml1JWcVVG1jkmm2pd6lpEVwtrQoScBDRgsakD','2025-02-18 14:43:47','2025-02-18 14:43:47'),
(5,'dronsyy','https://mc-heads.net/body/dronsyy/150px','minecraft','dronsyy','358bbc90891940bd8663baf7c2d58f0e','Belarus','BY','46.56.226.18',NULL,'oAjQ9UB1vDBNEULqdCWvB6m9YY6YlXusLN3PeyguhCz2L1rVrmKvz3IEFWJT','2025-02-18 18:03:00','2025-02-18 18:03:00'),
(6,'Akulonok_02','https://mc-heads.net/body/Akulonok_02/150px','minecraft','Akulonok_02',NULL,'Russia','RU','31.132.233.36',NULL,'VUSJyLYfbCM4Ma9Vowt0hLWedrFwyudDWqudN0lMf4V9IJnm6SrCl4MOr8qu','2025-02-18 18:11:30','2025-02-18 18:11:30'),
(7,'pizdenkaa','https://mc-heads.net/body/pizdenkaa/150px','minecraft','pizdenkaa',NULL,'Belarus','BY','93.84.98.147',NULL,'oUhWWxsO14s147w9ddpWYORroeDwJChQYS6s1N3mjyZLhh2F0AakaZtHS3BU','2025-02-18 19:15:27','2025-02-18 19:15:27'),
(8,'Makc','https://mc-heads.net/body/Makc/150px','minecraft','Makc','0e2e04f89ab241f8b3d76f44996160a6','Russia','RU','176.59.100.65',NULL,'oq3PwpK5CedbFy3YXMpRv11MgtyF8XcayS0NeIA1I5R20sQ7kMHnayQKv8SL','2025-02-19 13:56:57','2025-02-19 13:56:57'),
(9,'f4ef','https://mc-heads.net/body/f4ef/150px','minecraft','f4ef',NULL,'Belarus','BY','46.53.251.116',NULL,'rLxUZXccWYe41Txtd6cC3ZZCAPGjkvc70gHrZ8fgB4SyZFCIpqX8wfG9A2kl','2025-02-19 13:57:05','2025-02-19 13:57:05'),
(10,'saylor','https://mc-heads.net/body/saylor/150px','minecraft','saylor','1423ed5696f7425cb127bda9fceb5876','Czechia','CZ','46.135.68.119',NULL,'cN8TzHpJzUrJwOgHUcattAOdSlXDY0Zy50G0UW7qxjIAjfquRFiugx089Ak5','2025-02-19 14:26:58','2025-02-19 14:26:58'),
(11,'tess896','https://mc-heads.net/body/tess896/150px','minecraft','tess896',NULL,'Russia','RU','188.225.123.50',NULL,'BAJ5kkqaN89NJ51kJcx7yXoKG4HtOJbnYAZpfQWgaGCY4z3ZD7kcR2iyqWxx','2025-02-19 15:22:23','2025-02-19 15:22:23'),
(12,'root','https://mc-heads.net/body/root/150px','minecraft','root','18c82adf5d6f45908da7fb65f9c572e1','United Kingdom','GB','212.30.36.86',NULL,'lw1iiVTqLVtE1qCLzKzivcdQythfTdzKzt1hr8KZOEl7D2nmJdHVnu39dzgx','2025-02-19 15:51:12','2025-02-19 15:51:12'),
(13,'Larryyy','https://mc-heads.net/body/Larryyy/150px','minecraft','Larryyy','03df1e56e9a945948f5a174f285d5127','Russia','RU','188.225.123.50',NULL,'tPHReVPZu80t1yIZyqHL8aV12FQDnDz39Av9DqSpsMbiK4O9oIEGOF87BDog','2025-02-19 20:32:17','2025-02-19 20:32:17'),
(14,'Homes','https://mc-heads.net/body/Homes/150px','minecraft','Homes','992cb675265941c792538033cef5ff9c','Russia','RU','188.225.123.50',NULL,'hRiBgej1TX8aVb03IfVQQuDDH89LczKLsSchay0laKiSTqzNbgyIpLX3Ub0A','2025-02-19 20:33:01','2025-02-19 20:33:01'),
(15,'Player526','https://mc-heads.net/body/Player526/150px','minecraft','Player526','e6bf961a8133495885a7b5897c138475','United Kingdom','GB','82.24.19.135',NULL,'wvhwhr2rHH18cRt8cVI3bXmvpWjIuWOzzjDglmTczw0Ocw4bXco2Cr6y2hgU','2025-02-20 09:15:05','2025-02-20 09:15:05'),
(16,'DamirDanilov201','https://mc-heads.net/body/DamirDanilov201/150px','minecraft','DamirDanilov201',NULL,'Russia','RU','80.83.238.53',NULL,'nKtRhm6UkS7BQuIC3UeCgWdbDHSnjqocBQf1p9u1D6G4S7x0VI8f3HIEgDeY','2025-02-20 09:19:05','2025-02-20 09:19:05'),
(17,'shintaro','https://mc-heads.net/body/shintaro/150px','minecraft','shintaro','8bc4fff2014b4a4fa8e6e089ee121a4a','Russia','RU','92.63.69.13',NULL,'CGuzQOYA3iX0N4cnxnAlmUlrVxpWkf7gd9He8aTDYWpX6zrrSGYbOwwuujk3','2025-02-20 09:54:25','2025-02-20 09:54:25'),
(18,'IGGa','https://mc-heads.net/body/IGGa/150px','minecraft','IGGa','708ba24ff33849a7b639908c5e819b94','Russia','RU','95.27.12.47',NULL,'KcnG3ZPMSHxFH7WkLnapNd17eWH7nxsEHw412YM4D50AewbtaNb5XvdN7Sfz','2025-02-20 10:55:15','2025-02-20 10:55:15'),
(19,'kopysta228','https://mc-heads.net/body/kopysta228/150px','minecraft','kopysta228',NULL,'Switzerland','CH','57.128.184.65',NULL,'KHeMiM8XlaSRi82GOYxsDMxfatiL5hZbdhbTugOfMnaeATTqPb0RykNah4J9','2025-02-20 11:48:45','2025-02-20 11:48:45'),
(20,'HooGor','https://mc-heads.net/body/HooGor/150px','minecraft','HooGor','469791eae3e14e0fa91707d63d345e3c','Russia','RU','188.162.161.42',NULL,'ESAIh7dSQmSLB7nRawLdcwQksDxaq4To3C1M1qSEtL5YI43fKAQxKmfFulrn','2025-02-20 12:41:43','2025-02-20 12:41:43'),
(21,'vuvka','https://mc-heads.net/body/vuvka/150px','minecraft','vuvka',NULL,'Russia','RU','178.34.151.8',NULL,'VmYG3m3XXdKzPp3Ccw9fDVzlEMIqnWPwZwByv4WYxvmEFqv5Rkgs41HTBlxY','2025-02-20 13:08:02','2025-02-20 13:08:02'),
(22,'Ultragamer527','https://mc-heads.net/body/Ultragamer527/150px','minecraft','Ultragamer527',NULL,'Russia','RU','185.92.138.42',NULL,'JWVpX6UqERiBvfDjBBAMl5tQyimq00oiLdyZmknxaIZC1YBdBX5ZdHe6dWLk','2025-02-20 16:24:01','2025-02-20 16:24:01'),
(23,'timakat','https://mc-heads.net/body/timakat/150px','minecraft','timakat','3d73b427e27b4ebaabcacc8c1ce4b079','Russia','RU','195.208.161.2',NULL,'AE3m5Ca3sZlfOTgysUQ8e45nZ1YT4vdIGlxF63K5NiBD65TjfOEje4OqlbG3','2025-02-20 17:19:50','2025-02-20 17:19:50'),
(24,'Dazirry','https://mc-heads.net/body/Dazirry/150px','minecraft','Dazirry','dbbecad210c344749395e5e6d63a9dac','Russia','RU','185.49.242.63',NULL,'d3n3Cp42NUG6mX8QCmQMmeG5aDQIeDzVDjpsT0LKkdxmJaFypaF0TOda2E3A','2025-02-20 23:53:51','2025-02-20 23:53:51'),
(25,'Fekeks','https://mc-heads.net/body/Fekeks/150px','minecraft','Fekeks','c61916dd61824b46b77a4a9e47e5c2b7','Russia','RU','176.52.32.96',NULL,'7EBNOtV3CAmAaKP4VxWavJkleqOypmhvpbShSLC82Gq05NaQJlANly1Gdqm4','2025-02-21 03:46:45','2025-02-21 03:46:45'),
(26,'Xyerox12','https://mc-heads.net/body/Xyerox12/150px','minecraft','Xyerox12',NULL,'Czechia','CZ','95.47.138.133',NULL,'dYuHkCyNj1F9WI5brAoE3LEFo67R3AR6YJtDTzQjhkuaYVQkt0YHu3IjX8Li','2025-03-08 12:29:53','2025-03-08 12:29:53'),
(27,'MasterTimeLord','https://mc-heads.net/body/MasterTimeLord/150px','minecraft','MasterTimeLord','c32ca2b61f9f4e608d9cc60239084300','Russia','RU','93.181.249.63',NULL,'K8GXHDCbhnyLi4rPVkZUxG20pIHSiByAXl4FWdsAzzhpJhqZO3LqMqHo84aS','2025-04-22 16:26:10','2025-04-22 16:26:10'),
(28,'denkud3','https://mc-heads.net/body/denkud3/150px','minecraft','denkud3',NULL,'Russia','RU','79.139.213.0',NULL,'d51Hmaq1HZNTJnjCxQJaA7cqPDz5QsCV2tXsXdfifCL8NRCDuq6ZTrIfgLn3','2025-04-23 04:05:40','2025-04-23 04:05:40'),
(29,'katya55405','https://mc-heads.net/body/katya55405/150px','minecraft','katya55405',NULL,'Russia','RU','31.162.213.79',NULL,'Bq1M8gyf2pNhxpLTqfJbOCYLxBqrM7O5hVbmRZPoNEiD46AJndeKAfPnFk08','2025-05-02 13:40:50','2025-05-02 13:40:50'),
(30,'DubaBuba','https://mc-heads.net/body/DubaBuba/150px','minecraft','DubaBuba','5ccbd09625e34660bdd668f8752d1fc0','Ukraine','UA','78.30.246.231',NULL,'7RjNxuB6JvE8o1w47jYwFaHjjSgBOMmtuWfwEbwGpCBJLCOFIR3ovdYU3kbA','2025-05-02 14:17:50','2025-05-02 14:17:50'),
(31,'Jeremy','https://mc-heads.net/body/Jeremy/150px','minecraft','Jeremy','e519b41db02d415788d91a75c431a362','Russia','RU','217.66.156.221',NULL,'mXYMCJBrqNburca1tFdamj1yMqguS8TozmItJyDxKcEa4ss0PoEiAnzpSocL','2025-05-02 14:19:56','2025-05-02 14:19:56'),
(32,'bebrian','https://mc-heads.net/body/bebrian/150px','minecraft','bebrian',NULL,'Israel','IL','85.250.85.205',NULL,'tyhbM71cTiw5VAIw7tjdpCIYUoY4gx0keRCAToWdddTBkSKuv2Q8CQ3egBch','2025-05-02 16:20:52','2025-05-02 16:20:52'),
(33,'KOSTY_KRAFT','https://mc-heads.net/body/KOSTY_KRAFT/150px','minecraft','KOSTY_KRAFT',NULL,'Russia','RU','37.19.77.195',NULL,'LilWMxg6srDoy3Awvc34XCrw72puhayzB6krGBI4iAfqBUMnCI0XUTYodaqO','2025-05-02 17:53:08','2025-05-02 17:53:08'),
(34,'fredimen1337','https://mc-heads.net/body/fredimen1337/150px','minecraft','fredimen1337','4f39ee19c593491bbd717dc3d5dbbbed','Russia','RU','94.51.123.146',NULL,'nFoQ4tcnqVVwCKyz3jYkuTH6oUjogNnZkFbfLUMuVCzOxkWVOoSS3Zpuj9Uo','2025-05-02 19:29:04','2025-05-02 19:29:04'),
(35,'kaast1','https://mc-heads.net/body/kaast1/150px','minecraft','kaast1',NULL,'France','FR','51.158.253.79',NULL,'YR1y3O7uQzBYpP9hTnQCXmcJx2nd90WH78JbbMLYO3xJwgFYfbGYMvG1yPkm','2025-05-02 21:47:13','2025-05-02 21:47:13'),
(36,'Nozo','https://mc-heads.net/body/Nozo/150px','minecraft','Nozo','5e9e249cf6014f6fa1907c2c1530738c','Russia','RU','176.116.140.146',NULL,'VcicXIQf6J5Kgs6d4BLQ4T2Ud6nn6ql9xo9lBprXrqXSoMa7xf15gQo4SYmk','2025-05-03 10:15:17','2025-05-03 10:15:17'),
(37,'Falkconik','https://mc-heads.net/body/Falkconik/150px','minecraft','Falkconik',NULL,'United States','US','104.28.245.233',NULL,'z0myFEg6SuNWQ6DtWVgWdQPH57NUFOo5fLxIoXa7deHJ8CzRJfvHDxYYjd8s','2025-05-03 10:43:07','2025-05-03 10:43:07'),
(38,'ANTONGGG1','https://mc-heads.net/body/ANTONGGG1/150px','minecraft','ANTONGGG1',NULL,'Russia','RU','176.112.96.20',NULL,'5bwJya2AbNHPPswP2zJ7eOo0ElfQJe01fjjNDH6jWzidp4IELK7TRM2dtvk4','2025-05-03 16:37:12','2025-05-03 16:37:12'),
(39,'cusua','https://mc-heads.net/body/cusua/150px','minecraft','cusua',NULL,'Russia','RU','95.104.175.191',NULL,'0wJiYJlrXPG4uxdkHw17RZYbR6lwtpKwF5iVSLZfRrE2jWRvhAZECeMIc3IF','2025-05-03 16:58:47','2025-05-03 16:58:47'),
(40,'MeniE0ri','https://mc-heads.net/body/MeniE0ri/150px','minecraft','MeniE0ri',NULL,'Russia','RU','95.26.118.14',NULL,'d93JvIUWmPQW1iWYPw0FMlcaao1hi5V2U8eAS0CAgN9T3LsBw4Ze4G188JTn','2025-05-03 18:54:27','2025-05-03 18:54:27'),
(41,'kirill2004','https://mc-heads.net/body/kirill2004/150px','minecraft','kirill2004','24a0c5b46a6e4bdcb8c50d0cbe71c8ba','Russia','RU','77.222.104.201',NULL,'H2qLMFCjpYn5QtpfEhf3zPCBgFQEujZ6jWr8SII4EgIM0RAQakcztrYL6c3n','2025-05-03 19:16:04','2025-05-03 19:16:04'),
(42,'fnaf_fnaf','https://mc-heads.net/body/fnaf_fnaf/150px','minecraft','fnaf_fnaf',NULL,'Belgium','BE','91.177.13.83',NULL,'hKIEFAfzspHLaKYBypgN7ftianiBftkALiiNeTWo5UdcerHTeAqQTpl91iDR','2025-05-03 20:35:54','2025-05-03 20:35:54'),
(43,'Stps7925','https://mc-heads.net/body/Stps7925/150px','minecraft','Stps7925',NULL,'Russia','RU','185.150.13.200',NULL,'cyTJzRC8jss6uU0opAz7vPTVijmXLFcrGp9DaxXqmss9wyKs0iFzNmTo28vD','2025-05-03 20:51:05','2025-05-03 20:51:05'),
(44,'Nostalgiakiller','https://mc-heads.net/body/Nostalgiakiller/150px','minecraft','Nostalgiakiller',NULL,'Russia','RU','95.161.223.80',NULL,'MwpTzaPYs7z8oil7EyiVMkfMAJ8GQ5E3JJ1m7pMi9ykzLbIcQYKG74Gs9pGc','2025-05-03 21:53:40','2025-05-03 21:53:40'),
(45,'OptickuS','https://mc-heads.net/body/OptickuS/150px','minecraft','OptickuS','8d5f90dd980f441780334d39b495db09','Russia','RU','95.79.194.82',NULL,'7jGiTmEYhCQWjuMTakm00oZOaB8buMWOfgCTDkkH1zHKszQLCrB9JXwZsdPJ','2025-05-03 22:11:49','2025-05-03 22:11:49'),
(46,'diemondd','https://mc-heads.net/body/diemondd/150px','minecraft','diemondd',NULL,'Russia','RU','188.65.242.60',NULL,'0vfhF9RAVy6FNkqebEFdcPHJ1xpDT6lHfHA47LYkgj42SjsmMxujhaNg8uge','2025-05-03 22:31:29','2025-05-03 22:31:29'),
(47,'Makorunner1','https://mc-heads.net/body/Makorunner1/150px','minecraft','Makorunner1',NULL,'Georgia','GE','149.3.117.47',NULL,'EgKPAqkSmZc4pTFNWAJcBdQsazGC3T5ZenDSp61W7Vf1ncXRUh1rc7CmtmVW','2025-05-04 09:34:54','2025-05-04 09:34:54'),
(48,'xJungo','https://mc-heads.net/body/xJungo/150px','minecraft','xJungo',NULL,'Moldova','MD','92.115.5.222',NULL,'1ip3mSwSlpXiMU6BuM9uAHTa5V420HgAWSqtMUsQlwiw9vbHYBiCKWk53uyn','2025-05-04 11:12:36','2025-05-04 11:12:36'),
(49,'Doppel','https://mc-heads.net/body/Doppel/150px','minecraft','Doppel','edce66d78e814d2c9f8eed82a568018b','Russia','RU','88.201.185.237',NULL,'dbaz8hgjNP5MAHfm7bSYs3TRtXmX3WJHuoUAYgNHdL7ITT88qnG3Fsr3twPJ','2025-05-04 12:16:25','2025-05-04 12:16:25'),
(50,'Nikolea','https://mc-heads.net/body/Nikolea/150px','minecraft','Nikolea','8ececceddc314a55bacf541277f87b32','Moldova','MD','92.115.5.222',NULL,'jc49fqp5q7jqGoFSxMTygNtDZIlbG3lmR4Hq9Q49zVDRc2t8ma0M8H03u9hR','2025-05-04 13:54:04','2025-05-04 13:54:04'),
(51,'freevamjke','https://mc-heads.net/body/freevamjke/150px','minecraft','freevamjke',NULL,'Russia','RU','5.188.137.137',NULL,'wAIQnjLUebTFUnMOjO1DVquaezEhzgO2QRHNir3R6bsiVFr9hzY6FtE10fAK','2025-05-06 12:06:36','2025-05-06 12:06:36'),
(52,'DannzieBlaze','https://mc-heads.net/body/DannzieBlaze/150px','minecraft','DannzieBlaze','b8eb880bede4487fa11400a80d66f0ad','Russia','RU','31.131.194.231',NULL,'vKpnzGKq9ei1aswaaIfMAloNoaUfnmWsj865unaBbbnYhXkQwXdUj8FNm6EJ','2025-05-07 13:28:01','2025-05-07 13:28:01'),
(53,'K2907sqweezy','https://mc-heads.net/body/K2907sqweezy/150px','minecraft','K2907sqweezy',NULL,'Russia','RU','212.35.160.204',NULL,'9SK2vv9hdPYnaOGDqpyV5FGmZPGFtBakFuk7zrSCupvZCM3Q3RIhploGpwhZ','2025-05-08 14:32:33','2025-05-08 14:32:33'),
(54,'Evangelion_02_02','https://mc-heads.net/body/Evangelion_02_02/150px','minecraft','Evangelion_02_02','e51b404525984afb8d23c3d4a732fed2','Russia','RU','195.211.194.202',NULL,'vp5m7G8a7JZHsBwBi9DkpZyfSEiOU8Ih5f2Sh18UUJb7TWoRqY0sJVQ3NnWz','2025-05-08 17:49:07','2025-05-08 17:49:07'),
(55,'admin','https://mc-heads.net/body/admin/150px','minecraft','admin','f680df9bac5c4d3f9bac75bc0e316afa','France','FR','51.77.137.176',NULL,'B6Yo7GkgEi5IxIYD4WQuqvtybHl1Es9g0QgLo1mcOp1gowIPFfOB1VEsShoD','2025-05-10 08:58:55','2025-05-10 08:58:55'),
(56,'Faer777','https://mc-heads.net/body/Faer777/150px','minecraft','Faer777',NULL,'Russia','RU','89.208.205.109',NULL,'SGwDjbqrqCTPwsAMHyjKGOxhDSV3U5KLL5fDFRzJr6sEXXuZnniIjLIzjxDt','2025-05-10 09:02:59','2025-05-10 09:02:59'),
(57,'Ganduras','https://mc-heads.net/body/Ganduras/150px','minecraft','Ganduras','26036ada652e48efa3dcacc592eb9b3e','Russia','RU','94.29.3.40',NULL,'tA2j5DKNPwcAGQvGqnjCcsYuqK4oPTp2HsGXs14SrC5wq0uXCGJR6JXBiIM6','2025-05-10 10:13:09','2025-05-10 10:13:09'),
(58,'testest','https://mc-heads.net/body/testest/150px','minecraft','testest','5e3cc0d9e91245249fe58e2f92c39aa1','Russia','RU','188.243.237.17',NULL,'VDyiuRLPT1mPVxQdvsQLSHAMIRY106esBm6DGi3hfLKwfMSAigjWtRcwiPhP','2025-05-10 11:24:19','2025-05-10 11:24:19'),
(59,'Tr65456','https://mc-heads.net/body/Tr65456/150px','minecraft','Tr65456',NULL,'United Arab Emirates','AE','217.165.162.114',NULL,'uXtidmLebUhUxrzG9ztQ3YQJSoFwXuf1DzKzM3QJZYqDMOc720DtExSWkTZ0','2025-05-11 19:17:04','2025-05-11 19:17:04'),
(60,'Red','https://mc-heads.net/body/Red/150px','minecraft','Red','542189cf68fb415bbbe6c60da626e65a','United Arab Emirates','AE','217.165.162.114',NULL,'mJSOLHk6uVLx5Uu9k08BZNybLs0axmE4KJt6bg9lG0v4f8UrDKfy2tZpStrY','2025-05-11 19:18:14','2025-05-11 19:18:14'),
(61,'Shuga','https://mc-heads.net/body/Shuga/150px','minecraft','Shuga','8e151f77e1ef41d5ad1a744027c33fb5','Germany','DE','193.108.119.140',NULL,'ees6Kb8FO1anC0sCDZ452vWdsvsGRBSZviyR4GbUU68MY8420HzDpVLL8Ejk','2025-05-12 10:54:51','2025-05-12 10:54:51'),
(62,'SuperbBee194441','https://mc-heads.net/body/SuperbBee194441/150px','minecraft','SuperbBee194441',NULL,'Spain','ES','62.93.165.74',NULL,'4AurE3ds467gwNCR2QguYWx96n8Zu2xsOR0SKDZysNDga98MRSyNs6BtviLy','2025-05-12 11:37:02','2025-05-12 11:37:02'),
(63,'Crazy120202','https://mc-heads.net/body/Crazy120202/150px','minecraft','Crazy120202',NULL,'Kazakhstan','KZ','95.57.162.53',NULL,'KMJlwVgvml9AqNMZsj6BokWxX0z58V3TThaZb3Ckj1Ldvq9ZtsooDMsCnl8Y','2025-05-13 08:48:50','2025-05-13 08:48:50'),
(64,'corporate','https://mc-heads.net/body/corporate/150px','minecraft','corporate','6a564ecfe1ca4b439c7cb819de4fe07b','Russia','RU','217.66.158.166',NULL,'yYMHM1kPbQVutECm7lxrlsnp5mKPhHoJn9Lr3KCWKt1V3HpnquEirzmz4liK','2025-05-13 10:41:38','2025-05-13 10:41:38'),
(65,'Alum','https://mc-heads.net/body/Alum/150px','minecraft','Alum','2d173596996f42d1b3f5a1c60d47060b','Russia','RU','217.66.158.166',NULL,'Epu6MJONSWyb9qqgidoG87ucd1HurMP43hpjNW45N565bXjN996KFYp2B9VC','2025-05-13 12:16:58','2025-05-13 12:16:58'),
(66,'TecK','https://mc-heads.net/body/TecK/150px','minecraft','TecK','59813b8f189648de8ccb3b4be03162bd','Russia','RU','178.167.43.136',NULL,'53fkfJtBzJB9vepmC1MShWR1flAD3BBmBDC0z5iuybjkzySWFtcsSc7WH8u8','2025-05-13 13:44:39','2025-05-13 13:44:39'),
(67,'W_losgur','https://mc-heads.net/body/W_losgur/150px','minecraft','W_losgur',NULL,'Russia','RU','91.215.23.16',NULL,'WLvRnXcwWIa47RIegoDiYYSxr7tPIIDxQMvxxPS0zivS9ExabYpMes7sKLtt','2025-05-13 17:10:59','2025-05-13 17:10:59'),
(68,'Ole4ka1719','https://mc-heads.net/body/Ole4ka1719/150px','minecraft','Ole4ka1719',NULL,'Switzerland','CH','37.235.51.243',NULL,'pJW76BWsDy12RJiImdi2bPaxOyQBm0ydlN4IMVKCsdsqd0RCK47NOos9jsb3','2025-05-14 04:55:47','2025-05-14 04:55:47'),
(69,'amibektezhaboe','https://mc-heads.net/body/amibektezhaboe/150px','minecraft','amibektezhaboe',NULL,'Poland','PL','151.115.88.239',NULL,'kyLwXJwVJaWEzw5EeVGPlNX8WLwIfq3wa2IH50KeKbgr28Kk53AHJSwNUH5t','2025-05-14 16:18:50','2025-05-14 16:18:50'),
(70,'dremsan12','https://mc-heads.net/body/dremsan12/150px','minecraft','dremsan12',NULL,'Germany','DE','95.91.225.21',NULL,'kKqAvEkDkLX1sa484kpuLwKG7dAOuUMkHQBEwVypS5Wkln7KZ4GhUFwhxXE4','2025-05-14 18:51:28','2025-05-14 18:51:28'),
(71,'FIX','https://mc-heads.net/body/FIX/150px','minecraft','FIX','7395d056536a4cf39c966c7a7df6897a','Lithuania','LT','78.63.33.40',NULL,'ZtfhihMNE1NiV1eEFjIiZWWBJro3IWg542uLdYlO8fCBLCVBZ6GG56X18bcW','2025-05-14 19:24:51','2025-05-14 19:24:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vars`
--

DROP TABLE IF EXISTS `vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `identifier` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vars`
--

LOCK TABLES `vars` WRITE;
/*!40000 ALTER TABLE `vars` DISABLE KEYS */;
/*!40000 ALTER TABLE `vars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whitelist`
--

DROP TABLE IF EXISTS `whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `whitelist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whitelist`
--

LOCK TABLES `whitelist` WRITE;
/*!40000 ALTER TABLE `whitelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `whitelist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-05-15  0:30:01
