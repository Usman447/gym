-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: gymie_db
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.22.04.2

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
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `size` int unsigned NOT NULL,
  `manipulations` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `custom_properties` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `media_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('2018_03_07_055231_create_media_table',1),('2018_03_07_055231_create_mst_enquiries_table',1),('2018_03_07_055231_create_mst_expenses_categories_table',1),('2018_03_07_055231_create_mst_members_table',1),('2018_03_07_055231_create_mst_plans_table',1),('2018_03_07_055231_create_mst_services_table',1),('2018_03_07_055231_create_mst_sms_events_table',1),('2018_03_07_055231_create_mst_sms_triggers_table',1),('2018_03_07_055231_create_mst_users_table',1),('2018_03_07_055231_create_password_resets_table',1),('2018_03_07_055231_create_permission_role_table',1),('2018_03_07_055231_create_permissions_table',1),('2018_03_07_055231_create_role_user_table',1),('2018_03_07_055231_create_roles_table',1),('2018_03_07_055231_create_trn_access_log_table',1),('2018_03_07_055231_create_trn_cheque_details_table',1),('2018_03_07_055231_create_trn_enquiry_followups_table',1),('2018_03_07_055231_create_trn_expenses_table',1),('2018_03_07_055231_create_trn_invoice_details_table',1),('2018_03_07_055231_create_trn_invoice_table',1),('2018_03_07_055231_create_trn_payment_details_table',1),('2018_03_07_055231_create_trn_settings_table',1),('2018_03_07_055231_create_trn_sms_log_table',1),('2018_03_07_055231_create_trn_subscriptions_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_enquiries_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_expenses_categories_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_members_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_plans_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_services_table',1),('2018_03_07_055232_add_foreign_keys_to_mst_sms_events_table',1),('2018_03_07_055232_add_foreign_keys_to_permission_role_table',1),('2018_03_07_055232_add_foreign_keys_to_role_user_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_access_log_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_cheque_details_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_enquiry_followups_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_expenses_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_invoice_details_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_invoice_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_payment_details_table',1),('2018_03_07_055232_add_foreign_keys_to_trn_subscriptions_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_enquiries`
--

DROP TABLE IF EXISTS `mst_enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_enquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique record ID',
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `address` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 = Lost , 1 = Lead  , 2 =Member',
  `contact` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `gender` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pin_code` int NOT NULL,
  `occupation` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_by` date NOT NULL,
  `interested_in` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `aim` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `source` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mst_enquiries_mst_staff_1` (`created_by`),
  KEY `FK_mst_enquiries_mst_staff_2` (`updated_by`),
  CONSTRAINT `FK_mst_enquiries_mst_staff_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_enquiries_mst_staff_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_enquiries`
--

