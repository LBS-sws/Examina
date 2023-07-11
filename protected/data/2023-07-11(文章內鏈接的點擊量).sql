/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : quizdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-07-11 11:06:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_link_hits
-- ----------------------------
DROP TABLE IF EXISTS `exa_link_hits`;
CREATE TABLE `exa_link_hits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL,
  `study_id` int(10) NOT NULL COMMENT '文章id',
  `employee_id` int(11) DEFAULT NULL,
  `hit_date` datetime DEFAULT NULL COMMENT '點擊的時間',
  `link_url` varchar(255) DEFAULT NULL,
  `hit_type` int(2) NOT NULL DEFAULT '1' COMMENT '1:有效數據 0：無效數據',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='文章內鏈接的點擊量';
