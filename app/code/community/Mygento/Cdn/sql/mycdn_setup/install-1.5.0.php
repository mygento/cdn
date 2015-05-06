<?php
$this->startSetup();


$this->run("
DROP TABLE IF EXISTS {$this->getTable('mycdn/job')};
CREATE TABLE {$this->getTable('mycdn/job')} (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `uploadname` varchar(255) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `delete` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_Uploadname` (`uploadname`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->endSetup();
