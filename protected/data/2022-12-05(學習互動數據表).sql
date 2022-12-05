/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : quizdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-12-05 11:32:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_mutual
-- ----------------------------
DROP TABLE IF EXISTS `exa_mutual`;
CREATE TABLE `exa_mutual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `mutual_date` date DEFAULT NULL,
  `mutual_body` text NOT NULL COMMENT '互動內容',
  `mutual_state` int(11) NOT NULL DEFAULT '0' COMMENT '0：草稿 1：帶審核 2：審核通過 3：拒絕',
  `end_body` text NOT NULL COMMENT '最終顯示的互動文本',
  `z_index` int(11) NOT NULL DEFAULT '0',
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '0：隱藏 1：顯示',
  `reject_remark` text COMMENT '拒絕原因',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='學習互動表';
