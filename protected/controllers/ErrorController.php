<?php

class ErrorController extends Controller
{

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            echo $error['message'];
        }
    }

}