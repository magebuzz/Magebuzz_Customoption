<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('default_option_value')};
CREATE TABLE {$this->getTable('default_option_value')} (
  `option_id` INT(11) UNSIGNED NOT NULL,
  `option_type_id` INT(11) UNSIGNED ,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

    ");

$installer->endSetup(); 