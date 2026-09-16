-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: commeettee
-- ------------------------------------------------------
-- Server version	8.4.7

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
-- Current Database: `commeettee`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `commeettee` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `commeettee`;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `actor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'System',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `aspirant_id` int unsigned NOT NULL,
  `posting_id` int unsigned NOT NULL,
  `note` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status_seen` tinyint(1) NOT NULL DEFAULT '1',
  `seen_by_client` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `decided_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_application` (`aspirant_id`,`posting_id`),
  KEY `posting_id` (`posting_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`aspirant_id`) REFERENCES `users` (`id`),
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`posting_id`) REFERENCES `postings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES ('decorations','Decorations','Shapes the look and feel of the venue, from overall layout down to the smallest visual detail.','assets/committee-decorations.jpg'),('documentation','Documentation','Captures every milestone in photos, videos, and files, so nothing about the event goes unrecorded.','assets/committee-documentation.jpg'),('logistics','Logistics','Keeps people, supplies, and schedules moving so every event runs on time and on plan.','assets/committee-technicals.jpg'),('technicals','Technicals','Handles the technical equipment, setup, and operations needed to ensure smooth event execution.','assets/committee-technicals.jpg');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int unsigned NOT NULL,
  `sender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_by_admin` tinyint(1) NOT NULL DEFAULT '0',
  `read_by_client` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `chat_threads` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_threads`
--

DROP TABLE IF EXISTS `chat_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_threads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `guest_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_message_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user` (`user_id`),
  UNIQUE KEY `unique_guest_token` (`guest_token`),
  CONSTRAINT `chat_threads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_threads`
--

LOCK TABLES `chat_threads` WRITE;
/*!40000 ALTER TABLE `chat_threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postings`
--

DROP TABLE IF EXISTS `postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL,
  `category_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `skills_needed` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `slots` int unsigned NOT NULL DEFAULT '1',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `moderation_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `postings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  CONSTRAINT `postings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postings`
--

LOCK TABLES `postings` WRITE;
/*!40000 ALTER TABLE `postings` DISABLE KEYS */;
INSERT INTO `postings` VALUES (1,3,'technicals','AV & Live Stream Technical Crew','Operate audio mixers, stage microphones, LED monitors, and manage YouTube/FB live stream broadcasts for IT assemblies and symposiums.','Audio/Visual setup, OBS Studio / vMix, cable management, hardware diagnostics',4,'open','approved','','2026-09-16 22:33:06'),(2,5,'technicals','Hackathon & Lab Technical Support Aide','Configure LAN networking, maintain testing workstations, and resolve technical issues for student programmers during university hackathons.','Basic networking, Linux / Windows setup, router configuration, troubleshooting',3,'open','approved','','2026-09-16 22:33:06'),(3,3,'documentation','Photo & Video Documentation Specialist','Capture high-resolution photos and video highlights of all departmental events, draft caption stories, and curate the media repository.','DSLR/Mirrorless camera operation, Adobe Lightroom / Premiere, creative storytelling',3,'open','approved','','2026-09-16 22:33:06'),(4,4,'documentation','Campus Event Minutes & Media Archivist','Record committee proceedings, draft official press releases for student publications, and archive event documentation portfolios.','Technical writing, documentation filing, Google Workspace / MS Office, attention to detail',2,'open','approved','','2026-09-16 22:33:06'),(5,4,'decorations','Stage Backdrop & Creative Production Team','Conceptualize, craft, and assemble thematic stage backgrounds, entrance installations, and floral/podium arrangements for university festivities.','Visual arts, backdrop fabrication, stage lighting concepts, craft craftsmanship',4,'open','approved','','2026-09-16 22:33:06'),(6,6,'decorations','Exhibition Booth & Poster Designer','Design informative and visually striking advocacy booths, health fair exhibits, and campus bulletin board displays.','Graphic design, layout planning, poster printing coordination, creative styling',2,'open','approved','','2026-09-16 22:33:06'),(7,4,'logistics','University Arena Logistics & Floor Coordinator','Manage stage ingress/egress, transport sound and furniture equipment, oversee delegate registration booths, and enforce event timetables.','Physical inventory, teamwork, time management, crowd coordination',6,'open','approved','','2026-09-16 22:33:06'),(8,6,'logistics','First-Aid Station & Supply Logistics Officer','Organize first-aid response posts, track emergency supplies and medical inventory, and assist marshals during campus mass gatherings.','Inventory tracking, emergency response awareness, orderly coordination',3,'open','approved','','2026-09-16 22:33:06');
/*!40000 ALTER TABLE `postings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `aspirant_id` int unsigned NOT NULL,
  `client_id` int unsigned NOT NULL,
  `application_id` int unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`application_id`),
  KEY `aspirant_id` (`aspirant_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`aspirant_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` int unsigned DEFAULT NULL,
  `reporter_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Guest',
  `target_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int unsigned NOT NULL,
  `target_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reason` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reporter_id` (`reporter_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aspirant',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `org_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `year_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `skills` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `availability` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bio` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@commeettee.local','$2y$10$JtXkYbKmWhiEka5QczUmcuEuIj4gdPCPtRPd7M498oSNYknXbDwC6','admin','active','','','','','','','','2026-09-16 20:26:16'),(2,'Ciara Amber Saycon','ciaraamberx23@gmail.com','$2y$10$2UrG8NTt8uSTVJnhY84D3uEtcJr8/u1Hdw6c2hddRq7mv0tZqvekK','aspirant','active','','','Bachelor of Science in Information Technology','3rd Year','Information Technology Society','','','2026-09-16 21:09:45'),(3,'Information Technology Society','its@norsu.edu.ph','$2y$10$ZjRVwVlaSlDVwOY9tQ/Z1O5eV4PNyILHRw8qnuIVILv2IJTTlKCeC','client','active','','Information Technology Organization (ITO / ITS)','Bachelor of Science in Information Technology','','','','The official academic student organization of the Information Technology department at Negros Oriental State University.','2026-09-16 22:33:06'),(4,'League of Student Organizations','lso@norsu.edu.ph','$2y$10$Hpe.4wOcM/OTqHIIOTG16.VUIclwTBPYmuw6l8OF0zrdR..r4b0s6','client','active','','League of Student Organizations (LSO)','Student Affairs Office','','','','The umbrella organization overseeing and coordinating all recognized student groups, clubs, and events across NORSU.','2026-09-16 22:33:06'),(5,'Computer Science Guild','csg@norsu.edu.ph','$2y$10$gfU6nLu4O7PAa4uv8kEdnONEJ2jO/HcX9mZxNEswg7fYsoGII1M1G','client','active','','Computer Science Guild (CSG)','Bachelor of Science in Computer Science','','','','Student organization fostering software engineering, algorithm competitions, and tech innovation at NORSU.','2026-09-16 22:33:06'),(6,'NORSU Red Cross Youth Council','rcy@norsu.edu.ph','$2y$10$0ox5IgqIK9fGm/OSk6WjheUsd.etNm0mNTREAMD1FI/VeINnM2a92','client','active','','NORSU Red Cross Youth Council','Health & Community Services','','','','University chapter committed to humanitarian work, disaster response preparedness, and campus health drives.','2026-09-16 22:33:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-17  6:54:39
