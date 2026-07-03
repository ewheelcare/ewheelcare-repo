-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 10, 2025 at 04:18 AM
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
) ENGINE=MyISAM AUTO_INCREMENT=6 ;

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
  `credit_amount` int DEFAULT NULL,
  `credit_days` int DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 ;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `company_name`, `company_address`, `owner_name`, `owner_mobile`, `owner_aadhar`, `credit_amount`, `credit_days`) VALUES
(1, 'aa', 'ff', 'kjloooo', '08912513017', 2147483647, NULL, NULL),
(2, 'name', 'address', 'owner', '9638527410', 2147483647, NULL, NULL),
(3, 'test', 'address', 'owner ', '3698527410', 2147483647, NULL, NULL),
(4, '', '', '', '', 0, NULL, NULL),
(5, '', '', '', '', 0, NULL, NULL),
(6, '1233', 'aaaaaa', 'kjloooo', '08912513017', 2147483647, NULL, NULL),
(7, 'new customer', 'new address', 'new address', '08912513017', 2147483647, NULL, NULL),
(8, 'new customer for demo', 'new address for demo', 'new owner for demo', '3698521470', 2147483647, NULL, NULL);

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
) ENGINE=MyISAM ;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 ;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `item_id`, `shop_id`, `qty`) VALUES
(1, '1', '5', '10'),
(2, '1', '4', '1'),
(3, '1', '1', '1'),
(4, '1', '3', '1'),
(5, '1', '2', '1'),
(6, '1', NULL, '4'),
(7, '1', NULL, '26');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 ;

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
) ENGINE=MyISAM ;

--
-- Dumping data for table `receipt_trans`
--

INSERT INTO `receipt_trans` (`trans_id`, `trans_date`, `details`, `vendor`, `trans_amount`, `active_status`, `pending`) VALUES
(1, '2025-08-20', 'details edited', '2', '91686', 'Z', '0');

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
) ENGINE=MyISAM ;

--
-- Dumping data for table `receipt_trans_det`
--

INSERT INTO `receipt_trans_det` (`trans_id`, `subtrans_id`, `item_id`, `cost`, `qty`, `total`, `tax`, `tax_amount`, `active_status`, `created_by`, `created_on`, `modified_by`, `modified_on`) VALUES
(1, 1, '', '700', 7, '4900', '18', '882', 'A', 'admin', '2025-09-06', '', '0000-00-00'),
(1, 2, '', '700', 2, '1400', '18', '252', 'A', 'admin', '2025-09-07', '', '0000-00-00'),
(0, 1, '', '0', 0, '0', '0', '0', 'A', 'admin', '2025-09-07', '', '0000-00-00'),
(1, 3, '', '700', 2, '1400', '18', '252', 'A', 'admin', '2025-09-07', '', '2025-09-10'),
(1, 4, '', '700', 3, '2100', '18', '378', 'A', '', '2025-09-10', '', '2025-09-10'),
(1, 5, '1', '700', 70, '49000', '18', '8820', 'Z', '', '2025-09-10', '', '2025-09-10'),
(1, 6, '1', '700', 26, '18200', '18', '3276', 'Z', '', '2025-09-10', '', '2025-09-10'),
(1, 7, '', '700', 4, '2800', '18', '504', 'A', '', '2025-09-10', '', '2025-09-10');

-- --------------------------------------------------------

--
-- Table structure for table `sales_trans`
--

DROP TABLE IF EXISTS `sales_trans`;
CREATE TABLE IF NOT EXISTS `sales_trans` (
  `trans_id` int NOT NULL,
  `trans_date` date DEFAULT NULL,
  `details` varchar(700) DEFAULT NULL,
  `customer` varchar(700) DEFAULT NULL,
  `trans_amount` decimal(10,0) DEFAULT NULL,
  `pending` decimal(10,0) DEFAULT NULL,
  `gst` varchar(7) DEFAULT NULL,
  `tally` varchar(700) DEFAULT NULL,
  `created_by` varchar(70) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `modified_by` varchar(70) DEFAULT NULL,
  `modified_on` date DEFAULT NULL,
  `active_status` varchar(7) DEFAULT NULL
) ENGINE=MyISAM ;

--
-- Dumping data for table `sales_trans`
--

