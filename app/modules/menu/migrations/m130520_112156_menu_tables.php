<?php

class m130520_112156_menu_tables extends yii\db\Migration
{

    public function safeUp()
    {
        $sql = <<< EOD
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code_idx` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,  
  `url` varchar(255) DEFAULT NULL,
  `active_condition` varchar(255) DEFAULT NULL,
  `tree` int(11) DEFAULT NULL,
  `lft`  int(11) DEFAULT NULL,
  `rgt`  int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id_idx` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_item_i18ns`;
CREATE TABLE IF NOT EXISTS `menu_item_i18ns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `lang_id` varchar(2) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id_idx` (`parent_id`),
  UNIQUE KEY `lang_id_parent_id_unique_idx` (`lang_id`,`parent_id`)      
) ENGINE=InnoDB DEFAULT CHARSET=utf8;    
            
EOD;
        $this->execute($sql);
    }

    public function down()
    {
        
    }
}