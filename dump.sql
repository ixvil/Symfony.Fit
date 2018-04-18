-- MySQL dump 10.13  Distrib 5.7.21, for osx10.13 (x86_64)
--
-- Host: localhost    Database: fit
-- ------------------------------------------------------
-- Server version	5.7.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `hall`
--

DROP TABLE IF EXISTS `hall`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hall`
--

LOCK TABLES `hall` WRITE;
/*!40000 ALTER TABLE `hall` DISABLE KEYS */;
/*!40000 ALTER TABLE `hall` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson`
--

DROP TABLE IF EXISTS `lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_set_id` int(11) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_halls_id_fk` (`hall_id`),
  KEY `lessons_lesson_sets_id_fk` (`lesson_set_id`),
  CONSTRAINT `lessons_halls_id_fk` FOREIGN KEY (`hall_id`) REFERENCES `hall` (`id`),
  CONSTRAINT `lessons_lesson_sets_id_fk` FOREIGN KEY (`lesson_set_id`) REFERENCES `lesson_set` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson`
--

LOCK TABLES `lesson` WRITE;
/*!40000 ALTER TABLE `lesson` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_set`
--

DROP TABLE IF EXISTS `lesson_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer_user_id` int(11) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `lesson_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_sets_users_id_fk` (`trainer_user_id`),
  KEY `lesson_set_lesson_type_id_fk` (`lesson_type_id`),
  CONSTRAINT `lesson_set_lesson_type_id_fk` FOREIGN KEY (`lesson_type_id`) REFERENCES `lesson_type` (`id`),
  CONSTRAINT `lesson_sets_users_id_fk` FOREIGN KEY (`trainer_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_set`
--

LOCK TABLES `lesson_set` WRITE;
/*!40000 ALTER TABLE `lesson_set` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson_set` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_type`
--

DROP TABLE IF EXISTS `lesson_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_type`
--

LOCK TABLES `lesson_type` WRITE;
/*!40000 ALTER TABLE `lesson_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_user`
--

DROP TABLE IF EXISTS `lesson_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_ticket_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_users_lessons_id_fk` (`lesson_id`),
  KEY `lesson_users_users_id_fk` (`user_id`),
  KEY `lesson_users_user_tickets_id_fk` (`user_ticket_id`),
  CONSTRAINT `lesson_users_lessons_id_fk` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`),
  CONSTRAINT `lesson_users_user_tickets_id_fk` FOREIGN KEY (`user_ticket_id`) REFERENCES `user_ticket` (`id`),
  CONSTRAINT `lesson_users_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_user`
--

LOCK TABLES `lesson_user` WRITE;
/*!40000 ALTER TABLE `lesson_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_plan`
--

DROP TABLE IF EXISTS `ticket_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lessons_count` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `days_to_outdated` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_plans_ticket_plan_types_id_fk` (`type_id`),
  CONSTRAINT `ticket_plans_ticket_plan_types_id_fk` FOREIGN KEY (`type_id`) REFERENCES `ticket_plan_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_plan`
--

LOCK TABLES `ticket_plan` WRITE;
/*!40000 ALTER TABLE `ticket_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_plan_type`
--

DROP TABLE IF EXISTS `ticket_plan_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_plan_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_plan_type`
--

LOCK TABLES `ticket_plan_type` WRITE;
/*!40000 ALTER TABLE `ticket_plan_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_plan_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_user_types_id_fk` (`type_id`),
  CONSTRAINT `users_user_types_id_fk` FOREIGN KEY (`type_id`) REFERENCES `user_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_ticket`
--

DROP TABLE IF EXISTS `user_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_plan_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_created_at` datetime DEFAULT NULL,
  `lessons_expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_tickets_ticket_plans_id_fk` (`ticket_plan_id`),
  KEY `user_tickets_users_id_fk` (`user_id`),
  CONSTRAINT `user_tickets_ticket_plans_id_fk` FOREIGN KEY (`ticket_plan_id`) REFERENCES `ticket_plan` (`id`),
  CONSTRAINT `user_tickets_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_ticket`
--

LOCK TABLES `user_ticket` WRITE;
/*!40000 ALTER TABLE `user_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_type`
--

LOCK TABLES `user_type` WRITE;
/*!40000 ALTER TABLE `user_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-04-19  2:38:24
