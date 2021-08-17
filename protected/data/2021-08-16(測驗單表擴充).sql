
-- ----------------------------
-- Table structure for exa_quiz
-- ----------------------------
ALTER TABLE exa_quiz ADD COLUMN join_must int(2) NOT NULL DEFAULT 0 COMMENT '1:必須測驗' AFTER city;
