<?php

/**
 * This command updates metrics from social media websites. Should be run every 5 minutes by crontab.
 */
class UpdateCommand extends CConsoleCommand
{

    public function run($args)
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'not_changed ASC, updated ASC';
        $criteria->limit = 100;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            foreach (Page::model()->findAll($criteria) as $page) {
                $updatedSince = (time() - $page->updated);
                //Update less frequently if there has been no new social media shares in the past updates
                if (!$page->updated || $updatedSince > ((pow((($page->not_changed + 1) * ($page->not_changed + 1)), 2) * 15) * 60)) {
                    $page->updateMetrics();
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

}