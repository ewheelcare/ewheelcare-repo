-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 05, 2025 at 08:21 PM
-- Server version: 8.0.31
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expert_tyre`
--

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int NOT NULL AUTO_INCREMENT,
  `country_name` varchar(2500) DEFAULT NULL,
  `country_gdp` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`, `country_gdp`) VALUES
(3, 'pakistan', '5000'),
(2, 'INDIA', '7000'),
(4, 'sri lanka', '5200'),
(5, 'bangladesh', '2000');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(2500) DEFAULT NULL,
  `company_address` varchar(2500) DEFAULT NULL,
  `owner_name` varchar(2500) DEFAULT NULL,
  `owner_mobile` varchar(2500) DEFAULT NULL,
  `owner_aadhar` int DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `company_name`, `company_address`, `owner_name`, `owner_mobile`, `owner_aadhar`) VALUES
(1, 'aa', 'ff', 'kjloooo', '08912513017', 2147483647),
(2, 'name', 'address', 'owner', '9638527410', 2147483647),
(3, 'test', 'address', 'owner ', '3698527410', 2147483647),
(4, '', '', '', '', 0),
(5, '', '', '', '', 0),
(6, '1233', 'aaaaaa', 'kjloooo', '08912513017', 2147483647),
(7, 'new customer', 'new address', 'new address', '08912513017', 2147483647),
(8, 'new customer for demo', 'new address for demo', 'new owner for demo', '3698521470', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `expert_login`
--

DROP TABLE IF EXISTS `expert_login`;
CREATE TABLE IF NOT EXISTS `expert_login` (
  `user_name` varchar(70) NOT NULL,
  `user_role` varchar(70) NOT NULL,
  `user_id` varchar(70) NOT NULL,
  `user_pwd` varchar(70) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expert_login`
--

INSERT INTO `expert_login` (`user_name`, `user_role`, `user_id`, `user_pwd`) VALUES
('Sriya Basumallik', 'SA', 'admin', 'adminpwd');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `inventory_id` int NOT NULL AUTO_INCREMENT,
  `item_id` varchar(2500) DEFAULT NULL,
  `shop_id` varchar(2500) DEFAULT NULL,
  `qty` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`inventory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `item_id`, `shop_id`, `qty`) VALUES
(1, '1', '5', '7');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(2500) DEFAULT NULL,
  `item_description` varchar(2500) DEFAULT NULL,
  `cost` varchar(2500) DEFAULT NULL,
  `tax_pc` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `item_name`, `item_description`, `cost`, `tax_pc`) VALUES
(1, 'item 1', 'first item', '700', '18');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `location_id` int NOT NULL AUTO_INCREMENT,
  `location_name` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `location_name`) VALUES
(1, 'Gajuwaka'),
(2, 'Anakapalli'),
(3, 'Agnampudi');

-- --------------------------------------------------------

--
-- Table structure for table `receipt_trans`
--

