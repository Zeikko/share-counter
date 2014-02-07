<?php

class PagesController extends Controller
{

    public function actionCreate($url)
    {
        $license = $this->getLicense();

        //TODO Check license domain

        $url = Page::trimUrl(Yii::app()->request->getParam('url'));
        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }
        $url = Page::getCanonical($url);
        $url = Page::trimUrl(Yii::app()->request->getParam('url'));
        $page = Page::model()->findByAttributes(array('url' => $url));
        if (!$page) {
            $page = new Page();
            $page->url = $url;
            $page->created = time();
            $page->license_id = $license->id;
            $page->save();
        }
    }

    /**
     * Returns social data from the database.
     * Includes total sums and time series.
     * @param type $url
     * @param type $from
     * @param type $to
     */
    public function actionIndex($url, $from = null, $to = null)
    {
        $license = $this->getLicense();

        $url = Page::trimUrl($url);
        $json = Yii::app()->cache->get($url);

        if (!$json) {
            $page = Page::model()->findByAttributes(array('url' => $url));
            if (!$page) {
                throw new CHttpException(404, 'Page not found. You need to create it first.');
            }
            $comments = FacebookComment::findAllByPageId($page->id, $from, $to);
            $likes = FacebookLike::findAllByPageId($page->id, $from, $to);
            $facebookShares = FacebookShare::findAllByPageId($page->id, $from, $to);
            $tweets = Tweet::findAllByPageId($page->id, $from, $to);
            $linkedInShares = LinkedInShare::findAllByPageId($page->id, $from, $to);
            $sharesPerHours = SharesPerHour::findAllByPageId($page->id, $from, $to);

            $facebookTotal = array();
            $sharesTotal = array();

            $i = 0;
            foreach ($comments as $comment) {
                $facebookTotal[$i] = array('timestamp' => date('c', $comment['timestamp']), 'value' => $comment['value']);
                $i++;
            }

            $i = 0;
            foreach ($likes as $like) {
                $facebookTotal[$i]['value'] += $like['value'];
                $i++;
            }

            $i = 0;
            foreach ($facebookShares as $share) {
                $facebookTotal[$i]['value'] += $share['value'];
                $sharesTotal[$i] = array('timestamp' => date('c', $share['timestamp']), 'value' => $share['value']);
                $i++;
            }

            $i = 0;
            foreach ($tweets as $tweet) {
                if (isset($sharesTotal[$i]))
                    $sharesTotal[$i]['value'] += $tweet['value'];
                $i++;
            }

            $i = 0;
            foreach ($linkedInShares as $linkedInShare) {
                if (isset($sharesTotal[$i]))
                    $sharesTotal[$i]['value'] += $linkedInShare['value'];
                $i++;
            }

            $i = 0;
            foreach ($sharesPerHours as $sharesPerHour) {
                $i++;
            }

            $output = array(
                'facebook' => array(
                    'comments' => $comments,
                    'likes' => $likes,
                    'shares' => $facebookShares,
                    'total' => $facebookTotal,
                ),
                'twitter' => array(
                    'tweets' => $tweets,
                ),
                'linkedin' => array(
                    'shares' => $linkedInShares,
                ),
                'shares_per_hour' => $sharesPerHours,
                'shares_total' => $sharesTotal,
            );
//                        var_dump($output);
            $json = $this->encode($output);
            Yii::app()->cache->set($url, $json);
        }
        $this->output($json);
    }

}