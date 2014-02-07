<?php

class LinkedInShare extends Metric
{

    protected static $_type = 5;

    public function fetchValue()
    {
        $response = Curl::getJson('http://www.linkedin.com/countserv/count/share?url=' . $this->page->url . '&format=json');
        if (isset($response['count']))
            return $response['count'];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}