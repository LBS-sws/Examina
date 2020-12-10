/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-12-10 16:12:23
*/
-- ----------------------------
-- Table structure for hr_boss_audit
-- ----------------------------
ALTER TABLE exa_join ADD COLUMN title_sum int(11) NULL DEFAULT 1 AFTER employee_id;
ALTER TABLE exa_join ADD COLUMN title_num int(11) NULL DEFAULT 1 AFTER employee_id;
