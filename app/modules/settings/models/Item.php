<?php

namespace app\modules\settings\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $group
 * @property string $key
 * @property string $value
 * @property integer $order
 */
class Item extends ActiveRecord
{
   /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'key'], 'required'],
            [['key'], 'unique', 'targetAttribute' => ['group', 'key']],
            [['group', 'key', 'value', 'order'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => Yii::t('app', 'Group'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
            'order' => Yii::t('app', 'Order'),
        ];
    }
    
    public function search($params)
    {   
        $query = Item::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => '`group` ASC, `order` ASC'
            ]
        ]);
        
        if (!$this->load($params)) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['group' => $this->group]);

        return $dataProvider;
    }
    
    public static function listing()
    {
        static $listing;
        if ($listing === null) {
            $listing = Item::find()->asArray()->orderBy('`group` ASC, `order` ASC')->all();
        }
        return $listing;
    }
    
    /**
     * @param string $group
     * @return array
     */
    public static function getByGroup($group)
    {
        return ArrayHelper::map(static::find()
            ->where(['group' => $group])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()
            ->all(),
            'key', 'value'
        );
    }
    
    public static function getGroups()
    {
        return ArrayHelper::map(static::find()
            ->select('DISTINCT(`group`) AS `g`')
            ->orderBy(['group' => SORT_ASC])
            ->createCommand()
            ->queryAll(),
            'g', 'g'
        );
    }


    public static function set($data)
    {
        return static::getDb()->createCommand('
            INSERT INTO `' . static::tableName() . '` (`group`, `key`, `value`, `order`)
                VALUES (:group, :key, :value, :order)
            ON DUPLICATE KEY UPDATE `value` = :value, `order` = :order',
            $data
        )->execute();
    }
}