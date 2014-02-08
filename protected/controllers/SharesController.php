<?php

class SharesController extends Controller
{

    public function actionTotal($url, $id)
    {
        $page = Page::model()->findByAttributes(array('url' => $url));
        if (!$page) {
            $page = new Page();
            $page->url = $url;
            $page->created = time();
            $page->client_id = $clientId;
            $page->save();
        }
        if ($page->updated < time() - (60 * 10)) {
            $page->updateMetrics();
        }
        $this->outputJSON(array(
            'facebook' => $page->facebook_shares,
            'twitter' => $page->twitter_tweets,
            'linkedIn' => $page->linkedin_shares,
            'all' => $page->shares_total,
        ));
    }

    /**
     * Convert PHP Array to JSON and print it
     * @param type $array
     */
    protected function outputJSON($array)
    {
        $this->toInteger($array);
        $output = json_encode($array);
        if (isset($_GET['callback'])) {
            header('Content-Type: text/javascript');
            $output = $_GET['callback'] . '(' . $output . ');';
        }
        else {
            header('Content-Type: application/json');
        }
        echo $output;
    }

    /**
     * Converts numeric variables to integers.
     * @param type $array
     */
    protected function toInteger(&$array)
    {
        foreach ($array as &$value) {
            if (is_array($value))
                $this->toInteger($value);
            if (is_numeric($value)) {
                $value = (double) $value;
            }
        }
    }

}