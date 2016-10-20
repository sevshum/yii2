<?php

class m140801_124357_settings_tables extends yii\db\Migration
{

    public function safeUp()
    {
        $sql = <<< EOD
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_key_unique` (`group`,`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOD;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('settings');
    }
}