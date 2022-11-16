/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : quizdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-11-15 18:04:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for exa_chapter_class
-- ----------------------------
DROP TABLE IF EXISTS `exa_chapter_class`;
CREATE TABLE `exa_chapter_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL,
  `chapter_name` varchar(200) NOT NULL COMMENT '菜單的名字',
  `item_sum` int(11) DEFAULT '0' COMMENT '試題總數',
  `random_num` int(11) DEFAULT '0' COMMENT '試題隨機數量',
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '0:不顯示  1：顯示',
  `z_index` int(11) DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='章节的分类';

-- ----------------------------
-- Table structure for exa_chapter_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_chapter_title`;
CREATE TABLE `exa_chapter_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '菜单id',
  `chapter_id` int(11) NOT NULL DEFAULT '0' COMMENT '章节id',
  `title_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:单选题 1:多选题 2:判断题',
  `title_code` varchar(255) DEFAULT NULL COMMENT '題目編號',
  `name` text NOT NULL COMMENT '試題內容',
  `remark` text COMMENT '講解',
  `city` varchar(255) DEFAULT NULL,
  `display` int(1) DEFAULT '1' COMMENT '1：顯示  0：不顯示',
  `show_num` int(11) NOT NULL DEFAULT '0' COMMENT '出現次數',
  `success_num` int(11) NOT NULL DEFAULT '0' COMMENT '正確次數',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='試題庫';

-- ----------------------------
-- Table structure for exa_chapter_title_choose
-- ----------------------------
DROP TABLE IF EXISTS `exa_chapter_title_choose`;
CREATE TABLE `exa_chapter_title_choose` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL COMMENT '章节id',
  `title_id` int(11) NOT NULL COMMENT '試題id',
  `choose_name` text COMMENT '選項內容',
  `judge` int(11) NOT NULL DEFAULT '0' COMMENT '0:錯誤  1：正確',
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '1:参与选项 0:不参与',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='題庫選項表';

-- ----------------------------
-- Table structure for exa_markedly
-- ----------------------------
DROP TABLE IF EXISTS `exa_markedly`;
CREATE TABLE `exa_markedly` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '測驗單名字',
  `dis_name` text COMMENT '描述',
  `start_time` date DEFAULT NULL COMMENT '開始時間',
  `end_time` date DEFAULT NULL COMMENT '結束時間',
  `exa_num` int(10) unsigned NOT NULL DEFAULT '20' COMMENT '試題數量',
  `city` varchar(255) DEFAULT NULL COMMENT '適用城市  空：所有城市',
  `join_must` int(2) NOT NULL DEFAULT '0' COMMENT '1:必須測驗',
  `bumen` text COMMENT '章節。多個章節用,分割',
  `bumen_ex` text COMMENT '章節',
  `display` int(1) NOT NULL DEFAULT '1' COMMENT '1:顯示 0：隱藏',
  `take_sum` int(11) NOT NULL DEFAULT '0' COMMENT '已參加測驗的人數',
  `success_ratio` float(5,2) NOT NULL DEFAULT '0.00' COMMENT '總正確率',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='測驗單(新版)';

-- ----------------------------
-- Table structure for exa_study
-- ----------------------------
DROP TABLE IF EXISTS `exa_study`;
CREATE TABLE `exa_study` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL,
  `study_title` varchar(200) NOT NULL COMMENT '标题',
  `study_img` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `study_subtitle` varchar(255) DEFAULT NULL COMMENT '副标题',
  `study_body` text,
  `study_date` date DEFAULT NULL,
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '0:不顯示  1：顯示',
  `z_index` int(11) DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='学习指南(文章)';

-- ----------------------------
-- Table structure for exa_study_class
-- ----------------------------
DROP TABLE IF EXISTS `exa_study_class`;
CREATE TABLE `exa_study_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL,
  `class_name` varchar(200) NOT NULL COMMENT '菜單的名字',
  `default_img` varchar(255) DEFAULT NULL COMMENT '默认图片（暂时不使用）',
  `item_num` int(11) DEFAULT '0' COMMENT '文章数量',
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '0:不顯示  1：顯示',
  `z_index` int(11) DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='学习指南(分类)';

-- ----------------------------
-- Table structure for exa_take
-- ----------------------------
DROP TABLE IF EXISTS `exa_take`;
CREATE TABLE `exa_take` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `markedly_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `title_num` int(11) DEFAULT '1' COMMENT '正確的題數',
  `title_sum` int(11) DEFAULT '1' COMMENT '總題數',
  `success_ratio` float(5,2) NOT NULL DEFAULT '0.00' COMMENT '正確率',
  `title_id_list` text COMMENT '試題id逗號分割（主要保存試題順序）',
  `just_bool` int(11) NOT NULL DEFAULT '0' COMMENT '1:參加驗證 0:不參加',
  `just_remark` text COMMENT '不參加驗證的原因（測驗成績被管理員強制失效）',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='員工參與測試表';

-- ----------------------------
-- Table structure for exa_take_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_take_title`;
CREATE TABLE `exa_take_title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `take_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `choose_id` text NOT NULL COMMENT '用戶選擇的選項(多選用逗號分割)',
  `list_choose` text COMMENT '選項順序',
  `is_correct` int(1) NOT NULL DEFAULT '1' COMMENT '1：回答正確 0：回答錯誤',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='員工答題表';

-- ----------------------------
-- Table structure for exa_wrong_title
-- ----------------------------
DROP TABLE IF EXISTS `exa_wrong_title`;
CREATE TABLE `exa_wrong_title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `title_id` int(11) NOT NULL,
  `choose_id` text NOT NULL COMMENT '用戶選擇的選項(多選用逗號分割)',
  `list_choose` text COMMENT '選項順序',
  `wrong_date` datetime NOT NULL COMMENT '錯誤時間',
  `wrong_type` int(1) NOT NULL DEFAULT '0' COMMENT '0:模擬練習 1:綜合測驗 2：錯題糾正',
  `take_id` int(11) DEFAULT '0',
  `wrong_num` int(11) NOT NULL DEFAULT '1',
  `display` int(1) NOT NULL DEFAULT '1',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='我的錯誤題';

-- ----------------------------
-- Table structure for exa_setting
-- ----------------------------
DROP TABLE IF EXISTS `exa_setting`;
CREATE TABLE `exa_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(255) DEFAULT NULL,
  `menu_name` varchar(200) NOT NULL COMMENT '菜單的名字',
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '0:不顯示  1：顯示',
  `z_index` int(11) DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='菜單配置表';

-- ----------------------------
-- Records of exa_setting
-- ----------------------------
INSERT INTO `exa_setting` VALUES ('1', 'TE', '技术部', '1', '0', null, 'shenchao', '2022-11-07 10:58:00', '2022-11-15 17:23:33');
INSERT INTO `exa_setting` VALUES ('2', 'YI', '营运部', '1', '0', null, null, '2022-11-08 18:14:30', '2022-11-08 18:14:30');
INSERT INTO `exa_setting` VALUES ('4', 'SB', '管理层', '1', '2', 'shenchao', 'shenchao', '2022-11-15 17:24:37', '2022-11-15 17:41:05');
