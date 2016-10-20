<?php

class m130523_124357_languages_tables extends yii\db\Migration
{

    public function safeUp()
    {
        $sql = <<< EOD
DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` varchar(2) NOT NULL,
  `name` varchar(32) NOT NULL,
  `locale` varchar(32) DEFAULT NULL,
  `order` INT(11) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_default_idx` (`is_default`),
  KEY `is_active_idx` (`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
            
INSERT INTO `languages` VALUES ('en', 'English', NULL, 1, 1, 1, NOW(), NOW());
EOD;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('languages');
    }
}