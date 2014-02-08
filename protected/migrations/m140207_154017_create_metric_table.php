<?php

class m140207_154017_create_metric_table extends CDbMigration
{

    public function up()
    {
        $this->createTable('metric', array(
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'page_id' => 'int(11) NOT NULL',
            'timestamp' => 'int(11) NOT NULL',
            'type' => 'int(2) NOT NULL',
            'value' => 'int(11) NOT NULL',
            'PRIMARY KEY (id)',
            'KEY `timestamp` (`timestamp`, `type`)',
            'KEY `page_id` (`page_id`)',
        ));
        $this->addForeignKey('page', 'metric', 'page_id', 'page', 'id', 'restrict', 'restrict');
    }

    public function down()
    {
        $this->dropTable('metric');
    }

}