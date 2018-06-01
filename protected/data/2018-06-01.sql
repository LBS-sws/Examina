/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : examinadev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-06-01 11:10:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_quiz
-- ----------------------------
DROP TABLE IF EXISTS `exa_quiz`;
CREATE TABLE `exa_quiz` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` varchar(11) DEFAULT '1' COMMENT '測驗類別',
  `name` varchar(255) NOT NULL COMMENT '測驗單名字',
  `dis_name` text COMMENT '描述',
  `start_time` date DEFAULT NULL COMMENT '開始時間',
  `end_time` date DEFAULT NULL COMMENT '結束時間',
  `exa_num` int(10) unsigned NOT NULL DEFAULT '20' COMMENT '試題數量',
  `staff_all` int(11) NOT NULL DEFAULT '1' COMMENT '1:所有員工   0：自定義員工',
  `city` varchar(255) DEFAULT NULL COMMENT '適用城市  空：所有城市',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='測驗單';

-- ----------------------------
-- Table structure for exa_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_title`;
CREATE TABLE `exa_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` varchar(11) DEFAULT '1' COMMENT '測驗類別',
  `title_code` varchar(255) DEFAULT NULL COMMENT '題目編號',
  `name` text NOT NULL COMMENT '試題內容',
  `remark` text COMMENT '講解',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COMMENT='試題庫';

-- ----------------------------
-- Table structure for exa_type
-- ----------------------------
DROP TABLE IF EXISTS `exa_type`;
CREATE TABLE `exa_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `bumen` text COMMENT '部門。多個部門用,分割',
  `bumen_ex` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='測驗類別';
