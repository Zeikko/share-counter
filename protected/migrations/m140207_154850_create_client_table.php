<?php

class m140207_154850_create_client_table extends CDbMigration
{

    public function up()
    {
        $this->createTable('client', array(
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(64) NOT NULL',
            'PRIMARY KEY (id)',
        ));
        $this->addForeignKey('client', 'page', 'client_id', 'client', 'id', 'restrict', 'restrict');
    }

    public function down()
    {
        $this->dropTable('client');
    }

}