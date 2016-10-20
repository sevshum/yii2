<?php
namespace app\modules\menu\models;

use app\modules\core\components\AppActiveRecord;
use Yii;

class MenuItemI18n extends AppActiveRecord 
{
    public $dirtyParams = ['name'];

    public static function tableName() 
    {
        return 'menu_item_i18ns';
    }

    public function rules() 
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            [['name', 'lang_id'], 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() 
    {
        return [
            'name' => Yii::t('app', 'Name'),
        ];
    }
}