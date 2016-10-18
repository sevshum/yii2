<?php

use yii\db\Schema;
use yii\db\Migration;

class m141201_121052_dynamic_params_table extends Migration
{
    public function up()
    {
        $sql = <<< EOD
DROP TABLE IF EXISTS `dynamic_params`;
CREATE TABLE `dynamic_params` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_idx` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOD;
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('dynamic_params');
    }
}
