<?php

class Tweet extends Metric
{

    protected static $_type = 4;

    public function fetchValue()
    {
        $response = Curl::getJson('http://cdn.api.twitter.com/1/urls/count.json?url=' . $this->page->url);
        if (isset($response['count']))
            return $response['count'];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}