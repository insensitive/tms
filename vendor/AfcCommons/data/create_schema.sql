SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `afc_group`
-- ----------------------------
DROP TABLE IF EXISTS `afc_group`;
CREATE TABLE `afc_group` (
  `afc_group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_group_id` int(11) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`afc_group_id`),
  KEY `afc_group_ibfk_1` (`parent_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of afc_group
-- ----------------------------
INSERT INTO `afc_group` VALUES ('1', '1', 'GUEST');

-- ----------------------------
-- Table structure for `afc_group_acl`
-- ----------------------------
DROP TABLE IF EXISTS `afc_group_acl`;
CREATE TABLE `afc_group_acl` (
  `afc_group_acl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `afc_group_id` int(11) unsigned NOT NULL,
  `afc_mcategory_id` int(11) unsigned NOT NULL,
  `access` enum('ALLOW','DENY','PARTIAL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`afc_group_acl_id`),
  KEY `afc_group_id` (`afc_group_id`),
  KEY `afc_mcategory_id` (`afc_mcategory_id`),
  CONSTRAINT `afc_group_acl_ibfk_1` FOREIGN KEY (`afc_group_id`) REFERENCES `afc_group` (`afc_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `afc_group_acl_ibfk_2` FOREIGN KEY (`afc_mcategory_id`) REFERENCES `afc_mcategory` (`afc_mcategory_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `afc_group_table_access`
-- ----------------------------
DROP TABLE IF EXISTS `afc_group_table_access`;
CREATE TABLE `afc_group_table_access` (
  `afc_group_table_access_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `afc_group_id` int(11) unsigned NOT NULL,
  `access_mode` enum('0','1','2','3','4','5','6','7') COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
  PRIMARY KEY (`afc_group_table_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `afc_mcategory`
-- ----------------------------
DROP TABLE IF EXISTS `afc_mcategory`;
CREATE TABLE `afc_mcategory` (
  `afc_mcategory_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `action_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`afc_mcategory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `afc_mdb`
-- ----------------------------
DROP TABLE IF EXISTS `afc_mdb`;
CREATE TABLE `afc_mdb` (
  `afc_mdb_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `afc_mcategory_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`afc_mdb_id`),
  KEY `afc_mcategory_id` (`afc_mcategory_id`),
  CONSTRAINT `afc_mdb_ibfk_1` FOREIGN KEY (`afc_mcategory_id`) REFERENCES `afc_mcategory` (`afc_mcategory_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `afc_record_access`
-- ----------------------------
DROP TABLE IF EXISTS `afc_record_access`;
CREATE TABLE `afc_record_access` (
  `afc_record_access_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `primary_key_column` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `primary_key` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `user_access_mode` enum('0','1','2','3','4','5','6','7') COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
  `group_access_mode` enum('0','1','2','3','4','5','6','7') COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
  `other_access_mode` enum('0','1','2','3','4','5','6','7') COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
  PRIMARY KEY (`afc_record_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of afc_record_access
-- ----------------------------

DROP TABLE IF EXISTS `afc_user_acl`;
CREATE TABLE `afc_user_acl` (
  `afc_user_acl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `afc_mcategory_id` int(11) unsigned NOT NULL,
  `access` enum('ALLOW','DENY','PARTIAL') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`afc_user_acl_id`),
  KEY `user_id` (`user_id`),
  KEY `afc_mcategory_id` (`afc_mcategory_id`),
  CONSTRAINT `afc_user_acl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `afc_user_acl_ibfk_2` FOREIGN KEY (`afc_mcategory_id`) REFERENCES `afc_mcategory` (`afc_mcategory_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `afc_user_table_access`
-- ----------------------------
DROP TABLE IF EXISTS `afc_user_table_access`;
CREATE TABLE `afc_user_table_access` (
  `afc_user_table_access_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `access_mode` enum('0','1','2','3','4','5','6','7') COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
  PRIMARY KEY (`afc_user_table_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `username` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_updated_by` int(11) unsigned DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user` (`user_id`, `group_id`, `username`, `created_by`, `created_at`, `last_updated_by`, `last_updated_at`) VALUES ('1', '1', 'guest', '1', '2013-03-21 11:17:48', '1', '2013-03-21 11:17:54');