INSERT INTO `sales_trans` (`trans_id`, `trans_date`, `details`, `customer`, `trans_amount`, `pending`, `gst`, `tally`, `created_by`, `created_on`, `modified_by`, `modified_on`, `active_status`) VALUES
(1, '2025-09-08', 'buying something', '7', NULL, '160', 'Y', '', NULL, NULL, NULL, NULL, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `sales_trans_det`
--

DROP TABLE IF EXISTS `sales_trans_det`;
CREATE TABLE IF NOT EXISTS `sales_trans_det` (
  `trans_id` int NOT NULL,
  `subtrans_id` int NOT NULL,
  `item_id` varchar(700) DEFAULT NULL,
  `cost` decimal(10,0) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `tax` decimal(10,0) DEFAULT NULL,
  `tax_amount` decimal(10,0) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL,
  `created_by` varchar(700) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `modified_by` varchar(700) DEFAULT NULL,
  `modified_on` date DEFAULT NULL,
  `active_status` varchar(7) DEFAULT NULL,
  `vehicle` varchar(70) DEFAULT NULL,
  `shop_id` varchar(7) DEFAULT NULL
) ENGINE=MyISAM ;

--
-- Dumping data for table `sales_trans_det`
--

INSERT INTO `sales_trans_det` (`trans_id`, `subtrans_id`, `item_id`, `cost`, `qty`, `tax`, `tax_amount`, `total`, `created_by`, `created_on`, `modified_by`, `modified_on`, `active_status`, `vehicle`, `shop_id`) VALUES
(1, 1, '1', '700', 2, '18', '252', '1400', '', '2025-09-08', NULL, NULL, 'A', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(2500) DEFAULT NULL,
  `service_description` varchar(2500) DEFAULT NULL,
  `cost` varchar(2500) DEFAULT NULL,
  `tax_pc` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 ;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`service_id`, `service_name`, `service_description`, `cost`, `tax_pc`) VALUES
(1, 'Service 1', 'Service to cars', '2500', '12'),
(2, 'Service 2', 'Service to trucks', '2700', '12');

-- --------------------------------------------------------

--
-- Table structure for table `service_trans`
--

DROP TABLE IF EXISTS `service_trans`;
CREATE TABLE IF NOT EXISTS `service_trans` (
  `trans_id` int NOT NULL,
  `trans_date` date DEFAULT NULL,
  `details` varchar(700) DEFAULT NULL,
  `customer` varchar(700) DEFAULT NULL,
  `trans_amount` decimal(10,0) DEFAULT NULL,
  `pending` decimal(10,0) DEFAULT NULL,
  `gst` varchar(7) DEFAULT NULL,
  `tally` varchar(700) DEFAULT NULL,
  `created_by` varchar(70) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `modified_by` varchar(70) DEFAULT NULL,
  `modified_on` date DEFAULT NULL,
  `active_status` varchar(7) DEFAULT NULL
) ENGINE=MyISAM ;

--
-- Dumping data for table `service_trans`
--

INSERT INTO `service_trans` (`trans_id`, `trans_date`, `details`, `customer`, `trans_amount`, `pending`, `gst`, `tally`, `created_by`, `created_on`, `modified_by`, `modified_on`, `active_status`) VALUES
(1, '2025-09-07', 'service from me', '', '0', '700', 'N', '', NULL, NULL, NULL, NULL, 'A'),
(2, '2025-09-07', 'receipt from me', '', NULL, '700', 'N', '', NULL, NULL, NULL, NULL, 'A'),
(3, '2025-09-07', 'det', '', NULL, '700', 'N', '', NULL, NULL, NULL, NULL, 'A'),
(4, '2025-09-07', 'details edited', '7', NULL, '700', 'N', '', NULL, NULL, NULL, NULL, 'Z'),
(5, '2025-09-07', 'another one please', '8', '10824', '706', 'N', '', NULL, NULL, NULL, NULL, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `service_trans_det`
--

DROP TABLE IF EXISTS `service_trans_det`;
CREATE TABLE IF NOT EXISTS `service_trans_det` (
  `trans_id` int NOT NULL,
  `subtrans_id` int NOT NULL,
  `service_id` varchar(700) DEFAULT NULL,
  `cost` decimal(10,0) DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `tax` decimal(10,0) DEFAULT NULL,
  `tax_amount` decimal(10,0) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL,
  `created_by` varchar(700) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `modified_by` varchar(700) DEFAULT NULL,
  `modified_on` date DEFAULT NULL,
  `active_status` varchar(7) DEFAULT NULL,
  `vehicle` varchar(70) DEFAULT NULL
) ENGINE=MyISAM ;

--
-- Dumping data for table `service_trans_det`
--

INSERT INTO `service_trans_det` (`trans_id`, `subtrans_id`, `service_id`, `cost`, `qty`, `tax`, `tax_amount`, `total`, `created_by`, `created_on`, `modified_by`, `modified_on`, `active_status`, `vehicle`) VALUES
(5, 1, '2', '2700', 1, '12', '324', '2700', 'admin', '2025-09-07', 'admin', '2025-09-07', 'A', '3'),
(5, 2, '1', '2500', 1, '12', '300', '2500', 'admin', '2025-09-07', 'admin', '2025-09-07', 'Z', '3'),
(5, 3, '1', '2500', 2, '12', '600', '5000', 'admin', '2025-09-07', 'admin', '2025-09-07', 'Z', '3'),
(5, 4, '1', '2500', 1, '12', '300', '2500', 'admin', '2025-09-07', NULL, NULL, 'A', '3'),
(5, 5, '1', '2500', 1, '0', '0', '2500', 'admin', '2025-09-07', NULL, NULL, 'A', '3'),
(5, 6, '1', '2500', 1, '0', '0', '2500', '', '2025-09-10', NULL, NULL, 'A', '3'),
(5, 7, '2', '2700', 1, '0', '0', '2700', '', '2025-09-10', '', '2025-09-10', 'Z', '3');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 ;

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
) ENGINE=MyISAM AUTO_INCREMENT=7 ;

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
) ENGINE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `tyre_type`
--

DROP TABLE IF EXISTS `tyre_type`;
CREATE TABLE IF NOT EXISTS `tyre_type` (
  `tyre_type_id` int NOT NULL AUTO_INCREMENT,
  `tyre_type_name` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`tyre_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 ;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 ;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `vehicle_no`, `vehicle_brand`, `vehicle_model`, `customer_id`) VALUES
(1, 'WB1234TV', 'MARUTI', 'WAGON R', '3'),
(2, 'AP31TV1234', 'MARUTI', 'WAGON R', '7'),
(3, 'AP31TV1234', 'MARUTI', 'WAGON R', '8');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 ;

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
) ENGINE=MyISAM ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
