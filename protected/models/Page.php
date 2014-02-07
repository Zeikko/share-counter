<?php

/**
 * This is the model class for table "page".
 *
 * The followings are the available columns in table 'page':
 * @property integer $id
 * @property string $url
 * @property integer $updated
 *
 * The followings are the available model relations:
 * @property Metric[] $metrics
 */
class Page extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'page';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('url', 'required'),
            array('updated', 'numerical', 'integerOnly' => true),
            array('url', 'length', 'max' => 512),
            array('facebook_shares, facebook_likes, facebook_comments, twitter_tweets, linkedin_shares, not_changed, title, added, shares_per_hour, shares_total', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, url, updated', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'metrics' => array(self::HAS_MANY, 'Metric', 'page_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Page the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function updateMetrics()
    {
        $facebookData = FacebookUpdater::updateFacebook($this->id, $this->url);
        $tweet = new Tweet();
        $tweet->page_id = $this->id;
        $twitterTweets = $tweet->updateMetric();
        $linkedInShare = new LinkedInShare();
        $linkedInShare->page_id = $this->id;
        $linkedInShares = $linkedInShare->updateMetric();
//        $googlePlusShare = new GooglePlusShare();
//        $googlePlusShare->page_id = $this->id;
//        $googlePlusShares = $googlePlusShare->updateMetric();
        //Check if number of social interactions changed
        $changed = false;
        if ($this->facebook_shares != $facebookData['share_count'])
            $changed = true;
        if ($this->facebook_likes != $facebookData['like_count'])
            $changed = true;
        if ($this->facebook_comments != $facebookData['comment_count'])
            $changed = true;
        if ($this->twitter_tweets != $twitterTweets)
            $changed = true;
        if ($this->linkedin_shares != $linkedInShares)
            $changed = true;
        if ($changed)
            $this->not_changed = 0;
        else
            $this->not_changed++;

        $newShares = $facebookData['share_count'] + $twitterTweets + $linkedInShares;
        $this->updateSharesPerHour($newShares);

        //Save number of social interactions
        $this->facebook_shares = $facebookData['share_count'];
        $this->facebook_likes = $facebookData['like_count'];
        $this->facebook_comments = $facebookData['comment_count'];
        $this->twitter_tweets = $twitterTweets;
        $this->linkedin_shares = $linkedInShares;
        $this->shares_total = $facebookData['share_count'] + $twitterTweets + $linkedInShares;

        $this->updated = time();
        $this->save();
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('url', $this->url, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('updated', $this->updated);
        $criteria->compare('facebook_shares', $this->facebook_shares);
        $criteria->compare('facebook_comments', $this->facebook_comments);
        $criteria->compare('facebook_likes', $this->facebook_likes);
        $criteria->compare('linkedin_shares', $this->linkedin_shares);
        $criteria->compare('twitter_tweets', $this->twitter_tweets);
        $criteria->compare('shares_per_hour', $this->shares_per_hour);

        $sort = new CSort();
        $sort->defaultOrder = array(
            'shares_per_hour' => CSort::SORT_DESC,
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
        ));
    }

    public function updateSharesPerHour($newShares)
    {
        if ($this->updated) {
            $oldShares = $this->twitter_tweets + $this->linkedin_shares + $this->facebook_shares;
//        var_dump($oldShares);
//        var_dump($newShares);
//        var_dump(time());
//        var_dump($this->updated);
//        var_dump(((time() - $this->updated) / 60 / 60));
            $this->shares_per_hour = round(($newShares - $oldShares) / ((time() - $this->updated) / 60 / 60));
//        var_dump(($newShares - $oldShares) / ((time() - $this->updated) / 60 / 60));
//        var_dump($this->shares_per_hour);
//        var_dump('-');
            if ($this->shares_per_hour < 0)
                $this->shares_per_hour = 0;

            $sharesPerHour = new SharesPerHour();
            $sharesPerHour->page_id = $this->id;
            $sharesPerHour->value = $this->shares_per_hour;
            $sharesPerHour->type = SharesPerHour::$_type;
            $sharesPerHour->timestamp = time();
            $sharesPerHour->save();
        }
        else {
            return 0;
        }
    }

    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        } else {
            return $this->url;
        }
    }

    /**
     * Remove trailing slash
     * Remove url parameters
     * Remove protocol
     */
    public static function trimUrl($url)
    {
        $parsedUrl = parse_url($url);
        $trimmedUrl = '';
        if (isset($parsedUrl['scheme']))
            $trimmedUrl = $parsedUrl['scheme'] . '://';
        if (isset($parsedUrl['host']))
            $trimmedUrl .= $parsedUrl['host'];
        if (isset($parsedUrl['path']))
            $trimmedUrl .= $parsedUrl['path'];
//        //Remove url parameters
//        if (mb_strpos($url, '?') !== false) {
//            $url = mb_substr($url, 0, mb_strpos($url, '?'));
//        }
        //Remove trailing slash
        $trimmedUrl = rtrim($trimmedUrl, '/');
//        //Remove protocol
//        if (mb_strpos($url, 'http://') !== false) {
//            $url = mb_substr($url, mb_strpos($url, 'http://'));
//        }
//        if (mb_strpos($url, 'https://') !== false) {
//            $url = mb_substr($url, mb_strpos($url, 'https://'));
//        }
        $trimmedUrl = strtolower($trimmedUrl);
        return $trimmedUrl;
    }

    public function beforeSave()
    {
        return parent::beforeSave();
    }

    public function getShares()
    {
        return $this->facebook_shares + $this->twitter_tweets + $this->linkedin_shares;
    }

    public function getFacebookTotal()
    {
        return $this->facebook_shares + $this->facebook_likes + $this->facebook_comments;
    }

    public function isAttributeRequired($attribute)
    {
        return false;
    }

    public function getUrl()
    {
        if (strpos($this->url, 'http://') === false && strpos($this->url, 'https://') === false) {
            return $this->url = 'http://' . $this->url;
        } else {
            return $this->url;
        }
    }

    public static function getCanonical($url)
    {
        if (isset(Yii::app()->params['skipCanonical'])) {
            foreach (Yii::app()->params['skipCanonical'] as $skipCanonical) {
                if (strpos($url, $skipCanonical) !== false) {
                    var_dump('skip');
                    return $url;
                }
            }

            $parsedUrl = parse_url($url);
            $html = Curl::getHtml($url, false);
            $canonical = null;
            if (strpos($html, '<link rel="canonical" href="') !== 'false') {
                $canonical = strstr($html, '<link rel="canonical" href="');
                $canonical = substr($canonical, 28);
                $canonical = strstr($canonical, '"', true);
                $parsedCanonical = parse_url($canonical);
            }
            //Reconstruct canonical URL because it might be relative
            if ($canonical) {
                if (isset($parsedCanonical['scheme']))
                    $scheme = $parsedCanonical['scheme'];
                else
                    $scheme = $parsedUrl['scheme'];
                if (isset($parsedCanonical['host']))
                    $host = $parsedCanonical['host'];
                else
                    $host = $parsedUrl['host'];
                if (isset($parsedCanonical['path']))
                    $path = $parsedCanonical['path'];
                else
                    $path = $parsedUrl['path'];

                return $scheme . '://' . $host . $path;
            }
        }
        return $url;
    }

}
