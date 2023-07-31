-- ----------------------------
-- Table structure for exa_study
-- ----------------------------
ALTER TABLE exa_study ADD COLUMN study_body_min text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER study_body;

update exa_study set study_body_min = study_body where study_body_min is null

