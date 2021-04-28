-- MySQL dump 10.17  Distrib 10.3.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ansible
-- ------------------------------------------------------
-- Server version	10.3.25-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(40) NOT NULL,
  `os_family` varchar(20) DEFAULT NULL,
  `distro` varchar(40) DEFAULT NULL,
  `distro_release` varchar(40) DEFAULT NULL,
  `distro_mver` int(5) DEFAULT NULL,
  `distro_ver` varchar(10) DEFAULT NULL,
  `kernel` varchar(255) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_serial` varchar(100) DEFAULT NULL,
  `product_ver` varchar(30) DEFAULT NULL,
  `arch` varchar(10) DEFAULT NULL,
  `sys_vendor` varchar(60) DEFAULT NULL,
  `virt_type` varchar(60) DEFAULT NULL,
  `virt_role` varchar(60) DEFAULT NULL,
  `uptime_sec` varchar(20) DEFAULT NULL,
  `fqdn` varchar(70) DEFAULT NULL,
  `hostname` varchar(70) DEFAULT NULL,
  `nodename` varchar(70) DEFAULT NULL,
  `is_chroot` varchar(10) DEFAULT NULL,
  `iscsi_iqn` varchar(100) DEFAULT NULL,
  `cpu_cores` varchar(4) DEFAULT NULL,
  `cpu_count` varchar(4) DEFAULT NULL,
  `cpu_threads_per_core` varchar(4) DEFAULT NULL,
  `cpu_vcpus` varchar(4) DEFAULT NULL,
  `system_capabilities_enforced` varchar(1) DEFAULT NULL,
  `epoch_time` varchar(50) DEFAULT NULL,
  `bios_ver` varchar(20) DEFAULT NULL,
  `bios_date` varchar(20) DEFAULT NULL,
  `boot_image` varchar(255) DEFAULT NULL,
  `selinux_status` varchar(50) DEFAULT NULL,
  `selinux_mode` varchar(20) DEFAULT NULL,
  `selinux_type` varchar(20) DEFAULT NULL,
  `service_mgr` varchar(20) DEFAULT NULL,
  `python_ver` varchar(10) DEFAULT NULL,
  `all_ipsv4` text DEFAULT NULL,
  `all_ipsv6` text DEFAULT NULL,
  `main_ip_address` varchar(20) DEFAULT NULL,
  `main_ip_netmask` varchar(20) DEFAULT NULL,
  `main_ip_gateway` varchar(20) DEFAULT NULL,
  `main_ip_interface` varchar(20) DEFAULT NULL,
  `main_ip_mac` varchar(20) DEFAULT NULL,
  `main_ip_network` varchar(20) DEFAULT NULL,
  `main_ip_type` varchar(20) DEFAULT NULL,
  `domain` varchar(100) DEFAULT NULL,
  `dns_ns` varchar(255) DEFAULT NULL,
  `sys_cap` text DEFAULT NULL,
  `memory_free` int(10) DEFAULT NULL,
  `memory_total` int(10) DEFAULT NULL,
  `memory_swap_free` int(10) DEFAULT NULL,
  `memory_swap_total` int(10) DEFAULT NULL,
  `devices` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `mounts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `lvm` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `full` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `username` varchar(100) NOT NULL,
  `password` varchar(60) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-04-27 22:19:52
