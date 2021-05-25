/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-05-25 14:12:23
*/
-- ----------------------------
-- Table structure for exa_title
-- ----------------------------
ALTER TABLE exa_title ADD COLUMN show_int int(1) NULL DEFAULT 1 COMMENT '1：顯示  0：不顯示' AFTER city;

