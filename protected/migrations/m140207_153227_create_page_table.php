<?php

class m140207_153227_create_page_table extends CDbMigration
{

    public function up()
    {
        $this->createTable('page', array(
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'client_id' => 'int(11) NOT NULL',
            'url' => 'varchar(512) NOT NULL',
            'updated' => 'int(11) DEFAULT NULL',
            'facebook_shares' => 'int(6) NOT NULL DEFAULT "0"',
            'facebook_likes' => 'int(6) NOT NULL DEFAULT "0"',
            'facebook_comments' => 'int(6) NOT NULL DEFAULT "0"',
            'twitter_tweets' => 'int(6) NOT NULL DEFAULT "0"',
            'linkedin_shares' => 'int(6) NOT NULL DEFAULT "0"',
            'not_changed' => 'int(3) NOT NULL DEFAULT "0"',
            'title' => 'varchar(256) DEFAULT NULL',
            'created' => 'int(11) DEFAULT NULL',
            'shares_per_hour' => 'int(11) NOT NULL DEFAULT "0"',
            'shares_total' => 'int(11) NOT NULL DEFAULT "0"',
            'PRIMARY KEY (id)',
            'KEY `client_id` (`client_id`)',
            'KEY `url` (`url`(255),`updated`)',
            'KEY `shares_per_hour` (`shares_per_hour`)',
        ));
    }

    public function down()
    {
        $this->dropTable('page');
    }

}