/*
Navicat MySQL Data Transfer

Source Server         : me
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : quizdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-03-16 15:41:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_flow_photo
-- ----------------------------
DROP TABLE IF EXISTS `exa_flow_photo`;
CREATE TABLE `exa_flow_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_code` varchar(255) NOT NULL,
  `flow_name` text,
  `flow_photo` varchar(255) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='培訓流程圖片';

-- ----------------------------
-- Table structure for exa_flow_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_flow_title`;
CREATE TABLE `exa_flow_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_code` varchar(255) NOT NULL,
  `flow_title` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='培訓流程標題';
