<?php

use yii\db\Migration;

class m140718_130739_user_profile extends Migration
{
    public function up()
    {    
        $sql = <<<EOD
DROP TABLE IF EXISTS `user_providers`;
CREATE TABLE `user_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(32) NOT NULL,
  `provider_id` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `data` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `provider_idx` (`provider`,`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `address` varchar(120) DEFAULT NULL,
  `description` text,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOD;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('user_providers');
        $this->dropTable('profiles');
        
    }
}