LOCK TABLES `mst_enquiries` WRITE;
/*!40000 ALTER TABLE `mst_enquiries` DISABLE KEYS */;
/*!40000 ALTER TABLE `mst_enquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_expenses_categories`
--

DROP TABLE IF EXISTS `mst_expenses_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_expenses_categories` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id for system',
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'category name',
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mst_expenses_categories_mst_users_1` (`created_by`),
  KEY `FK_mst_expenses_categories_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_mst_expenses_categories_mst_users_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_expenses_categories_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_expenses_categories`
--

LOCK TABLES `mst_expenses_categories` WRITE;
/*!40000 ALTER TABLE `mst_expenses_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `mst_expenses_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_members`
--

DROP TABLE IF EXISTS `mst_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_members` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id for system',
  `member_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Unique member id for reference',
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s name',
  `photo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s photo',
  `DOB` date NOT NULL COMMENT 'member''s date of birth',
  `email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s email id',
  `address` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s address',
  `status` tinyint(1) NOT NULL COMMENT '0 for inactive , 1 for active',
  `proof_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'name of the proof provided by member',
  `proof_photo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'photo of the proof',
  `gender` char(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s gender',
  `contact` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'member''s contact number',
  `emergency_contact` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `health_issues` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pin_code` int NOT NULL,
  `occupation` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `aim` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `source` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_code`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `contact` (`contact`),
  KEY `FK_mst_members_mst_users_1` (`created_by`),
  KEY `FK_mst_members_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_mst_members_mst_users_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_members_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_members`
--

LOCK TABLES `mst_members` WRITE;
/*!40000 ALTER TABLE `mst_members` DISABLE KEYS */;
INSERT INTO `mst_members` VALUES (7,'MEM5','Usman','','2000-02-15','usman@fhsjf.com','No',1,'Usman','','m','03211234567','03211234567','Nothing',12345,'0','0','0','2025-10-26 00:00:00','2025-10-26 00:00:00',1,1);
/*!40000 ALTER TABLE `mst_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_plans`
--

DROP TABLE IF EXISTS `mst_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_plans` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id for system',
  `plan_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'Unique plan id for reference',
  `service_id` int NOT NULL,
  `plan_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'name of the plan',
  `plan_details` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'plan details',
  `days` int NOT NULL COMMENT 'duration of the plans in days',
  `amount` int NOT NULL COMMENT 'amount to charge for the plan',
  `status` tinyint(1) NOT NULL COMMENT '0 for inactive , 1 for active',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_id` (`plan_code`),
  KEY `FK_mst_plans_mst_services` (`service_id`),
  KEY `FK_mst_plans_mst_users_1` (`created_by`),
  KEY `FK_mst_plans_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_mst_plans_mst_services` FOREIGN KEY (`service_id`) REFERENCES `mst_services` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_plans_mst_users_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_plans_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_plans`
--

LOCK TABLES `mst_plans` WRITE;
/*!40000 ALTER TABLE `mst_plans` DISABLE KEYS */;
INSERT INTO `mst_plans` VALUES (4,'PLN001',3,'Plan 2','Plan 1',30,5000,1,'2025-10-26 20:01:27','2025-10-26 20:01:27',1,1);
/*!40000 ALTER TABLE `mst_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_services`
--

DROP TABLE IF EXISTS `mst_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mst_services_mst_users_1` (`created_by`),
  KEY `FK_mst_services_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_mst_services_mst_users_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_services_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_services`
--

LOCK TABLES `mst_services` WRITE;
/*!40000 ALTER TABLE `mst_services` DISABLE KEYS */;
INSERT INTO `mst_services` VALUES (3,'Basic','Basic','2025-10-26 20:01:00','2025-10-26 20:01:00',1,1);
/*!40000 ALTER TABLE `mst_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_sms_events`
--

DROP TABLE IF EXISTS `mst_sms_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_sms_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `message` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(140) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `send_to` int NOT NULL COMMENT '0 = active members , 1 = inactive members , 2= lead enquiries , 3 = lost enquiries',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mst_sms_events_mst_users_1` (`created_by`),
  KEY `FK_mst_sms_events_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_mst_sms_events_mst_users_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_mst_sms_events_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_sms_events`
--

LOCK TABLES `mst_sms_events` WRITE;
/*!40000 ALTER TABLE `mst_sms_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `mst_sms_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_sms_triggers`
--

DROP TABLE IF EXISTS `mst_sms_triggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_sms_triggers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alias` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `message` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_sms_triggers`
--

LOCK TABLES `mst_sms_triggers` WRITE;
/*!40000 ALTER TABLE `mst_sms_triggers` DISABLE KEYS */;
INSERT INTO `mst_sms_triggers` VALUES (1,'Member admission (Paid)','member_admission_with_paid_invoice','Hi %s , Welcome to %s . Your payment of Rs %u against your invoice no. %s has been received. Thank you and we hope to see you in action soon. Good day!',0,'2025-10-24 02:56:18'),(2,'Member admission (Partial)','member_admission_with_partial_invoice','Hi %s , Welcome to %s . Your payment of Rs %u against your invoice no. %s has been received. Outstanding payment to be cleared is Rs %u .Thank you!',0,'2025-10-24 02:56:18'),(3,'Member admission (Unpaid)','member_admission_with_unpaid_invoice','Hi %s , Welcome to %s . Your payment of Rs %u is pending against your invoice no. %s . Thank you!',0,'2025-10-24 02:56:18'),(4,'Enquiry placement','enquiry_placement','Hi %s , Thank you for your enquiry with %s . We would love to hear from you soon. Good day!',0,'2025-10-24 02:56:18'),(5,'Followup','followup','Hi %s , This is regarding the inquiry you placed at %s . Let us know by when would you like to get started? Good day!',0,'2025-10-24 02:56:18'),(6,'Subscription renewal (Paid)','subscription_renewal_with_paid_invoice','Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u against your invoice no. %s  has been received. Thank you!',0,'2025-10-24 02:56:18'),(7,'Subscription renewal (Partial)','subscription_renewal_with_partial_invoice','Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u against your invoice no. %s has been received. Outstanding payment to be cleared is Rs %u . Thank you!',0,'2025-10-24 02:56:18'),(8,'Subscription renewal (Unpaid)','subscription_renewal_with_unpaid_invoice','Hi %s , Your subscription has been renewed successfully. Your payment of Rs %u is pending against your invoice no. %s . Thank you!',0,'2025-10-24 02:56:18'),(9,'Subscription expiring','subscription_expiring','Hi %s ,  Last few days to renew your gym subscription. Kindly renew it before %s . Thank you!',0,'2025-10-24 02:56:18'),(10,'Subscription expired','subscription_expired','Hi %s , Your gym subscription has been expired on %s . Kindly renew it soon!',0,'2025-10-24 02:56:18'),(11,'Payment recieved','payment_recieved','Hi %s , Your payment of Rs %u  has been received against your invoice no. %s . Thank you!',0,'2025-10-24 02:56:18'),(12,'Pending invoice','pending_invoice','Hi %s , Your payment of Rs %u is still pending against your invoice no. %s . Kindly clear it soon!',0,'2025-10-24 02:56:18'),(13,'Expense alertexpense_alert','expense_alert','Hi , You have an expense lined up for%s of Rs %u on %s . Thank you!',0,'2025-10-24 02:56:18'),(14,'Member birthday wishes','member_birthday','Hi %s , Team %s wishes you a very Happy birthday :) Enjoy your day!Payment with cheque',0,'2025-10-24 02:56:18'),(15,'Payment with cheque','payment_with_cheque','Hi %s , your cheque of Rs %u with cheque no. %u has been recieved against your invoice no. %s . Regards %s .',0,'2025-10-24 02:56:18');
/*!40000 ALTER TABLE `mst_sms_triggers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_users`
--

DROP TABLE IF EXISTS `mst_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mst_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_users`
--

LOCK TABLES `mst_users` WRITE;
/*!40000 ALTER TABLE `mst_users` DISABLE KEYS */;
INSERT INTO `mst_users` VALUES (1,'gym-super-admin','admin@gym.com','$2y$10$kIxOCa8tRfHkFaowoIu7/OBoi3XYFPcvgbYN5B5ZYd2Axl1WGIAkm',1,'n0SKcMbJ8QYTlGZH1XLqZ6bN1nKYwmpNN5TojvEUvHwbTmGzIi6MnnDdMH7I','2025-10-23 21:56:19','2025-10-26 19:53:59'),(2,'Manager','manager@gmail.com','$2y$10$YQqO/64NvNdtp12c.lgHl.NVhu6HbQ79.yD3H9K./iEkg86Wb3FZ.',1,'OqElWVud26ZiBSACG6q92pJDbQ4A2tYavTvGNvuwBSzb26PMhrN5UhAHHjTZ','2025-10-23 22:09:43','2025-10-24 18:09:08'),(3,'Admin','admin@gmail.com','$2y$10$yvyajGJ/BRAJcSS7H4KEcuJxnt5O/2wZ12H7q1B8DJ6tb4BGWnAna',1,'PgZmCjXz5aLb8NI3hrjXq6vFFqDh7wcP31UqHC5ql0Hl8PqTo41eAitZignC','2025-10-23 22:10:19','2025-10-26 17:53:15'),(4,'Usman','usman@gymstation.com','$2y$10$Olu5thClLBNgPIR5vfcIpu8W3mElQueYbf4Lz0mmigUcczlVK6VJO',1,'2bBchO4zzKInYexMRr43AlMzp1vS7blma1CzIOsUv26OuPzeFE5tWP6NAok1','2025-10-26 19:52:59','2025-10-26 20:00:17');
/*!40000 ALTER TABLE `mst_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `permission_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(44,1),(51,1),(2,4),(6,4),(7,4),(8,4),(17,4),(18,4),(20,4),(28,4),(29,4),(30,4),(53,4);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `group_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage-gymie','Manage Gymie','','Global','2025-10-23 21:56:18','2025-10-23 21:56:18'),(2,'view-dashboard-quick-stats','View quick stats on dashboard','','Dashboard','2025-10-23 21:56:18','2025-10-23 21:56:18'),(3,'view-dashboard-charts','View charts on dashboard','','Dashboard','2025-10-23 21:56:18','2025-10-23 21:56:18'),(4,'view-dashboard-members-tab','View members tab on dashboard','','Dashboard','2025-10-23 21:56:18','2025-10-23 21:56:18'),(5,'view-dashboard-enquiries-tab','View enquiries tab on dashboard','','Dashboard','2025-10-23 21:56:18','2025-10-23 21:56:18'),(6,'add-member','Add member','','Members','2025-10-23 21:56:18','2025-10-23 21:56:18'),(7,'view-member','View member details','','Members','2025-10-23 21:56:18','2025-10-23 21:56:18'),(8,'edit-member','Edit member details','','Members','2025-10-23 21:56:18','2025-10-23 21:56:18'),(9,'delete-member','Delete member','','Members','2025-10-23 21:56:19','2025-10-23 21:56:19'),(10,'add-plan','Add plans','','Plans','2025-10-23 21:56:19','2025-10-23 21:56:19'),(11,'view-plan','View plan details','','Plans','2025-10-23 21:56:19','2025-10-23 21:56:19'),(12,'edit-plan','Edit plan details','','Plans','2025-10-23 21:56:19','2025-10-23 21:56:19'),(13,'delete-plan','Delete plans','','Plans','2025-10-23 21:56:19','2025-10-23 21:56:19'),(14,'add-subscription','Add subscription','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(15,'edit-subscription','Edit subscription details','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(16,'renew-subscription','Renew subscription','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(17,'view-invoice','View invoice','','Invoices','2025-10-23 21:56:19','2025-10-23 21:56:19'),(18,'add-payment','Add payments','','Payments','2025-10-23 21:56:19','2025-10-23 21:56:19'),(19,'view-subscription','View subscription details','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(20,'view-payment','View payment details','','Payments','2025-10-23 21:56:19','2025-10-23 21:56:19'),(21,'edit-payment','Edit payment details','','Payments','2025-10-23 21:56:19','2025-10-23 21:56:19'),(22,'manage-members','Manage members','','Members','2025-10-23 21:56:19','2025-10-23 21:56:19'),(23,'manage-plans','Manage plans','','Plans','2025-10-23 21:56:19','2025-10-23 21:56:19'),(24,'manage-subscriptions','Manage subscriptions','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(25,'manage-invoices','Manage invoices','','Invoices','2025-10-23 21:56:19','2025-10-23 21:56:19'),(26,'manage-payments','Manage payments','','Payments','2025-10-23 21:56:19','2025-10-23 21:56:19'),(27,'manage-users','Manage users','','Users','2025-10-23 21:56:19','2025-10-23 21:56:19'),(28,'add-enquiry','Add enquiry','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(29,'view-enquiry','View enquiry details','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(30,'edit-enquiry','Edit enquiry details','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(31,'add-enquiry-followup','Add enquiry followup','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(32,'edit-enquiry-followup','Edit enquiry followup','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(33,'transfer-enquiry','Transfer enquiry','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(34,'manage-enquiries','Manage enquiries','','Enquiries','2025-10-23 21:56:19','2025-10-23 21:56:19'),(35,'add-expense','Add expense','','Expenses','2025-10-23 21:56:19','2025-10-23 21:56:19'),(36,'view-expense','View expense details','','Expenses','2025-10-23 21:56:19','2025-10-23 21:56:19'),(37,'edit-expense','Edit expense details','','Expenses','2025-10-23 21:56:19','2025-10-23 21:56:19'),(38,'manage-expenses','Manage expenses','','Expenses','2025-10-23 21:56:19','2025-10-23 21:56:19'),(39,'add-expenseCategory','Add expense category','','Expense Categories','2025-10-23 21:56:19','2025-10-23 21:56:19'),(40,'view-expenseCategory','View expense categories','','Expense Categories','2025-10-23 21:56:19','2025-10-23 21:56:19'),(41,'edit-expenseCategory','Edit expense category details','','Expense Categories','2025-10-23 21:56:19','2025-10-23 21:56:19'),(42,'delete-expenseCategory','Delete expense category','','Expense Categories','2025-10-23 21:56:19','2025-10-23 21:56:19'),(43,'manage-expenseCategories','Manage expense categories','','Expense Categories','2025-10-23 21:56:19','2025-10-23 21:56:19'),(44,'manage-settings','Manage settings','','Global','2025-10-23 21:56:19','2025-10-23 21:56:19'),(45,'cancel-subscription','Cancel subscription','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(46,'manage-services','Manage services','','Services','2025-10-23 21:56:19','2025-10-23 21:56:19'),(47,'add-service','Add services','','Services','2025-10-23 21:56:19','2025-10-23 21:56:19'),(48,'edit-service','Edit service details','','Services','2025-10-23 21:56:19','2025-10-23 21:56:19'),(49,'view-service','View service details','','Services','2025-10-23 21:56:19','2025-10-23 21:56:19'),(50,'manage-sms','Manage SMS','','SMS','2025-10-23 21:56:19','2025-10-23 21:56:19'),(51,'pagehead-stats','View pagehead counts','','Global','2025-10-23 21:56:19','2025-10-23 21:56:19'),(52,'view-dashboard-expense-tab','View expenses tab on dashboard','','Dashboard','2025-10-23 21:56:19','2025-10-23 21:56:19'),(53,'print-invoice','Print invoices','','Invoices','2025-10-23 21:56:19','2025-10-23 21:56:19'),(54,'delete-invoice','Delete invoices','','Invoices','2025-10-23 21:56:19','2025-10-23 21:56:19'),(55,'delete-subscription','Delete subscriptions','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19'),(56,'delete-payment','Delete payment transactions','','Payments','2025-10-23 21:56:19','2025-10-23 21:56:19'),(57,'delete-expense','Delete expense details','','Expenses','2025-10-23 21:56:19','2025-10-23 21:56:19'),(58,'delete-service','Delete Service details','','Services','2025-10-23 21:56:19','2025-10-23 21:56:19'),(59,'add-discount','Add discount on a invoice','','Invoices','2025-10-23 21:56:19','2025-10-23 21:56:19'),(60,'change-subscription','Upgrade or downgrade a subscription','','Subscriptions','2025-10-23 21:56:19','2025-10-23 21:56:19');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `mst_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1),(4,4);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Gym-super-admin','super-admin','super-admin','2025-10-23 21:56:18','2025-10-23 23:58:06'),(4,'Manager','Manager','Manager','2025-10-26 19:52:04','2025-10-26 19:52:04');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_access_log`
--

DROP TABLE IF EXISTS `trn_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_access_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `module` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `record` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_activities_mst_users_1` (`user_id`),
  CONSTRAINT `FK_trn_activities_mst_users_1` FOREIGN KEY (`user_id`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_access_log`
--

LOCK TABLES `trn_access_log` WRITE;
/*!40000 ALTER TABLE `trn_access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `trn_access_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_cheque_details`
--

DROP TABLE IF EXISTS `trn_cheque_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_cheque_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `number` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 = recieved , 1 = deposited , 2 = cleared , 3 = bounced',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_cheque_details_trn_payment_details` (`payment_id`),
  KEY `FK_trn_cheque_details_mst_users` (`created_by`),
  KEY `FK_trn_cheque_details_mst_users_2` (`updated_by`),
  CONSTRAINT `FK_trn_cheque_details_mst_users` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_cheque_details_mst_users_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_cheque_details_trn_payment_details` FOREIGN KEY (`payment_id`) REFERENCES `trn_payment_details` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_cheque_details`
--

LOCK TABLES `trn_cheque_details` WRITE;
/*!40000 ALTER TABLE `trn_cheque_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `trn_cheque_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_enquiry_followups`
--

DROP TABLE IF EXISTS `trn_enquiry_followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_enquiry_followups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `enquiry_id` int unsigned NOT NULL,
  `followup_by` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `outcome` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 = Pending , 1 = Done',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_enquiry_followups_mst_enquiries_1` (`enquiry_id`),
  KEY `FK_trn_enquiry_followups_mst_staff_2` (`created_by`),
  KEY `FK_trn_enquiry_followups_mst_staff_3` (`updated_by`),
  CONSTRAINT `FK_trn_enquiry_followups_mst_enquiries_1` FOREIGN KEY (`enquiry_id`) REFERENCES `mst_enquiries` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_enquiry_followups_mst_staff_2` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_enquiry_followups_mst_staff_3` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_enquiry_followups`
--

LOCK TABLES `trn_enquiry_followups` WRITE;
/*!40000 ALTER TABLE `trn_enquiry_followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `trn_enquiry_followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_expenses`
--

DROP TABLE IF EXISTS `trn_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_expenses` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id for system',
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'name of the expense',
  `category_id` int NOT NULL COMMENT 'name of the category of expense',
  `amount` int NOT NULL COMMENT 'expense amount',
  `due_date` date NOT NULL COMMENT 'Due Date for the expense created',
  `repeat` tinyint(1) NOT NULL COMMENT '0 = never repeat , 1 = every day , 2 = every week , 3 = every month , 4 = every year',
  `paid` tinyint(1) NOT NULL COMMENT '0 = false , 1 = true i.e. paid',
  `note` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_expenses_mst_expenses_categories_1` (`category_id`),
  KEY `FK_trn_expenses_mst_users_2` (`created_by`),
  KEY `FK_trn_expenses_mst_users_3` (`updated_by`),
  CONSTRAINT `FK_trn_expenses_mst_expenses_categories_1` FOREIGN KEY (`category_id`) REFERENCES `mst_expenses_categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_expenses_mst_users_2` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_expenses_mst_users_3` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_expenses`
--

LOCK TABLES `trn_expenses` WRITE;
/*!40000 ALTER TABLE `trn_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `trn_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_invoice`
--

DROP TABLE IF EXISTS `trn_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_invoice` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id for system',
  `member_id` int NOT NULL COMMENT 'links to unique record id of mst_members',
  `total` int NOT NULL COMMENT 'total fees/amount generated',
  `pending_amount` int NOT NULL COMMENT 'pending amount',
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'note regarding payments',
  `status` tinyint(1) NOT NULL COMMENT '0 = Unpaid, 1 = Paid,  2 = Partial 3 = overpaid',
  `invoice_number` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'number of the inovice/reciept',
  `discount_percent` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_amount` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `discount_note` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  `tax` int NOT NULL,
  `additional_fees` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_invoice_mst_members_1` (`member_id`),
  KEY `FK_trn_payments_mst_users_3` (`created_by`),
  KEY `FK_trn_payments_mst_users_4` (`updated_by`),
  CONSTRAINT `FK_trn_invoice_mst_members_1` FOREIGN KEY (`member_id`) REFERENCES `mst_members` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_invoice_mst_staff_1` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_invoice_mst_staff_2` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_invoice`
--

LOCK TABLES `trn_invoice` WRITE;
/*!40000 ALTER TABLE `trn_invoice` DISABLE KEYS */;
INSERT INTO `trn_invoice` VALUES (7,7,5000,0,' ',1,'INV5','0','0','','2025-10-26 00:00:00','2025-10-26 20:11:05',1,1,0,0);
/*!40000 ALTER TABLE `trn_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_invoice_details`
--

DROP TABLE IF EXISTS `trn_invoice_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_invoice_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL COMMENT 'links to unique record id of trn_invoice',
  `item_amount` int NOT NULL COMMENT 'amount of the items',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  `plan_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_trn_invoice_details_trn_invoice_1` (`invoice_id`),
  KEY `FK_trn_invoice_details_mst_staff_2` (`created_by`),
  KEY `FK_trn_invoice_details_mst_staff_3` (`updated_by`),
  KEY `trn_invoice_details_plan_id_foreign` (`plan_id`),
  CONSTRAINT `FK_trn_invoice_details_mst_staff_2` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_invoice_details_mst_staff_3` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_invoice_details_trn_invoice_1` FOREIGN KEY (`invoice_id`) REFERENCES `trn_invoice` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `trn_invoice_details_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `mst_plans` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_invoice_details`
--

LOCK TABLES `trn_invoice_details` WRITE;
/*!40000 ALTER TABLE `trn_invoice_details` DISABLE KEYS */;
INSERT INTO `trn_invoice_details` VALUES (5,7,5000,'2025-10-26 00:00:00','2025-10-26 00:00:00',1,1,4);
/*!40000 ALTER TABLE `trn_invoice_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_payment_details`
--

DROP TABLE IF EXISTS `trn_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_payment_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL COMMENT 'links to unique record id of trn_invoice',
  `payment_amount` int NOT NULL COMMENT 'amount of transaction being done',
  `mode` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT '1 = Cash , 0 = Cheque',
  `note` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT 'misc. note',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_payment_details_1` (`invoice_id`),
  KEY `FK_trn_payment_details_mst_staff_2` (`created_by`),
  KEY `FK_trn_payment_details_mst_staff_3` (`updated_by`),
  CONSTRAINT `FK_trn_payment_details_1` FOREIGN KEY (`invoice_id`) REFERENCES `trn_invoice` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_payment_details_mst_staff_2` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_payment_details_mst_staff_3` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_payment_details`
--

LOCK TABLES `trn_payment_details` WRITE;
/*!40000 ALTER TABLE `trn_payment_details` DISABLE KEYS */;
INSERT INTO `trn_payment_details` VALUES (9,7,3000,'1',' ','2025-10-26 00:00:00','2025-10-26 00:00:00',1,1),(10,7,2000,'1','','2025-10-26 20:11:05','2025-10-26 20:11:05',1,1);
/*!40000 ALTER TABLE `trn_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_settings`
--

DROP TABLE IF EXISTS `trn_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_settings`
--

LOCK TABLES `trn_settings` WRITE;
/*!40000 ALTER TABLE `trn_settings` DISABLE KEYS */;
INSERT INTO `trn_settings` VALUES (1,'financial_start','','2025-10-26 20:06:14'),(2,'financial_end','','2025-10-26 20:06:14'),(3,'gym_logo','','2025-10-24 02:56:18'),(4,'gym_name','GYM','2025-10-26 20:06:14'),(5,'gym_address_1','','2025-10-26 20:06:14'),(6,'gym_address_2','','2025-10-26 20:06:14'),(7,'invoice_prefix','INV','2025-10-26 20:06:14'),(8,'invoice_last_number','5','2025-10-26 20:09:24'),(9,'invoice_name_type','gym_name','2025-10-26 20:06:14'),(10,'invoice_number_mode','1','2025-10-26 20:06:14'),(11,'member_prefix','MEM','2025-10-26 20:06:14'),(12,'member_last_number','5','2025-10-26 20:09:24'),(13,'member_number_mode','1','2025-10-26 20:06:14'),(14,'last_membership_check','','2025-10-24 02:56:18'),(15,'admission_fee','0','2025-10-26 20:06:14'),(16,'taxes','0','2025-10-26 20:06:14'),(17,'sms_api_key','','2025-10-24 02:56:18'),(18,'sms_sender_id','','2025-10-24 02:56:18'),(19,'sms','0','2025-10-24 10:54:46'),(20,'primary_contact','','2025-10-24 10:54:46'),(21,'discounts','5,10,15,20,25','2025-10-26 20:06:14'),(22,'sms_balance','0','2025-10-24 02:56:18'),(23,'sms_request','0','2025-10-24 02:56:18'),(24,'sender_id_list','','2025-10-24 02:56:18');
/*!40000 ALTER TABLE `trn_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_sms_log`
--

DROP TABLE IF EXISTS `trn_sms_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_sms_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `message` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `shoot_id` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'NA',
  `send_time` datetime NOT NULL,
  `sender_id` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_sms_log`
--

LOCK TABLES `trn_sms_log` WRITE;
/*!40000 ALTER TABLE `trn_sms_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `trn_sms_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trn_subscriptions`
--

DROP TABLE IF EXISTS `trn_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trn_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL COMMENT 'links to unique record id of mst_members',
  `invoice_id` int NOT NULL COMMENT 'links to unique record id of trn_invoice',
  `plan_id` int NOT NULL COMMENT 'links to unique record if of mst_plans',
  `start_date` date NOT NULL COMMENT 'start date of subscription',
  `end_date` date NOT NULL COMMENT 'end date of subscription',
  `status` tinyint(1) NOT NULL COMMENT '0 = expired, 1 = ongoing, 2 = renewed, 3 = canceled',
  `is_renewal` tinyint(1) NOT NULL COMMENT '0= false , 1=true',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int unsigned NOT NULL,
  `updated_by` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trn_subscriptions_mst_members_1` (`member_id`),
  KEY `FK_trn_subscriptions_trn_invoice` (`invoice_id`),
  KEY `FK_trn_subscriptions_mst_plans_2` (`plan_id`),
  KEY `FK_trn_subscriptions_mst_staff_3` (`created_by`),
  KEY `FK_trn_subscriptions_mst_staff_4` (`updated_by`),
  CONSTRAINT `FK_trn_subscriptions_mst_members_1` FOREIGN KEY (`member_id`) REFERENCES `mst_members` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_subscriptions_mst_plans_2` FOREIGN KEY (`plan_id`) REFERENCES `mst_plans` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_subscriptions_mst_staff_3` FOREIGN KEY (`created_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_subscriptions_mst_staff_4` FOREIGN KEY (`updated_by`) REFERENCES `mst_users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_trn_subscriptions_trn_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `trn_invoice` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trn_subscriptions`
--

LOCK TABLES `trn_subscriptions` WRITE;
/*!40000 ALTER TABLE `trn_subscriptions` DISABLE KEYS */;
INSERT INTO `trn_subscriptions` VALUES (5,7,7,4,'2025-10-26','2025-11-24',1,0,'2025-10-26 00:00:00','2025-10-26 00:00:00',1,1);
/*!40000 ALTER TABLE `trn_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-27 11:01:18
