<?php
namespace app\modules\language\models;

use app\modules\core\components\AppActiveRecord;
use app\modules\core\helpers\App;
use Locale;
use NumberFormatter;
use Yii;
use yii\helpers\Url;

/**
 * Language model
 *
 * @property integer $id
 * @property string $name
 * @property string $locale
 * @property integer $order
 * @property integer $is_default
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class Language extends AppActiveRecord
{   

    public static function tableName()
    {
        return 'languages';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            ['id', 'string', 'max' => 2],
            [['name', 'locale'], 'string', 'max' => 32],
            ['id', 'checkLanguage', 'params' => ['type' => 'iso']],
            ['locale', 'checkLanguage', 'params' => ['type' => 'locale']],
            [['id', 'name'], 'unique'],
            [['is_active', 'is_default'], 'safe'],
        ];
    }
    
    public function checkLanguage($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {            
            return;            
        }
        if ($params['type'] === 'iso') {
            if (Locale::getDisplayName($this->$attribute) === $this->$attribute) {
                $this->addError($attribute, Yii::t('app', 'Invalid ISO language.'));                
            }
        } elseif ($params['type'] === 'locale') {
            $formatter = numfmt_create($this->$attribute, NumberFormatter::DECIMAL);
            $valid = numfmt_get_locale($formatter, Locale::VALID_LOCALE);
            if ($valid !== $this->$attribute) {
                $this->addError($attribute, Yii::t('app', 'Invalid locale. Did you mean "{suggest}"?', ['suggest' => $valid]) );
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ISO'),
            'name'       => Yii::t('app', 'Name'),
            'is_default' => Yii::t('app', 'Default'),
            'locale'     => Yii::t('app', 'Locale'),
            'is_active'  => Yii::t('app', 'Active'),
            'order'      => Yii::t('app', 'Order'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }
    
    /**
     * Get default language id
     * @staticvar array $default
     * @return array
     */
    public static function getDefault()
    {
        static $default;
        if ($default === null) {
            foreach (self::listing() as $key => $lang) {
                if ($lang['is_default'] == 1) {
                    $default = $key;
                    break;
                }
            }
        }
        if ($default === null) {
            $default = 'en';
        }
        return $default;
    }
    
    /**
     * @staticvar array $languages
     * @param boolean $onlyActive
     * @return array
     */
    public static function listing($onlyActive = true)
    {
        static $languages;
        if ($languages === null) {
            $q = static::find()->orderBy(['order' => SORT_ASC])->asArray()->indexBy('id');
            if ($onlyActive) {
                $q->where(['is_active' => 1]);
            }
            $languages = $q->all();
        }
        return $languages;
    }
    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->order = $this->getMaxOrder() + 1;
        }
        if ($this->isAttributeChanged('is_default') && $this->is_default) {
            $this->updateAll(['is_default' => 0], 'is_default = 1');
        }
        
        return parent::beforeSave($insert);
    }
    
    public function getEditLink($params = [])
    {
        $params[0] = '/languages/languages/update';
        $params['id'] = $this->id;
        return (App::isFront() ? param('backend-link') : '') . Url::to($params);
    }
}