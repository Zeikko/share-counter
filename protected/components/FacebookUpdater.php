<?php

class FacebookUpdater
{

    /**
     * Updates all Facebook metrics with one request.
     * @param type $pageId
     * @param type $url
     * @return type
     */
    public static function updateFacebook($pageId, $url)
    {
        $facebookData = Curl::getJson('https://api.facebook.com/method/fql.query?format=json&query=SELECT+share_count,+like_count,+comment_count,+total_count+FROM+link_stat+WHERE+url=%22' . $url . '%22');
        if (isset($facebookData[0])) {
            $facebookData = $facebookData[0];
            $facebookShare = new FacebookShare();
            $facebookShare->page_id = $pageId;
            $facebookShare->updateMetric($facebookData['share_count']);
            $facebookLike = new FacebookLike();
            $facebookLike->page_id = $pageId;
            $facebookLike->updateMetric($facebookData['like_count']);
            $facebookComment = new FacebookComment();
            $facebookComment->page_id = $pageId;
            $facebookComment->updateMetric($facebookData['comment_count']);
            return $facebookData;
        } else {
            Yii::log(print_r($facebookData, true), 'info');
        }
    }

}
