-- MySQL dump 10.13  Distrib 8.0.23, for Linux (x86_64)
--
-- Host: localhost    Database: hotpads
-- ------------------------------------------------------
-- Server version	8.0.23-0ubuntu0.20.04.1

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
-- Table structure for table `availability`
--

DROP TABLE IF EXISTS `availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `availability` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `property_id` bigint NOT NULL,
  `bedroom_cnt` varchar(32) DEFAULT NULL,
  `bathroom_cnt` varchar(32) DEFAULT NULL,
  `listing_price` varchar(32) DEFAULT NULL,
  `home_size_sq_ft` varchar(32) DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `create_at` date DEFAULT NULL,
  `update_at` date DEFAULT NULL,
  `image_urls` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `availability`
--

LOCK TABLES `availability` WRITE;
/*!40000 ALTER TABLE `availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `properties` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `address` varchar(256) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `latitude` varchar(64) DEFAULT NULL,
  `longitude` varchar(64) DEFAULT NULL,
  `contact_type` varchar(64) DEFAULT NULL,
  `contact_person` varchar(256) DEFAULT NULL,
  `contact_company` varchar(256) DEFAULT NULL,
  `contact_phone` varchar(32) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `building_units` varchar(64) DEFAULT NULL,
  `addr_line_1` varchar(128) DEFAULT NULL,
  `addr_line_2` varchar(128) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state_cd` char(2) DEFAULT NULL,
  `zip5_cd` varchar(32) DEFAULT NULL,
  `image_urls` text,
  `listing_comments` text,
  `virtual_tour_urls` text,
  `pet_policy` text,
  `outdoor_space` varchar(128) DEFAULT NULL,
  `on_premise_services` text,
  `walk_score` varchar(128) DEFAULT NULL,
  `transit_score` varchar(128) DEFAULT NULL,
  `nearby_colleges` text,
  `nearby_rail` text,
  `nearby_transit` text,
  `nearby_shopping` text,
  `nearby_parks` text,
  `nearby_airports` text,
  `neighborhood_comments` text,
  `listing_last_updated` varchar(32) DEFAULT NULL,
  `utilities_included` varchar(256) DEFAULT NULL,
  `building_security` varchar(128) DEFAULT NULL,
  `living_space` varchar(128) DEFAULT NULL,
  `on_premise_features` text,
  `student_features` varchar(256) DEFAULT NULL,
  `kitchen` varchar(256) DEFAULT NULL,
  `parking` text,
  `property_info` text,
  `building_features` text,
  `subdivision` varchar(32) DEFAULT NULL,
  `building_desc` text,
  `building_name` varchar(128) DEFAULT NULL,
  `builiding_office_hours` text,
  `link` varchar(255) NOT NULL,
  `expences` text,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `nearby_school` text,
  `create_at` date DEFAULT NULL,
  `update_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-11 13:56:35
