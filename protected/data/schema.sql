-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 25, 2013 at 10:28 PM
-- Server version: 5.5.32
-- PHP Version: 5.3.10-1ubuntu3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `share-api`
--

-- --------------------------------------------------------

--
-- Table structure for table `apilog`
--

CREATE TABLE IF NOT EXISTS `apilog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` int(2) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `response` text NOT NULL,
  `response_code` int(3) DEFAULT NULL,
  `total_time` float(5,2) NOT NULL,
  `request` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service` (`service`,`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=962743 ;

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE IF NOT EXISTS `license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expires` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `metric`
--

CREATE TABLE IF NOT EXISTS `metric` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `type` int(2) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`,`type`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(512) NOT NULL,
  `updated` int(11) DEFAULT NULL,
  `facebook_shares` int(6) NOT NULL DEFAULT '0',
  `facebook_likes` int(6) NOT NULL DEFAULT '0',
  `facebook_comments` int(6) NOT NULL DEFAULT '0',
  `twitter_tweets` int(6) NOT NULL DEFAULT '0',
  `linkedin_shares` int(6) NOT NULL DEFAULT '0',
  `not_changed` int(3) NOT NULL DEFAULT '0',
  `title` varchar(256) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `shares_per_hour` int(11) NOT NULL DEFAULT '0',
  `shares_total` int(11) NOT NULL DEFAULT '0',
  `license_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`(255),`updated`),
  KEY `shares_per_hour` (`shares_per_hour`),
  KEY `license_id` (`license_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14795 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `metric`
--
ALTER TABLE `metric`
  ADD CONSTRAINT `metric_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`);

--
-- Constraints for table `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `page_ibfk_1` FOREIGN KEY (`license_id`) REFERENCES `license` (`id`);