DROP TABLE IF EXISTS `receipt_trans`;
CREATE TABLE IF NOT EXISTS `receipt_trans` (
  `trans_id` int NOT NULL,
  `trans_date` date NOT NULL,
  `details` varchar(700) NOT NULL,
  `vendor` varchar(70) NOT NULL,
  `trans_amount` decimal(10,0) NOT NULL,
  `active_status` varchar(7) NOT NULL,
  `pending` decimal(10,0) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `receipt_trans`
--

INSERT INTO `receipt_trans` (`trans_id`, `trans_date`, `details`, `vendor`, `trans_amount`, `active_status`, `pending`) VALUES
(1, '2025-08-20', 'details edited', '2', '0', 'A', '0');

-- --------------------------------------------------------

--
-- Table structure for table `receipt_trans_det`
--

DROP TABLE IF EXISTS `receipt_trans_det`;
CREATE TABLE IF NOT EXISTS `receipt_trans_det` (
  `trans_id` int NOT NULL,
  `subtrans_id` int NOT NULL,
  `item_id` varchar(70) NOT NULL,
  `cost` decimal(10,0) NOT NULL,
  `qty` int NOT NULL,
  `total` decimal(10,0) NOT NULL,
  `tax` decimal(10,0) NOT NULL,
  `tax_amount` decimal(10,0) NOT NULL,
  `active_status` varchar(7) NOT NULL,
  `created_by` varchar(70) NOT NULL,
  `created_on` date NOT NULL,
  `modified_by` varchar(70) NOT NULL,
  `modified_on` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE IF NOT EXISTS `shop` (
  `shop_id` int NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(2500) DEFAULT NULL,
  `location_id` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shop`
--

INSERT INTO `shop` (`shop_id`, `shop_name`, `location_id`) VALUES
(1, 'SHOP 1', NULL),
(2, 'SHOP 10', NULL),
(3, 'SHOP 10', '2'),
(4, 'shop agnampudi', '3'),
(5, 'shop @ agnampudi opened newly', '3');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

DROP TABLE IF EXISTS `state`;
CREATE TABLE IF NOT EXISTS `state` (
  `state_id` int NOT NULL AUTO_INCREMENT,
  `state_name` varchar(2500) DEFAULT NULL,
  `country_id` varchar(2500) DEFAULT NULL,
  `state_gdp` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`state_id`),
  KEY `fk_state_country_id` (`country_id`(250))
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `state_name`, `country_id`, `state_gdp`) VALUES
(1, 'AP', '2', NULL),
(2, NULL, '2', '16000'),
(3, NULL, '3', '12000'),
(4, 'WEST BENGAL', '2', '16000'),
(5, 'COLOMBO', '4', '12400'),
(6, 'new state for test', '2', '23000');

-- --------------------------------------------------------

--
-- Table structure for table `state2`
--

DROP TABLE IF EXISTS `state2`;
CREATE TABLE IF NOT EXISTS `state2` (
  `state2_id` int NOT NULL AUTO_INCREMENT,
  `state_name` varchar(2500) DEFAULT NULL,
  `country_id` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`state2_id`),
  KEY `fk_state_m_country_id` (`country_id`(250)),
  KEY `fk_state2_country_id` (`country_id`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyre_type`
--

DROP TABLE IF EXISTS `tyre_type`;
CREATE TABLE IF NOT EXISTS `tyre_type` (
  `tyre_type_id` int NOT NULL AUTO_INCREMENT,
  `tyre_type_name` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`tyre_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tyre_type`
--

INSERT INTO `tyre_type` (`tyre_type_id`, `tyre_type_name`) VALUES
(1, 'MRF'),
(2, 'ARF'),
(3, 'BRF');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE IF NOT EXISTS `vehicle` (
  `vehicle_id` int NOT NULL AUTO_INCREMENT,
  `vehicle_no` varchar(2500) DEFAULT NULL,
  `vehicle_brand` varchar(2500) DEFAULT NULL,
  `vehicle_model` varchar(2500) DEFAULT NULL,
  `customer_id` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `vehicle_no`, `vehicle_brand`, `vehicle_model`, `customer_id`) VALUES
(1, 'WB1234TV', 'MARUTI', 'WAGON R', '3'),
(2, 'AP31TV1234', 'MARUTI', 'WAGON R', '7');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
CREATE TABLE IF NOT EXISTS `vendor` (
  `vendor_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(2500) DEFAULT NULL,
  `company_address` varchar(2500) DEFAULT NULL,
  `owner_name` varchar(2500) DEFAULT NULL,
  `owner_mobile` varchar(2500) DEFAULT NULL,
  `owner_aadhar` int DEFAULT NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`vendor_id`, `company_name`, `company_address`, `owner_name`, `owner_mobile`, `owner_aadhar`) VALUES
(1, 'vendor no one', 'address', 'owner', '08912513017', 2147483647),
(2, 'anotehr vendor', 'address', 'owner of vendor', '08912513017', 2147483647),
(3, 'vendor no two', 'new address for demo', 'owner for vendor', '08912513017', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `{`
--

DROP TABLE IF EXISTS `{`;
CREATE TABLE IF NOT EXISTS `{` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
