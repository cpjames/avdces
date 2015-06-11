-- phpMyAdmin SQL Dump
-- version 4.0.10.8
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: 2015 年 05 月 29 日 16:09
-- 伺服器版本: 5.1.73
-- PHP 版本: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `avdces`
--

-- --------------------------------------------------------

--
-- 資料表結構 `hagroup`
--

Create DATABASE avdces;

Use avdces;

CREATE TABLE IF NOT EXISTS `hagroup` (
  `hg_id` int(11) NOT NULL AUTO_INCREMENT,
  `hg_name` varchar(50) NOT NULL,
  `hg_port` int(5) NOT NULL,
  `hg_mode` varchar(20) NOT NULL,
  `hg_net` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`hg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- 資料表結構 `haproxy`
--

CREATE TABLE IF NOT EXISTS `haproxy` (
  `h_id` int(11) NOT NULL AUTO_INCREMENT,
  `h_name` varchar(50) DEFAULT NULL,
  `h_vm_name` varchar(50) DEFAULT NULL,
  `h_net` varchar(20) DEFAULT NULL,
  `h_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`h_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- 資料表結構 `iso`
--

CREATE TABLE IF NOT EXISTS `iso` (
  `i_id` int(10) NOT NULL AUTO_INCREMENT,
  `i_name` varchar(50) NOT NULL,
  `i_path` varchar(50) NOT NULL,
  PRIMARY KEY (`i_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 資料表結構 `net`
--

CREATE TABLE IF NOT EXISTS `net` (
  `uuid` varchar(50) NOT NULL,
  `net_name` varchar(25) NOT NULL,
  `forwarding` varchar(20) NOT NULL,
  `net_mac` varchar(20) NOT NULL,
  `net_network` varchar(20) NOT NULL,
  `dhcp_start` varchar(20) NOT NULL,
  `dhcp_stop` varchar(20) NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `net_name` (`net_name`),
  UNIQUE KEY `net_network` (`net_network`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `net_vip`
--

CREATE TABLE IF NOT EXISTS `net_vip` (
  `vip_id` int(10) NOT NULL AUTO_INCREMENT,
  `vip_mac` varchar(20) NOT NULL,
  `vip_ip` varchar(20) NOT NULL,
  `vip_use_name` varchar(50) DEFAULT NULL,
  `vip_net_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`vip_id`),
  UNIQUE KEY `vip_ip` (`vip_ip`),
  UNIQUE KEY `vip_mac` (`vip_mac`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4983 ;

-- --------------------------------------------------------

--
-- 資料表結構 `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT,
  `t_name` varchar(50) NOT NULL,
  `t_disk_size` int(10) NOT NULL,
  `t_os` varchar(20) NOT NULL,
  `t_date` varchar(50) NOT NULL,
  `t_describe` varchar(100) DEFAULT NULL,
  `t_path` varchar(50) NOT NULL,
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `t_name` (`t_name`),
  UNIQUE KEY `t_id` (`t_id`),
  UNIQUE KEY `t_describe` (`t_describe`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- 資料表結構 `vm`
--

CREATE TABLE IF NOT EXISTS `vm` (
  `uuid` varchar(50) NOT NULL,
  `vm_name` varchar(20) NOT NULL,
  `vm_mem` int(20) NOT NULL,
  `vm_cpu` int(2) NOT NULL,
  `vm_disk_size` int(10) NOT NULL,
  `vm_disk_file` varchar(50) NOT NULL,
  `vm_mac` varchar(20) NOT NULL,
  `vm_ip` varchar(20) NOT NULL,
  `vm_net` varchar(20) NOT NULL,
  `vm_vnc_port` int(10) DEFAULT NULL,
  `vm_os` varchar(50) NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `vm_name` (`vm_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
