/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : examinadev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-07-26 17:44:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_examina
-- ----------------------------
DROP TABLE IF EXISTS `exa_examina`;
CREATE TABLE `exa_examina` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `join_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `choose_id` int(11) NOT NULL,
  `list_choose` text COMMENT '選項順序',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COMMENT='員工答題表';

-- ----------------------------
-- Table structure for exa_join
-- ----------------------------
DROP TABLE IF EXISTS `exa_join`;
CREATE TABLE `exa_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='員工參與測試表';
