<?php

/**
 * This is the model class for table "license".
 *
 * The followings are the available columns in table 'license':
 * @property integer $id
 * @property integer $expires
 * @property string $key
 * @property string $domain
 *
 * The followings are the available model relations:
 * @property Page[] $pages
 */
class License extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return License the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'license';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('expires, key, domain', 'required'),
            array('expires', 'numerical', 'integerOnly' => true),
            array('key', 'length', 'max' => 32),
            array('domain', 'length', 'max' => 256),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, expires, key, domain', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'pages' => array(self::HAS_MANY, 'Page', 'license_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'expires' => 'Expires',
            'key' => 'Key',
            'domain' => 'Domain',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('expires', $this->expires);
        $criteria->compare('key', $this->key, true);
        $criteria->compare('domain', $this->domain, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getLicense($key)
    {
        $license = License::model()->findByAttributes(array('key' => $key));
        if ($license) {
            if($license->expires < time()) {
                return false;
            }
            return $license;
        } else {
            return false;
        }
    }

}