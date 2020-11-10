-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- 主機: localhost
-- 產生時間： 2020 年 11 月 11 日 03:48
-- 伺服器版本: 10.3.8-MariaDB-1:10.3.8+maria~xenial-log
-- PHP 版本： 7.2.13-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `dev_user_list`
--

-- --------------------------------------------------------

--
-- 資料表結構 `account_info`
--

CREATE TABLE `account_info` (
  `user_id` int(11) NOT NULL COMMENT 'user流水號',
  `account` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '帳號',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '帳號',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `sex` tinyint(1) NOT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '備註'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `account_info`
--
ALTER TABLE `account_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `account` (`account`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `account_info`
--
ALTER TABLE `account_info`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'user流水號';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
