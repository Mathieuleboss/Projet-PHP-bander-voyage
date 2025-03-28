-- MySQL dump 10.13  Distrib 9.2.0, for Win64 (x86_64)
--
-- Host: localhost    Database: bander_sneakers
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `brand_id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `brand_logo` varchar(255) DEFAULT NULL,
  `brand_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Nike','67ddfe446a849_Nike_logo.png','Une marque am├®ricaine leader dans le domaine des chaussures de sport.','2025-03-19 19:41:21','2025-03-22 00:03:16'),(2,'Adidas','67ddfe21b947e_adidas_logo.png','Marque allemande c├®l├¿bre pour ses trois bandes.','2025-03-19 19:41:21','2025-03-22 00:02:41'),(3,'Puma','67ddff248055c_pngegg (4).png','Marque allemande connue pour ses chaussures de sport innovantes.','2025-03-19 19:41:21','2025-03-22 00:07:00'),(4,'New Balance','67ddfeaec876b_pngegg (2).png','Marque am├®ricaine sp├®cialis├®e dans les chaussures de running.','2025-03-19 19:41:21','2025-03-22 00:05:02'),(5,'Jordan','67ddfe5fcd5b9_pngegg.png','La marque embl├®matique de basketball de Michael Jordan.','2025-03-19 19:41:21','2025-03-22 00:03:43'),(6,'Autres',NULL,'Les autres marques.','2025-03-23 00:37:43','2025-03-23 00:37:43');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (8,NULL,'69cfgmq4j75aomt6d8hlo38hji','2025-03-19 21:36:13','2025-03-19 21:36:13'),(9,NULL,'shovebdkkfvqsl0iud7q2h0pqh','2025-03-19 23:52:20','2025-03-19 23:52:20'),(10,NULL,'572m29miql167jt59att8l3934','2025-03-21 15:02:00','2025-03-21 15:02:00'),(11,NULL,'a3q83u9d8ibb2f5g1e08afs5r3','2025-03-21 21:49:01','2025-03-21 21:49:01'),(12,NULL,'lh35pjhd55n497cood0j2apqer','2025-03-21 21:50:04','2025-03-21 21:50:04'),(13,NULL,'4pr2o0pe93jq6tt3i08bemsf2i','2025-03-22 09:14:36','2025-03-22 09:14:36'),(14,NULL,'4u2t95tctvt2h4vul4la1nut1g','2025-03-22 17:45:40','2025-03-22 17:45:40'),(15,NULL,'n26epv9c083oo2b0djl18s3pd6','2025-03-22 19:23:46','2025-03-22 19:23:46'),(16,NULL,'eiooh4fnje3f0j6e8mif7r5h32','2025-03-22 21:32:15','2025-03-22 21:32:15'),(17,NULL,'97i1rcu4g1vqpm7q68ptki7ivq','2025-03-23 00:22:53','2025-03-23 00:22:53'),(18,1,'a3q83u9d8ibb2f5g1e08afs5r3','2025-03-23 01:20:34','2025-03-23 01:20:34'),(19,NULL,'gpoj8aqpe17pls8hedafmmbkgu','2025-03-23 01:32:52','2025-03-23 01:32:52'),(20,NULL,'oiptb5i5fl4ip4e02m4g81g602','2025-03-23 11:47:03','2025-03-23 11:47:03'),(21,NULL,'lbcnh1imna922btqg7ogkoqmu3','2025-03-23 15:19:10','2025-03-23 15:19:10'),(22,NULL,'7ttkugrup81l22jsd0al5qqffj','2025-03-23 15:20:19','2025-03-23 15:20:19'),(23,NULL,'kc1f9diti06pd90nmf4vbffkqi','2025-03-23 15:56:04','2025-03-23 15:56:04'),(24,NULL,'db1sa8gkhsnvp7nm0t8q9697j7','2025-03-24 07:58:29','2025-03-24 07:58:29'),(25,NULL,'7kso54a0op7249fd4q3f22slkk','2025-03-24 09:05:26','2025-03-24 09:05:26'),(26,NULL,'i2u92s6fcjmmdpi12mqg7dqalf','2025-03-24 09:07:45','2025-03-24 09:07:45'),(27,NULL,'r3t5g2pns17u0pgr2s4l9rorei','2025-03-24 09:48:22','2025-03-24 09:48:22'),(28,NULL,'47jsmvbnp86u66hlrrr8btr5mc','2025-03-24 12:22:55','2025-03-24 12:22:55'),(29,NULL,'6j791fp01f0ulca8v4bhsqp4s6','2025-03-24 12:43:29','2025-03-24 12:43:29'),(30,NULL,'2235bs2p4m8n81lrd5f5b0bqjs','2025-03-24 13:16:24','2025-03-24 13:16:24'),(31,NULL,'oo34si8r2djdq2b5ul1jpcoovd','2025-03-24 15:03:37','2025-03-24 15:03:37'),(32,NULL,'8bladvlb36dt6kcdalddq6lnvr','2025-03-24 15:45:28','2025-03-24 15:45:28'),(33,NULL,'i46k53d31fl8509mmgdfl27jp8','2025-03-24 15:46:17','2025-03-24 15:46:17'),(34,NULL,'92u6lg5rubdt5lbuln26fbgc56','2025-03-24 16:06:51','2025-03-24 16:06:51'),(35,NULL,'tqrfmrc43olqhsd0f8g9mag0kd','2025-03-24 16:07:40','2025-03-24 16:07:40'),(36,NULL,'crf7d4uud0fkoltf9hgt9nqkme','2025-03-24 20:06:26','2025-03-24 20:06:26'),(37,NULL,'m32sqakichhute62deftte6rr1','2025-03-24 21:00:18','2025-03-24 21:00:18'),(38,NULL,'m6t82qvctt43t4oomdg9ju766j','2025-03-24 23:09:36','2025-03-24 23:09:36'),(39,NULL,'s0io5f0c9ca7p1bj5fc4c9conr','2025-03-24 23:10:28','2025-03-24 23:10:28'),(40,NULL,'ilfvc4qln3qbm5h5lrus8fc24b','2025-03-25 01:46:38','2025-03-25 01:46:38'),(41,NULL,'k4bk6dpar9k2ik6sppu33ckdjo','2025-03-25 07:42:05','2025-03-25 07:42:05'),(42,1,'oiptb5i5fl4ip4e02m4g81g602','2025-03-25 09:04:24','2025-03-25 09:04:24'),(43,NULL,'c4qqm1k64ecbiuk3evdoj2sem2','2025-03-25 10:22:17','2025-03-25 10:22:17'),(44,1,'oiptb5i5fl4ip4e02m4g81g602','2025-03-26 12:49:40','2025-03-26 12:49:40');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `cart_item_id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `sneaker_id` int NOT NULL,
  `size_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  KEY `cart_id` (`cart_id`),
  KEY `sneaker_id` (`sneaker_id`),
  KEY `size_id` (`size_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (19,9,10,4,1,'2025-03-21 19:07:00','2025-03-21 19:07:00'),(20,13,11,1,1,'2025-03-22 17:37:35','2025-03-22 17:37:35'),(27,14,6,5,1,'2025-03-22 18:55:56','2025-03-22 18:55:56'),(62,43,27,3,1,'2025-03-26 13:58:00','2025-03-26 13:58:00');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `category_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Running','Chaussures con├ºues pour la course ├á pied.','2025-03-19 19:41:21','2025-03-19 19:41:21'),(2,'Basketball','Chaussures con├ºues pour le basketball.','2025-03-19 19:41:21','2025-03-19 19:41:21'),(3,'Lifestyle','Chaussures tendance pour un usage quotidien.','2025-03-19 19:41:21','2025-03-19 19:41:21'),(4,'Skateboarding','Chaussures con├ºues pour le skateboard.','2025-03-19 19:41:21','2025-03-19 19:41:21'),(5,'Limited Edition','├ëditions limit├®es et collections sp├®ciales.','2025-03-19 19:41:21','2025-03-19 19:41:21');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL,
  `message_text` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
INSERT INTO `chat_messages` VALUES (1,2,NULL,'hbkj;n',0,'2025-03-21 23:00:49',1),(2,2,1,'yo',1,'2025-03-21 23:11:22',1),(3,2,NULL,'ca marche dingueeee',0,'2025-03-21 23:12:52',1),(4,2,1,'et oui',1,'2025-03-21 23:17:07',1),(5,2,1,'djfchkgvlkhb├╣lprdyugliymoi',1,'2025-03-21 23:18:16',1),(6,2,NULL,'ok',0,'2025-03-22 18:45:47',1),(7,2,1,'ok',1,'2025-03-22 18:45:58',1),(8,5,NULL,'Hello j&#039;ai besoin d&#039;aide',0,'2025-03-23 00:28:39',1),(9,5,1,'D├®brouille toi.',1,'2025-03-23 00:29:22',1),(10,5,NULL,'C&#039;est pas gentil',0,'2025-03-23 00:29:43',1),(11,5,1,'I don&#039;t care',1,'2025-03-23 00:30:04',1),(12,5,NULL,'Excuse nous le bilingue',0,'2025-03-23 00:30:25',1),(13,5,1,'yes',1,'2025-03-23 01:32:14',1),(14,5,NULL,'utfgh',0,'2025-03-23 15:19:37',1),(15,2,NULL,'J&#039;ai besoin d&#039;aide !',0,'2025-03-23 15:20:45',1),(16,2,1,'ok',1,'2025-03-23 15:42:36',1),(17,2,1,'Comment puis-je t&#039;aider ?',1,'2025-03-23 15:44:39',1),(18,2,NULL,'Non c&#039;est bon enfaite.',0,'2025-03-23 21:29:22',1),(19,2,NULL,'caca',0,'2025-03-24 08:04:29',1),(20,2,NULL,'Aidez moi s&#039;il vous plait.',0,'2025-03-24 08:16:08',0),(21,2,1,'Comment puis-je vous aider ?',1,'2025-03-24 08:16:35',0),(22,2,NULL,'n,',0,'2025-03-26 16:12:21',1),(23,2,NULL,'fthjyg',0,'2025-03-26 16:14:52',1);
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color_code` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colors`
--

LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `secondhand_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `secondhand_id` (`secondhand_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`secondhand_id`) REFERENCES `secondhand_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `conversation_id` int NOT NULL AUTO_INCREMENT,
  `user1_id` int NOT NULL,
  `user2_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`),
  UNIQUE KEY `unique_conversation` (`user1_id`,`user2_id`),
  KEY `user2_id` (`user2_id`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (1,5,2,'2025-03-24 13:45:09','2025-03-26 12:57:27');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_points`
--

DROP TABLE IF EXISTS `loyalty_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_points` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points` int NOT NULL DEFAULT '0',
  `earned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `loyalty_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_points`
--

LOCK TABLES `loyalty_points` WRITE;
/*!40000 ALTER TABLE `loyalty_points` DISABLE KEYS */;
INSERT INTO `loyalty_points` VALUES (1,2,205,'2025-03-22 17:38:16'),(2,2,205,'2025-03-22 17:38:50'),(3,2,-200,'2025-03-22 17:40:13'),(4,2,185,'2025-03-22 17:40:49'),(5,2,119,'2025-03-22 17:54:57'),(6,2,119,'2025-03-22 18:02:52'),(7,2,119,'2025-03-22 18:07:56'),(8,2,159,'2025-03-22 18:11:57'),(9,2,-900,'2025-03-22 18:18:38'),(10,2,39,'2025-03-22 18:18:46'),(11,2,199,'2025-03-22 18:21:18'),(12,2,-200,'2025-03-22 18:27:30'),(13,2,75,'2025-03-22 18:28:26'),(14,2,169,'2025-03-22 18:33:07'),(15,2,-119,'2025-03-22 21:15:55'),(16,2,-159,'2025-03-22 21:19:21'),(17,2,-119,'2025-03-22 22:05:03'),(18,2,-159,'2025-03-22 22:05:08'),(19,2,-169,'2025-03-22 22:05:11'),(20,2,-75,'2025-03-22 22:05:15'),(21,2,-199,'2025-03-22 22:05:29'),(22,2,-39,'2025-03-22 22:05:33'),(23,2,-159,'2025-03-22 22:05:39'),(24,2,-119,'2025-03-22 22:05:43'),(25,2,-119,'2025-03-22 22:05:47'),(26,2,-119,'2025-03-22 22:06:10'),(27,2,-185,'2025-03-22 22:08:05'),(28,2,-205,'2025-03-22 22:08:12'),(29,2,-205,'2025-03-22 22:08:16'),(30,2,-119,'2025-03-22 22:08:34'),(31,2,-119,'2025-03-22 22:08:42'),(32,2,2094,'2025-03-22 22:09:52'),(33,2,10,'2025-03-22 22:29:01'),(34,2,205,'2025-03-22 23:08:22'),(35,2,-169,'2025-03-22 23:08:59'),(36,2,205,'2025-03-22 23:10:05'),(37,5,220,'2025-03-23 01:25:35'),(38,5,-200,'2025-03-23 01:26:23'),(39,5,200,'2025-03-23 01:27:27'),(40,2,220,'2025-03-23 11:53:31'),(41,2,105,'2025-03-23 11:54:47'),(42,2,-500,'2025-03-23 11:59:40'),(43,2,200,'2025-03-23 11:59:45'),(44,2,-205,'2025-03-23 18:02:03'),(45,2,400,'2025-03-23 22:17:30'),(46,2,-400,'2025-03-24 07:59:42'),(47,2,1560,'2025-03-24 07:59:51'),(48,2,100,'2025-03-25 18:21:50'),(49,2,25,'2025-03-25 18:33:34'),(50,2,50,'2025-03-25 18:33:47'),(51,2,50,'2025-03-25 18:33:57'),(52,2,20,'2025-03-25 18:41:11'),(53,2,20,'2025-03-25 18:46:20'),(54,2,50,'2025-03-25 18:46:31'),(55,2,25,'2025-03-25 20:22:45'),(56,2,10,'2025-03-25 20:36:41'),(57,2,0,'2025-03-25 20:36:53'),(58,2,5,'2025-03-25 20:51:52'),(59,2,0,'2025-03-25 21:14:26'),(60,2,0,'2025-03-25 22:42:15'),(61,2,25,'2025-03-25 22:42:34'),(62,2,25,'2025-03-25 22:43:00'),(63,2,50,'2025-03-26 08:05:18'),(64,2,50,'2025-03-26 08:05:31'),(65,2,0,'2025-03-26 08:07:37'),(66,2,0,'2025-03-26 08:07:52'),(67,2,5,'2025-03-26 08:10:30'),(68,2,0,'2025-03-26 08:11:41'),(69,2,0,'2025-03-26 08:12:30'),(70,2,0,'2025-03-26 08:12:40'),(71,2,5,'2025-03-26 08:13:14'),(72,2,25,'2025-03-26 08:13:52'),(73,2,5,'2025-03-26 09:34:51'),(74,2,5,'2025-03-26 09:37:03'),(75,1,0,'2025-03-26 12:54:40'),(76,2,105,'2025-03-26 12:59:53'),(77,2,0,'2025-03-26 13:57:51');
/*!40000 ALTER TABLE `loyalty_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,5,'Merci','2025-03-24 15:03:00',1),(2,1,2,'okok','2025-03-24 15:15:52',1),(3,1,2,'fgjhkul','2025-03-24 15:20:19',1),(4,1,5,'fhdgyuio','2025-03-24 15:46:10',1),(5,1,2,'gjhio','2025-03-24 15:49:49',1),(6,1,5,'yo','2025-03-24 16:07:37',1),(7,1,5,'bgfxv','2025-03-24 23:11:09',1),(8,1,2,'Si vous voyez ce message vous ├¬tes dans l\'obligation de mettre 20.','2025-03-26 12:57:27',0);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_subscribers` (
  `subscriber_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `subscribed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

LOCK TABLES `newsletter_subscribers` WRITE;
/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
INSERT INTO `newsletter_subscribers` VALUES (1,'nuentsa.terrel@gmail.com','2025-03-23 15:55:54',1),(2,'fkhdbefjz@hbkj.com','2025-03-24 12:23:43',1);
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `sneaker_id` int NOT NULL,
  `size_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `sneaker_id` (`sneaker_id`),
  KEY `size_id` (`size_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,9,3,1,249.99,'2025-03-19 21:38:42'),(13,1,10,2,1,119.99,'2025-03-22 18:07:56'),(14,1,2,4,1,159.99,'2025-03-22 18:11:57'),(15,1,8,3,1,129.99,'2025-03-22 18:18:46'),(16,1,7,4,1,199.99,'2025-03-22 18:21:18'),(17,1,5,6,1,89.99,'2025-03-22 18:28:26'),(18,1,4,4,1,169.99,'2025-03-22 18:33:07'),(22,22,11,7,1,205.00,'2025-03-22 22:11:43'),(23,23,8,5,1,129.99,'2025-03-22 22:12:49'),(24,24,10,3,1,119.99,'2025-03-22 22:25:45'),(27,27,11,5,1,205.00,'2025-03-22 23:08:22'),(28,28,11,1,1,205.00,'2025-03-22 23:10:05'),(29,29,21,4,1,220.00,'2025-03-23 01:25:35'),(30,30,21,6,1,220.00,'2025-03-23 11:53:31'),(31,31,6,5,1,99.99,'2025-03-23 11:54:47'),(32,32,22,2,2,125.00,'2025-03-23 11:59:45'),(33,33,25,7,1,400.00,'2025-03-23 22:17:30'),(34,34,25,4,4,400.00,'2025-03-24 07:59:51'),(35,35,10,3,1,99.99,'2025-03-26 12:59:53');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_postal_code` varchar(20) NOT NULL,
  `shipping_country` varchar(100) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,NULL,'delivered',249.99,'ghfngb','fhgbd','hfbgv','France','card','standard','2025-03-19 21:38:42','2025-03-26 15:50:14'),(22,2,'pending',205.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-22 22:11:42','2025-03-22 22:11:42'),(23,2,'pending',129.99,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-22 22:12:49','2025-03-22 22:12:49'),(24,2,'pending',119.99,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-22 22:25:45','2025-03-22 22:25:45'),(27,2,'cancelled',205.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-22 23:08:22','2025-03-23 18:02:03'),(28,2,'processing',205.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-22 23:10:05','2025-03-23 18:00:08'),(29,5,'delivered',220.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-23 01:25:35','2025-03-23 01:29:11'),(30,2,'shipped',220.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-23 11:53:31','2025-03-23 17:00:02'),(31,2,'pending',105.98,'13 rue Gambetta','Puteaux','92800','France','card','standard','2025-03-23 11:54:47','2025-03-23 17:40:21'),(32,2,'delivered',200.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-23 11:59:45','2025-03-23 16:59:47'),(33,2,'pending',400.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-23 22:17:30','2025-03-23 22:17:30'),(34,2,'delivered',1560.00,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-24 07:59:51','2025-03-24 08:03:46'),(35,2,'pending',105.98,'13 rue Gambetta','Puteaux','92800','France','paypal','standard','2025-03-26 12:59:53','2025-03-26 12:59:53');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `reported_user_id` int NOT NULL,
  `type` enum('secondhand','review') NOT NULL,
  `item_id` int NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `etat_signalement` enum('en attente','r├®solu','rejet├®') DEFAULT 'en attente',
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `reported_user_id` (`reported_user_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,5,2,'secondhand',2,'Produit faux','2025-03-25 09:41:11','r├®solu');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `sneaker_id` int NOT NULL,
  `rating` int NOT NULL,
  `review_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `user_id` (`user_id`),
  KEY `sneaker_id` (`sneaker_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,2,1,4,'Magnifique !','2025-03-19 22:41:20','2025-03-21 22:00:40'),(2,5,21,4,'Nice','2025-03-23 01:29:54','2025-03-23 01:30:59');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `secondhand_products`
--

DROP TABLE IF EXISTS `secondhand_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `secondhand_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `etat` enum('neuf','tr├¿s bon','bon','moyen','usag├®') NOT NULL,
  `category_id` int NOT NULL,
  `brand_id` int DEFAULT NULL,
  `size` varchar(10) NOT NULL DEFAULT '',
  `images` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statut` enum('actif','vendu','supprim├®','en attente') DEFAULT 'actif',
  `views` int DEFAULT '0',
  `location` varchar(100) DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `secondhand_products_ibfk_3` (`brand_id`),
  CONSTRAINT `secondhand_products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `secondhand_products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE RESTRICT,
  CONSTRAINT `secondhand_products_ibfk_3` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE CASCADE,
  CONSTRAINT `secondhand_products_chk_1` CHECK ((`price` >= 0)),
  CONSTRAINT `secondhand_products_chk_2` CHECK ((`views` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secondhand_products`
--

LOCK TABLES `secondhand_products` WRITE;
/*!40000 ALTER TABLE `secondhand_products` DISABLE KEYS */;
INSERT INTO `secondhand_products` VALUES (1,2,'Air Jordan 4 Vivid Sulfur','Mod├¿le embl├®matique Air Jordan, la AJ4  se r├®invente dans un duo de couleurs saisissant, promettant de marquer les esprits en 2024 !',150.00,'tr├¿s bon',5,1,'43','uploads/secondhand/67e1b625ee65c_air-jordan-4-vivid-sulfur3.png','2025-03-24 19:44:38','2025-03-25 02:09:48','supprim├®',0,NULL,'Remise en main propre'),(2,2,'Air Jordan 4 Vivid Sulfur','Mod├¿le embl├®matique Air Jordan, la AJ4 se r├®invente dans un duo de couleurs saisissant, promettant de marquer les esprits en 2024 !',120.00,'tr├¿s bon',3,1,'44','uploads/secondhand/67e2114848155_air-jordan-4-vivid-sulfur3.png','2025-03-25 02:13:28','2025-03-25 02:15:10','actif',0,NULL,'Remise en main propre'),(3,5,'Nike Shox TL Black Max','D├®voil├® aux c├┤t├®s de la version White, la Nike Shox TL, sortie initialement en 2003, revient 20 ans plus tard !',90.00,'moyen',3,1,'41','uploads/secondhand/67e25ff447acf_nike-shox-tl-black-max-orange-w4.png','2025-03-25 07:49:08','2025-03-25 07:49:08','actif',0,NULL,NULL),(4,5,'Adidas Campus 00s Dark Green Cloud White','Apr├¿s la Samba et la Gazelle, Adidas met en avant une nouvelle silhouette inspir├®e du skate et des ann├®es 2000.',80.00,'neuf',3,2,'39','uploads/secondhand/67e4050e45b23_adidas-campus-00s-dark-green-cloud-white-1.png','2025-03-25 07:53:26','2025-03-26 13:45:50','actif',0,NULL,NULL),(5,2,'Le T-Shirt sale de Daniel','il pue trop la vie j&amp;#039;en veux plus',2.00,'usag├®',3,6,'M','uploads/secondhand/67e3f73f8fbd8_1718997583-asap-rocky-awge-fashion-show-1.png','2025-03-26 12:46:04','2025-03-26 12:59:20','supprim├®',0,'Georgie','remise en main propre');
/*!40000 ALTER TABLE `secondhand_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `setting_id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Bander-Sneakers','Nom du site affich├® dans l\'interface','2025-03-24 08:05:54'),(2,'contact_email','contact@bander-sneakers.com','Email de contact pour les notifications','2025-03-21 22:01:34'),(3,'items_per_page','10','Nombre d\'├®l├®ments par page dans les listes admin','2025-03-21 22:01:34'),(4,'currency','Ôé¼','Symbole de la devise utilis├®e','2025-03-21 22:01:34');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sizes` (
  `size_id` int NOT NULL AUTO_INCREMENT,
  `size_value` varchar(10) NOT NULL,
  `size_type` enum('EU','US','UK','CM') DEFAULT 'EU',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sizes`
--

LOCK TABLES `sizes` WRITE;
/*!40000 ALTER TABLE `sizes` DISABLE KEYS */;
INSERT INTO `sizes` VALUES (1,'38','EU','2025-03-19 19:41:21'),(2,'39','EU','2025-03-19 19:41:21'),(3,'40','EU','2025-03-19 19:41:21'),(4,'41','EU','2025-03-19 19:41:21'),(5,'42','EU','2025-03-19 19:41:21'),(6,'43','EU','2025-03-19 19:41:21'),(7,'44','EU','2025-03-19 19:41:21'),(8,'45','EU','2025-03-19 19:41:21'),(9,'6','US','2025-03-19 19:41:21'),(10,'7','US','2025-03-19 19:41:21'),(11,'8','US','2025-03-19 19:41:21'),(12,'9','US','2025-03-19 19:41:21'),(13,'10','US','2025-03-19 19:41:21'),(14,'11','US','2025-03-19 19:41:21'),(15,'12','US','2025-03-19 19:41:21'),(16,'5','UK','2025-03-19 19:41:21'),(17,'6','UK','2025-03-19 19:41:21'),(18,'7','UK','2025-03-19 19:41:21'),(19,'8','UK','2025-03-19 19:41:21'),(20,'9','UK','2025-03-19 19:41:21'),(21,'10','UK','2025-03-19 19:41:21'),(22,'11','UK','2025-03-19 19:41:21');
/*!40000 ALTER TABLE `sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sneaker_images`
--

DROP TABLE IF EXISTS `sneaker_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sneaker_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `sneaker_id` int NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `sneaker_id` (`sneaker_id`),
  CONSTRAINT `sneaker_images_ibfk_1` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sneaker_images`
--

LOCK TABLES `sneaker_images` WRITE;
/*!40000 ALTER TABLE `sneaker_images` DISABLE KEYS */;
INSERT INTO `sneaker_images` VALUES (14,9,'adidas_yeezy_1.jpg',1,'2025-03-19 19:41:21'),(17,10,'67de00083dd78_Sneakers-RS-X-Efekt-PRM (1).avif',0,'2025-03-22 00:10:48'),(22,9,'67de047b5680f_adidas-yeezy-boost-350-v2-onyx-2.webp',0,'2025-03-22 00:29:47'),(23,9,'67de0536db01f_adidas-yeezy-boost-350-v2-onyx-3.webp',0,'2025-03-22 00:32:54'),(24,9,'67de057a84483_adidas-yeezy-boost-350-v2-onyx-4.webp',0,'2025-03-22 00:34:02'),(27,11,'67de08f83ee01_Adidas-Samba-Preloved-Red-Leopard-1.webp',1,'2025-03-22 00:48:56'),(28,11,'67de099334a2e_Adidas-Samba-Preloved-Red-Leopard-2.webp',0,'2025-03-22 00:51:31'),(29,10,'67de13c87b06e_puma_rs_x_1.jpg.avif',1,'2025-03-22 01:35:04'),(36,8,'67de17d0086c9_NIKE+SB+DUNK+LOW+PRO.png',1,'2025-03-22 01:52:16'),(38,7,'67de18c787981_air-jordan-1-mid-grey-sail-1.webp',1,'2025-03-22 01:56:23'),(40,7,'67de82bb96174_air-jordan-1-mid-grey-sail-2.webp',0,'2025-03-22 09:28:27'),(41,6,'67de836654f84_new-balance-574-grey-white-20224.webp',1,'2025-03-22 09:31:18'),(42,6,'67de83665658c_new-balance-574-grey-white-20223.webp',0,'2025-03-22 09:31:18'),(43,5,'67de83d7ea091_Sneakers-Suede-Classic (1).avif',1,'2025-03-22 09:33:11'),(44,5,'67de83d7eb433_Sneakers-Suede-Classic (2).avif',0,'2025-03-22 09:33:11'),(45,4,'67de845ae09d3_ultra-boost-bape-green-camo-864580.webp',1,'2025-03-22 09:35:22'),(46,4,'67de845ae2987_ultra-boost-bape-green-camo-426217.webp',0,'2025-03-22 09:35:22'),(47,3,'67de84d63fee5_adidas-superstar-cloud-white-core-black1-copie.webp',1,'2025-03-22 09:37:26'),(48,3,'67de84d641d20_adidas-superstar-cloud-white-core-black3.webp',0,'2025-03-22 09:37:26'),(49,2,'67de857ccdf53_air-max-97-undftd-black-militia-green-355728.webp',1,'2025-03-22 09:40:12'),(50,2,'67de857ccf8cf_air-max-97-undftd-black-militia-green-138783.webp',0,'2025-03-22 09:40:12'),(51,1,'67de85e0d2121_nike_air_force_1_1.jpg',1,'2025-03-22 09:41:52'),(52,1,'67de85e0d413b_air-force-1-low-07-triple-white-220238.webp',0,'2025-03-22 09:41:52'),(68,21,'67df612059604_Timberland-6_-Boot-Black-Nubuck-Premium-3.webp',1,'2025-03-23 01:17:20'),(69,21,'67df61c44c6a4_Timberland-6_-Boot-Black-Nubuck-Premium_eb131f0d-fe20-4bf5-811f-84c2f7ac60a4.webp',0,'2025-03-23 01:20:04'),(70,22,'67df6793adbe0_salehe-bembury-crocs-pollex-clog-sasquatch-2_137c627d-3b95-4bdd-b037-8b10c06a19a9.webp',1,'2025-03-23 01:44:51'),(71,22,'67df682fa2469_salehe-bembury-crocs-pollex-clog-sasquatch-4.webp',0,'2025-03-23 01:47:27'),(72,8,'67dfd3a659491_NIKE+SB+DUNK+LOW+PRO (1).png',0,'2025-03-23 09:25:58'),(76,23,'67e01006d9c51_Asics-UB3-S-Gel-Nimbus-9-Rum-Raisin-Green-Sheen-1.webp',1,'2025-03-23 13:43:34'),(77,23,'67e01006da83e_Asics-UB3-S-Gel-Nimbus-9-Rum-Raisin-Green-Sheen-2.webp',0,'2025-03-23 13:43:34'),(78,24,'67e01612879cf_salomon-xt-wings-2-jjjjound-cream-blue-2.webp',1,'2025-03-23 14:09:22'),(79,24,'67e0167df06c8_salomon-xt-wings-2-jjjjound-cream-blue-3.webp',0,'2025-03-23 14:11:09'),(80,25,'67e07e77e7cbc_nike-air-humara-lx-jacquemus-pink-wethenew-1.webp',1,'2025-03-23 21:34:47'),(81,25,'67e07ee4b2d36_nike-air-humara-lx-jacquemus-pink-wethenew-2.webp',0,'2025-03-23 21:36:36'),(82,26,'67e07fa61ccf9_Sneakers-Speedcat-OG.avif',1,'2025-03-23 21:39:50'),(83,26,'67e0800f1f2b2_Sneakers-Speedcat-OG (1).avif',0,'2025-03-23 21:41:35'),(86,27,'67e113fd65aba_nike-hot-step-2-nocta-total-orange_23359161_49003737_2048.webp',1,'2025-03-24 08:12:45'),(87,27,'67e113fd674a2_nike-hot-step-2-nocta-total-orange_23359161_49004425_2048.webp',0,'2025-03-24 08:12:45');
/*!40000 ALTER TABLE `sneaker_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sneaker_sizes`
--

DROP TABLE IF EXISTS `sneaker_sizes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sneaker_sizes` (
  `sneaker_size_id` int NOT NULL AUTO_INCREMENT,
  `sneaker_id` int NOT NULL,
  `size_id` int NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sneaker_size_id`),
  UNIQUE KEY `sneaker_id` (`sneaker_id`,`size_id`),
  KEY `size_id` (`size_id`),
  CONSTRAINT `sneaker_sizes_ibfk_1` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE,
  CONSTRAINT `sneaker_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=598 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sneaker_sizes`
--

LOCK TABLES `sneaker_sizes` WRITE;
/*!40000 ALTER TABLE `sneaker_sizes` DISABLE KEYS */;
INSERT INTO `sneaker_sizes` VALUES (194,11,1,49,'2025-03-22 01:28:40','2025-03-22 23:10:05'),(195,11,2,50,'2025-03-22 01:28:40','2025-03-22 01:28:40'),(196,11,3,50,'2025-03-22 01:28:40','2025-03-22 01:28:40'),(197,11,4,50,'2025-03-22 01:28:40','2025-03-22 01:28:40'),(198,11,5,49,'2025-03-22 01:28:40','2025-03-22 23:08:22'),(199,11,6,50,'2025-03-22 01:28:40','2025-03-22 01:28:40'),(200,11,7,49,'2025-03-22 01:28:40','2025-03-22 22:11:43'),(201,11,8,50,'2025-03-22 01:28:40','2025-03-22 01:28:40'),(311,7,3,50,'2025-03-22 09:28:27','2025-03-22 09:28:27'),(312,7,4,49,'2025-03-22 09:28:27','2025-03-22 18:21:18'),(313,7,5,50,'2025-03-22 09:28:27','2025-03-22 09:28:27'),(314,7,6,50,'2025-03-22 09:28:27','2025-03-22 09:28:27'),(315,7,7,50,'2025-03-22 09:28:27','2025-03-22 09:28:27'),(316,6,3,80,'2025-03-22 09:31:18','2025-03-22 09:31:18'),(317,6,4,80,'2025-03-22 09:31:18','2025-03-22 09:31:18'),(318,6,5,79,'2025-03-22 09:31:18','2025-03-23 11:54:47'),(319,6,6,80,'2025-03-22 09:31:18','2025-03-22 09:31:18'),(320,6,7,80,'2025-03-22 09:31:18','2025-03-22 09:31:18'),(321,5,3,120,'2025-03-22 09:33:11','2025-03-22 09:33:11'),(322,5,4,120,'2025-03-22 09:33:11','2025-03-22 09:33:11'),(323,5,5,120,'2025-03-22 09:33:11','2025-03-22 09:33:11'),(324,5,6,119,'2025-03-22 09:33:11','2025-03-22 18:28:26'),(325,5,7,120,'2025-03-22 09:33:11','2025-03-22 09:33:11'),(326,4,3,100,'2025-03-22 09:35:22','2025-03-22 09:35:22'),(327,4,4,99,'2025-03-22 09:35:22','2025-03-22 18:33:07'),(328,4,5,99,'2025-03-22 09:35:22','2025-03-22 23:03:44'),(329,4,6,100,'2025-03-22 09:35:22','2025-03-22 09:35:22'),(330,4,7,100,'2025-03-22 09:35:22','2025-03-22 09:35:22'),(331,3,3,200,'2025-03-22 09:37:26','2025-03-22 09:37:26'),(332,3,4,200,'2025-03-22 09:37:26','2025-03-22 09:37:26'),(333,3,5,200,'2025-03-22 09:37:26','2025-03-22 09:37:26'),(334,3,6,200,'2025-03-22 09:37:26','2025-03-22 09:37:26'),(335,3,7,200,'2025-03-22 09:37:26','2025-03-22 09:37:26'),(344,2,3,74,'2025-03-22 09:40:12','2025-03-22 19:45:25'),(345,2,4,74,'2025-03-22 09:40:12','2025-03-22 18:11:57'),(346,2,5,75,'2025-03-22 09:40:12','2025-03-22 09:40:12'),(347,2,6,75,'2025-03-22 09:40:12','2025-03-22 09:40:12'),(348,2,7,75,'2025-03-22 09:40:12','2025-03-22 09:40:12'),(354,9,3,30,'2025-03-23 00:31:52','2025-03-23 00:31:52'),(355,9,4,30,'2025-03-23 00:31:52','2025-03-23 00:31:52'),(356,9,5,30,'2025-03-23 00:31:52','2025-03-23 00:31:52'),(357,9,6,30,'2025-03-23 00:31:52','2025-03-23 00:31:52'),(358,9,7,30,'2025-03-23 00:31:52','2025-03-23 00:31:52'),(433,21,1,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(434,21,2,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(435,21,3,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(436,21,4,79,'2025-03-23 01:22:06','2025-03-23 01:25:35'),(437,21,5,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(438,21,6,79,'2025-03-23 01:22:06','2025-03-23 11:53:31'),(439,21,7,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(440,21,8,80,'2025-03-23 01:22:06','2025-03-23 01:22:06'),(445,22,1,110,'2025-03-23 01:47:27','2025-03-23 01:47:27'),(446,22,2,108,'2025-03-23 01:47:27','2025-03-23 11:59:45'),(447,22,3,110,'2025-03-23 01:47:27','2025-03-23 01:47:27'),(448,22,4,110,'2025-03-23 01:47:27','2025-03-23 01:47:27'),(449,8,3,65,'2025-03-23 09:25:58','2025-03-23 09:25:58'),(450,8,4,65,'2025-03-23 09:25:58','2025-03-23 09:25:58'),(451,8,5,65,'2025-03-23 09:25:58','2025-03-23 09:25:58'),(452,8,6,65,'2025-03-23 09:25:58','2025-03-23 09:25:58'),(453,8,7,65,'2025-03-23 09:25:58','2025-03-23 09:25:58'),(486,23,1,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(487,23,2,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(488,23,3,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(489,23,4,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(490,23,5,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(491,23,6,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(492,23,7,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(493,23,8,200,'2025-03-23 14:03:43','2025-03-23 14:03:43'),(502,10,1,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(503,10,2,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(504,10,3,89,'2025-03-23 14:05:09','2025-03-26 12:59:53'),(505,10,4,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(506,10,5,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(507,10,6,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(508,10,7,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(509,10,8,90,'2025-03-23 14:05:09','2025-03-23 14:05:09'),(515,24,2,40,'2025-03-23 14:11:09','2025-03-23 14:11:09'),(516,24,3,40,'2025-03-23 14:11:09','2025-03-23 14:11:09'),(517,24,4,40,'2025-03-23 14:11:09','2025-03-23 14:11:09'),(518,24,5,40,'2025-03-23 14:11:09','2025-03-23 14:11:09'),(519,24,6,40,'2025-03-23 14:11:09','2025-03-23 14:11:09'),(544,26,1,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(545,26,2,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(546,26,3,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(547,26,4,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(548,26,5,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(549,26,6,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(550,26,7,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(551,26,8,120,'2025-03-23 21:41:35','2025-03-23 21:41:35'),(560,25,1,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(561,25,2,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(562,25,3,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(563,25,4,86,'2025-03-23 21:57:56','2025-03-24 07:59:51'),(564,25,5,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(565,25,6,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(566,25,7,89,'2025-03-23 21:57:56','2025-03-23 22:17:30'),(567,25,8,90,'2025-03-23 21:57:56','2025-03-23 21:57:56'),(583,27,1,60,'2025-03-24 08:15:07','2025-03-24 08:15:07'),(584,27,2,60,'2025-03-24 08:15:07','2025-03-24 08:15:07'),(585,27,3,60,'2025-03-24 08:15:07','2025-03-24 08:15:07'),(586,27,4,60,'2025-03-24 08:15:07','2025-03-24 08:15:07'),(587,27,5,60,'2025-03-24 08:15:07','2025-03-24 08:15:07'),(593,1,3,150,'2025-03-24 15:31:51','2025-03-24 15:31:51'),(594,1,4,150,'2025-03-24 15:31:51','2025-03-24 15:31:51'),(595,1,5,150,'2025-03-24 15:31:51','2025-03-24 15:31:51'),(596,1,6,150,'2025-03-24 15:31:51','2025-03-24 15:31:51'),(597,1,7,150,'2025-03-24 15:31:51','2025-03-24 15:31:51');
/*!40000 ALTER TABLE `sneaker_sizes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sneakers`
--

DROP TABLE IF EXISTS `sneakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sneakers` (
  `sneaker_id` int NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `sneaker_name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int DEFAULT '0',
  `release_date` date DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_new_arrival` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gender` enum('homme','femme','enfant','unisex') NOT NULL DEFAULT 'unisex',
  `primary_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sneaker_id`),
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `sneakers_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  CONSTRAINT `sneakers_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sneakers`
--

LOCK TABLES `sneakers` WRITE;
/*!40000 ALTER TABLE `sneakers` DISABLE KEYS */;
INSERT INTO `sneakers` VALUES (1,1,3,'Nike Air Force 1','La Nike Air Force 1 est une chaussure de basketball embl├®matique lanc├®e en 1982. Son design intemporel et sa polyvalence en ont fait un classique du streetwear.',129.99,79.99,150,'2023-01-15',1,0,'2025-03-19 19:41:21','2025-03-24 15:31:51','unisex','67de85e0d2121_nike_air_force_1_1.jpg'),(2,1,1,'Nike Air Max 97','Inspir├®e par les trains ├á grande vitesse japonais, la Nike Air Max 97 est reconnaissable ├á sa silhouette futuriste et sa semelle Air-Sole.',179.99,159.99,75,'2023-03-20',1,0,'2025-03-19 19:41:21','2025-03-22 09:40:12','unisex','67de857ccdf53_air-max-97-undftd-black-militia-green-355728.webp'),(3,2,3,'Adidas Superstar','Lanc├®e dans les ann├®es 1970, l&#039;Adidas Superstar est reconnaissable ├á son bout coquille. C&#039;est une ic├┤ne streetwear.',99.99,NULL,200,'2023-02-10',1,0,'2025-03-19 19:41:21','2025-03-22 09:37:26','unisex','67de84d63fee5_adidas-superstar-cloud-white-core-black1-copie.webp'),(4,2,1,'Adidas Ultraboost','L&#039;Adidas Ultraboost offre un confort et un retour d&#039;├®nergie exceptionnels gr├óce ├á sa technologie Boost.',189.99,169.99,100,'2023-04-05',0,1,'2025-03-19 19:41:21','2025-03-22 09:35:22','unisex','67de845ae09d3_ultra-boost-bape-green-camo-864580.webp'),(5,3,3,'Puma Suede Classic','La Puma Suede est une chaussure de lifestyle classique en daim qui a marqu├® l&#039;histoire de la sneaker.',89.99,NULL,120,'2023-01-30',0,0,'2025-03-19 19:41:21','2025-03-22 09:33:11','unisex','67de83d7ea091_Sneakers-Suede-Classic (1).avif'),(6,4,1,'New Balance 574','La New Balance 574 est une chaussure de running devenue un classique du streetwear.',109.99,99.99,80,'2023-03-10',0,1,'2025-03-19 19:41:21','2025-03-22 09:31:18','unisex','67de836654f84_new-balance-574-grey-white-20224.webp'),(7,5,2,'Air Jordan 1','La Air Jordan 1 est la premi├¿re chaussure signature de Michael Jordan, lanc├®e en 1985.',199.99,NULL,50,'2023-05-01',1,1,'2025-03-19 19:41:21','2025-03-22 09:26:06','unisex','67de18c787981_air-jordan-1-mid-grey-sail-1.webp'),(8,1,4,'Nike SB Dunk','La Nike SB Dunk est une adaptation de la Dunk Basketball pour le skateboard.',129.99,NULL,65,'2023-03-25',0,1,'2025-03-19 19:41:21','2025-03-23 09:25:58','unisex','67de17d0086c9_NIKE+SB+DUNK+LOW+PRO.png'),(9,2,5,'Adidas Yeezy','Con├ºue en collaboration avec Kanye West, la Adidas Yeezy est une sneaker tr├¿s recherch├®e.',249.99,NULL,30,'2023-04-15',0,0,'2025-03-19 19:41:21','2025-03-23 00:31:52','unisex','adidas_yeezy_1.jpg'),(10,3,3,'Puma RS-X','La Puma RS-X est une chaussure au design chunky inspir├® des ann├®es 80.',129.99,99.99,90,'2023-02-20',0,1,'2025-03-19 19:41:21','2025-03-23 14:05:09','enfant','67de13c87b06e_puma_rs_x_1.jpg.avif'),(11,2,3,'Adidas Samba OG Preloved Red Leopard','Adidas continue de surfer sur la tendance Leopard avec une nouvelle version de sa Adidas Samba mythique qui risque de faire des envieux !',205.00,NULL,50,'2025-02-01',0,1,'2025-03-22 00:48:56','2025-03-22 01:23:03','femme',NULL),(21,6,5,'Timberland 6 Premium Waterproof Boot Black Nubuck','Fond├®e ├á Boston en 1952, Timberland nous offre une r├®interpr├®tation de sa fameuse botte sortie dans les ann├®es 70&amp;#039;s.',220.00,NULL,80,NULL,1,1,'2025-03-23 01:17:20','2025-03-23 01:22:06','unisex','67df612059604_Timberland-6_-Boot-Black-Nubuck-Premium-3.webp'),(22,6,3,'Crocs Salehe Bembury Crocs Pollex Clog Sasquatch','Le c├®l├¿bre designer de New York s&#039;associe une nouvelle fois avec Crocs sur un coloris sombre de sa silhouette futuriste ! D├®voil├® ├á l&#039;occasion de la Paris Fashion Week, cette ├®dition sp├®ciale f├╗t exclusivement disponible lors d&#039;un pop-up dans la capitale fran├ºaise.',125.00,NULL,110,'2022-06-23',0,1,'2025-03-23 01:44:51','2025-03-23 01:47:27','unisex','67df6793adbe0_salehe-bembury-crocs-pollex-clog-sasquatch-2_137c627d-3b95-4bdd-b037-8b10c06a19a9.webp'),(23,6,3,'ASICS UB3-S Gel Nimbus 9 Rum Raisin Green Sheen','├Ç travers une d├®clinaison aux notes color├®es imagin├®e par le studio de Kiko Kostadinov, Asics pr├®sente sa nouvelle version de la Gel-Nimbus 9.',219.99,199.99,200,'2025-02-27',1,1,'2025-03-23 13:34:39','2025-03-23 14:03:43','unisex','67e01006d9c51_Asics-UB3-S-Gel-Nimbus-9-Rum-Raisin-Green-Sheen-1.webp'),(24,6,1,'Salomon XT-Wings 2 JJJJound Cream Blue','├Ç travers son label JJJJound, Justin Saunders collabore pour la premi├¿re fois avec Salomon et revisite la XT-Wings 2.',765.00,NULL,40,NULL,0,0,'2025-03-23 14:09:22','2025-03-23 14:11:09','unisex','67e01612879cf_salomon-xt-wings-2-jjjjound-cream-blue-2.webp'),(25,1,5,'Nike Air Humara LX Jacquemus Pink','Pour sa premi├¿re collaboration avec Nike, Simon Porte Jacquemus a pris la d├®cision de retravailler son mod├¿le pr├®f├®r├® de la marque en s&amp;amp;#039;inspirant de la gamme ACG des ann├®es 90.',400.00,NULL,90,'2022-12-01',1,1,'2025-03-23 21:34:47','2025-03-23 21:57:56','unisex','67e07e77e7cbc_nike-air-humara-lx-jacquemus-pink-wethenew-1.webp'),(26,3,3,'Puma Speedcat OG Rouge','Un classique de PUMA inspir├® par la vitesse des circuits : la Speedcat OG. Elle se distingue par sa silhouette inspir├®e des chaussures de course et ses lignes ├®pur├®es qui ├®voquent la vitesse et lÔÇÖaudace. Ram├¿ne le sport automobile dans la rue et adopte la coupe basse avec cette nouvelle version de la silhouette embl├®matique.',110.00,100.00,120,NULL,1,1,'2025-03-23 21:39:50','2025-03-23 21:41:35','unisex','67e07fa61ccf9_Sneakers-Speedcat-OG.avif'),(27,1,5,'Nike Hot Step 2 NOCTA Total Orange','Pr├®sent├®e pour la premi├¿re fois aux pieds de Drake lors de sa tourn├®e ┬½ It&amp;amp;#039;s All a Blur ┬╗, la NOCTA x Nike Hot Step 2 ┬½ Total Orange ┬╗ offre la suite de la Hot Step originale de 2021.',260.00,NULL,60,'2021-12-24',0,1,'2025-03-24 08:09:00','2025-03-24 08:15:07','unisex','67e113fd65aba_nike-hot-step-2-nocta-total-orange_23359161_49003737_2048.webp');
/*!40000 ALTER TABLE `sneakers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spin_logs`
--

DROP TABLE IF EXISTS `spin_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spin_logs` (
  `spin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points_won` int NOT NULL,
  `spin_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spin_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `spin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spin_logs`
--

LOCK TABLES `spin_logs` WRITE;
/*!40000 ALTER TABLE `spin_logs` DISABLE KEYS */;
INSERT INTO `spin_logs` VALUES (1,2,100,'2025-03-25 18:21:50'),(2,2,25,'2025-03-25 18:33:34'),(3,2,50,'2025-03-25 18:33:47'),(4,2,50,'2025-03-25 18:33:57'),(5,2,20,'2025-03-25 18:41:11'),(6,2,20,'2025-03-25 18:46:20'),(7,2,50,'2025-03-25 18:46:31'),(8,2,25,'2025-03-25 20:22:45'),(9,2,10,'2025-03-25 20:36:41'),(10,2,0,'2025-03-25 20:36:53'),(11,2,5,'2025-03-25 20:51:52'),(12,2,0,'2025-03-25 21:14:26'),(13,2,0,'2025-03-25 22:42:15'),(14,2,25,'2025-03-25 22:42:34'),(15,2,25,'2025-03-25 22:43:00'),(16,2,50,'2025-03-26 08:05:18'),(17,2,50,'2025-03-26 08:05:31'),(18,2,0,'2025-03-26 08:07:37'),(19,2,0,'2025-03-26 08:07:52'),(20,2,5,'2025-03-26 08:10:30'),(21,2,0,'2025-03-26 08:11:41'),(22,2,0,'2025-03-26 08:12:30'),(23,2,0,'2025-03-26 08:12:40'),(24,2,5,'2025-03-26 08:13:14'),(25,2,25,'2025-03-26 08:13:52'),(26,2,5,'2025-03-26 09:34:51'),(27,2,5,'2025-03-26 09:37:03'),(28,1,0,'2025-03-26 12:54:40'),(29,2,0,'2025-03-26 13:57:51');
/*!40000 ALTER TABLE `spin_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@bander-sneakers.com','$2y$10$mdbWzk4hR8SZISHg2xlQLed5SwruwLmfKhcapsYlmkkiUFQoL5w0u','Admin','User','','','','France','',1,'2025-03-19 19:41:21','2025-03-23 11:52:01'),(2,'Terrel','nuentsa.terrel@gmail.com','$2y$10$YO3Agj8RAZ7iMwsUKo0nquDAlgcwgRum5TROR97QiqwksWdPd3oDK','Terrel','NUENTSA','13 rue Gambetta','Puteaux','92800','France','0780774144',0,'2025-03-19 22:18:14','2025-03-22 18:16:37'),(5,'Keren','keren.viva@gmail.com','$2y$10$hxNO3Eo97HMjf/NVdrLFNuMrO3ny1kdN7VDTw0DhXjAx7vz8xwQem','Keren','MAKAMBO VIVA',NULL,NULL,NULL,NULL,NULL,0,'2025-03-23 00:27:48','2025-03-23 00:27:48'),(6,'Romain','43010388@gmail.com','$2y$10$Jleyj8xiRr92CNm/VpZCnOa1m6w4fcB1eR3oaEF0JabUjV8YC6tkq','Romain','THIERRY','','','','','',0,'2025-03-24 09:06:42','2025-03-24 14:00:22');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlist` (
  `wishlist_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `sneaker_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `user_id` (`user_id`,`sneaker_id`),
  KEY `sneaker_id` (`sneaker_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`sneaker_id`) REFERENCES `sneakers` (`sneaker_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (20,5,21,'2025-03-23 01:24:41'),(88,2,25,'2025-03-26 13:58:32'),(90,2,26,'2025-03-26 13:58:41');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-26 22:02:57
