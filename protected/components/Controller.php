<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        if (Yii::app()->params['authentication']['required']) {
            return array(
                array('deny',
                    'users' => array('?'),
                ),
            );
        } else {
            return array();
        }
    }

    public $breadcrumbs = array();

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $feedbackForm;

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    protected function encode($array)
    {
        $this->toInteger($array);
        return CJSON::encode($array);
    }

    /**
     * Converts numeric variables to integers, encodes and outputs them as json.
     * @param type $output
     */
    public function output($output)
    {
        header('Content-Type: application/json');
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
                $value = (int) $value;
            }
        }
    }

    protected function getLicense()
    {
        if (!Yii::app()->request->getParam('key')) {
            throw new CHttpException(400, 'Missing key');
        }
        $license = License::getLicense(Yii::app()->request->getParam('key'));
        if (!$license) {
            throw new CHttpException(400, 'Invalid license');
        }
        return $license;
    }

}