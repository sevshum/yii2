<?php

namespace app\modules\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profiles".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $avatar
 * @property string $city
 * @property string $country
 * @property string $address
 * @property string $description
 * @property string $updated_at
 */
class Profile extends ActiveRecord
{
    public $image;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profiles';
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            'mImage' => ['class' => '\maxlapko\components\ImageBehavior']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [            
            [['description'], 'string'],
            [['city'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 120],
            [
                'image', '\maxlapko\components\ImageValidator',
                'extensions' => array('jpg', 'png', 'jpeg'), 'maxSize' => 5 * 1024 * 1024,
            ],
        ];
    }
    
    /**
     * @param string $url
     */
    public function setAvatar($url)
    {
        $imageData = @file_get_contents($url);
        if (!$imageData) {
            return;
        }
        $filename = Yii::$app->getRuntimePath() . '/' . uniqid() . '.jpg';
        file_put_contents($filename, $imageData);
        $image = Yii::$app->get('image')->upload($filename, $this->formName());
        $this->avatar = $image['filename'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'image' => Yii::t('app', 'Avatar'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
            'address' => Yii::t('app', 'Address'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
}