/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : examinadev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-07-06 16:10:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_examina
-- ----------------------------
DROP TABLE IF EXISTS `exa_examina`;
CREATE TABLE `exa_examina` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `choose_id` int(11) NOT NULL,
  `list_choose` text COMMENT '選項順序',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='員工答題表';

-- ----------------------------
-- Table structure for exa_quiz
-- ----------------------------
DROP TABLE IF EXISTS `exa_quiz`;
CREATE TABLE `exa_quiz` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '測驗單名字',
  `dis_name` text COMMENT '描述',
  `start_time` date DEFAULT NULL COMMENT '開始時間',
  `end_time` date DEFAULT NULL COMMENT '結束時間',
  `exa_num` int(10) unsigned NOT NULL DEFAULT '20' COMMENT '試題數量',
  `staff_all` int(11) NOT NULL DEFAULT '1' COMMENT '1:所有員工   0：自定義員工',
  `city` varchar(255) DEFAULT NULL COMMENT '適用城市  空：所有城市',
  `bumen` text COMMENT '部門。多個部門用,分割',
  `bumen_ex` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='測驗單';

-- ----------------------------
-- Table structure for exa_quiz_staff
-- ----------------------------
DROP TABLE IF EXISTS `exa_quiz_staff`;
CREATE TABLE `exa_quiz_staff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='試驗單自定義的員工';

-- ----------------------------
-- Table structure for exa_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_title`;
CREATE TABLE `exa_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` varchar(11) DEFAULT '1' COMMENT '測驗單id',
  `title_code` varchar(255) DEFAULT NULL COMMENT '題目編號',
  `name` text NOT NULL COMMENT '試題內容',
  `remark` text COMMENT '講解',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8 COMMENT='試題庫';

-- ----------------------------
-- Table structure for exa_title_choose
-- ----------------------------
DROP TABLE IF EXISTS `exa_title_choose`;
CREATE TABLE `exa_title_choose` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(11) NOT NULL COMMENT '試題id',
  `choose_name` text NOT NULL COMMENT '選項內容',
  `judge` int(11) NOT NULL DEFAULT '0' COMMENT '0:錯誤  1：正確',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=521 DEFAULT CHARSET=utf8 COMMENT='題庫選項表';
