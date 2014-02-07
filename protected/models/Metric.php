<?php

/**
 * This is the model class for table "metric".
 *
 * The followings are the available columns in table 'metric':
 * @property integer $id
 * @property integer $timestamp
 * @property integer $type
 * @property integer $value
 * 
 * The followings are the available model relations:
 * @property Page[] $page
 */
class Metric extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'metric';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('timestamp, type, value', 'required'),
            array('timestamp, type, value', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, timestamp, type, value', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'page' => array(self::BELONGS_TO, 'Page', 'page_id'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Metric the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function updateMetric($value = null)
    {
        $this->type = static::$_type;
        $this->timestamp = time();
        if (isset($value)) {
            $this->value = $value;
        } else {
            $this->value = $this->fetchValue();
        }
        $this->save();
        return $this->value;
    }

    public function toArray()
    {
        return array(
            'value' => (int) $this->value,
            'timestamp' => date('c', $this->timestamp),
        );
    }

    public static function findAllByPageId($pageId, $from, $to)
    {
        $command = self::getFindCommand($from, $to);
        $command->andWhere('page_id = :page_id', array('page_id' => $pageId));
        return $command->queryAll();
    }

    public static function findAllByLicense($licenseId, $from, $to)
    {
        $command = self::getFindCommand($from, $to);
        $command->select('timestamp, value, page_id');
        $command->join('page', 'metric.page_id = page.id');
        $command->andWhere('license_id = :license_id', array('license_id' => $licenseId));
        $command->order('timestamp ASC');
        $metrics = $command->queryAll();
        return self::combinePageMetrics($metrics);
    }

    protected static function getFindCommand($from, $to)
    {
        $command = Yii::app()->db->createCommand()
                ->select('timestamp, value')
                ->from('metric')
                ->where('type = :type', array(':type' => static::$_type));
        return $command;
    }

    /**
     * Combines metrics of many pages to a single array 
     * @param type $metrics
     * @return type
     */
    protected static function combinePageMetrics($metrics)
    {
        $lastValueByPage = array();
        $lastValue = 0;
        $combinedMetrics = array();
        foreach ($metrics as $metric) {
            //Last value by page
            if (isset($lastValueByPage[$metric['page_id']])) {
                $value = $metric['value'] - $lastValueByPage[$metric['page_id']];
            } else {
                $value = $metric['value'];
            }

            if (isset($combinedMetrics[$metric['timestamp']])) {
                $combinedMetrics[$metric['timestamp']]['value'] += $value + $lastValue;
            } else {
                $combinedMetrics[$metric['timestamp']] = array(
                    'value' => $value + $lastValue,
                    'timestamp' => $metric['timestamp'],
                );
            }
            $lastValueByPage[$metric['page_id']] = $metric['value'];
            $lastValue = $value + $lastValue;
        }
        return array_values($combinedMetrics);
    }

}
