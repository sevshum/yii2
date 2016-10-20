<?php
namespace app\modules\menu\models;

use app\modules\core\components\AppActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use Yii;

class MenuItem extends AppActiveRecord
{
    use \app\modules\core\traits\I18nActiveRecordTrait;
    use \app\modules\core\traits\TreeTrait;
    
    public $i18nModel = 'app\modules\menu\models\MenuItemI18n';

    public static function tableName() 
    {
        return 'menu_items';
    }

    
    public function rules() 
    {
        return [
            [['parent_id', 'url'], 'required'],
            [['url'], 'string', 'max' => 255],
            [['translateattrs', 'active_condition'], 'safe']
        ];
    }
    
    /**
     * @return static
     */
    public static function find()
    {
        $query = parent::find();
        $query->attachBehavior('nested_set', NestedSetsQueryBehavior::className());
        return $query;
    }
    
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    
    public function behaviors()
    {
        return array(
            'nestedSetBehavior' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
                'attributes' => [
                    AppActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ]
        );
    }
    
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menu_id']);
    }

    public function attributeLabels() 
    {
        return array(
            'id' => 'ID',
            'url' => Yii::t('app', 'Url'),
            'parent_id' => Yii::t('app', 'Parent'),
        );
    }
    
}