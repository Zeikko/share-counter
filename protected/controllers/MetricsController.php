<?php

class MetricsController extends Controller
{

    public function actionTotal($from = null, $to = null)
    {
        $license = $this->getLicense();

        $comments = FacebookComment::findAllByLicense($license->id, $from, $to);
        $likes = FacebookLike::findAllByLicense($license->id, $from, $to);
        $facebookShares = FacebookShare::findAllByLicense($license->id, $from, $to);
        $tweets = Tweet::findAllByLicense($license->id, $from, $to);
        $linkedInShares = LinkedInShare::findAllByLicense($license->id, $from, $to);
        $sharesPerHours = SharesPerHour::findAllByLicense($license->id, $from, $to);
        
        
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

        $this->output($json);
    }

}