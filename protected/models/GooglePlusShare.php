<?php

class GooglePlusShare extends Metric
{

    protected static $_type = 6;

    public function fetchValue()
    {
        $response = Curl::postJson('https://clients6.google.com/rpc?key=' . Yii::app()->params['googlePlusKey'], array(
            'method' => 'pos.plusones.get',
            'id' => 'p',
            'params' => array(
                'nolog' => true,
                'id' => $this->page->url,
                'source' => 'widget',
                'userId' => '@viewer',
                'groupId' => '@self',
            ),
            'jsonrpc' => '2.0',
            'key' => 'p',
            'apiVersion' => 'v1',
        ));
        return $response['count'];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


}