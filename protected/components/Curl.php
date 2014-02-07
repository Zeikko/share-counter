<?php

/**
 * Wrapper for PHP Curl
 * 
 */
class Curl
{

    /**
     * Get request which expects json as response.
     * @param type $url
     * @param type $data
     * @return type
     */
    public static function getJson($url, $data = null)
    {
        $curl = self::init();
        $request = self::constructGetQuery($url, $data);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,
        ));

        $response = self::request($curl);
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * Post request which sends json encoded data and expects json as response.
     * @param type $url
     * @param type $data
     * @return type
     */
    public static function postJson($url, $data)
    {
        $data = json_encode($data);

        $curl = self::init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );
        $response = self::request($curl);
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * Get request which expects xml as response.
     * @param type $url
     * @param type $data
     * @return type
     */
    public static function getXml($url, $data = null)
    {
        $curl = self::init();
        $request = self::constructGetQuery($url, $data);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,
        ));
        $response = self::request($curl);
        libxml_use_internal_errors(true);
        $response = simplexml_load_string($response);
        return $response;
    }

    /**
     * Get request which does not do any post processing to the result.
     * @param type $url
     * @param type $data
     * @return type
     */
    public static function getHtml($url, $log = true)
    {
        $curl = self::init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
        ));

        $response = self::request($curl, $log);
        return $response;
    }

    /**
     * Initializes Curl
     * @return type
     */
    protected static function init()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => 'Share API',
            CURLINFO_HEADER_OUT => true,
        ));
        if (Yii::app()->params['httpProxy'])
            curl_setopt($curl, CURLOPT_PROXY, Yii::app()->params['httpProxy']);

        return $curl;
    }

    /**
     * Performs the curl request and logs it.
     * @param type $curl
     * @return type
     */
    protected static function request($curl, $log = true)
    {
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        if ($log) {
            Apilog::log($info['request_header'], $response, $info['http_code'], $info['total_time']);
        }
        curl_close($curl);
        return $response;
    }

    /**
     * Constructs a query string with url and parameters
     * @param type $url
     * @param type $parameters
     * @return string
     */
    protected static function constructGetQuery($url, $parameters)
    {
        if ($parameters) {
            $queryString = array();
            foreach ($parameters as $key => $value) {
                $queryString[] = $key . '=' . $value;
            }
            $queryString = implode('&', $queryString);
            if (strpos($url, '?') === true)
                $url .= '&' . $queryString;
            else
                $url .= '?' . $queryString;
        }
        return $url;
    }

}