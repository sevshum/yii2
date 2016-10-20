<?php
namespace app\modules\menu\models;

use app\modules\core\components\AppActiveRecord;
use app\modules\core\helpers\App;
use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "menus".
 *
 * The followings are the available columns in table 'menus':
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $created_at
 */
class Menu extends AppActiveRecord 
{

    public static function tableName() 
    {
        return 'menus';
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
                    AppActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
            ]
        ];
    }
    
    public function getRootItem()
    {
        return $this->hasOne(MenuItem::className(), ['menu_id' => 'id'])->where(['depth' => 0]);
    }

    public function rules() 
    {
        return [
            [['name', 'code'], 'required'],
            ['code', 'unique'],
            ['name', 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() 
    {
        return array(
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
        );
    }

    public function search($params) 
    {
        $query = Menu::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
    
    /**
     * @param string $code
     * @return Menu
     */
    public static function getByCode($code)
    {
        return static::findOne(['code' => $code]);
    }
    
    public function getTree($lang = null)
    {
        static $tree = [];        
        if (isset($tree[$this->id])) {
            return $tree[$this->id];
        }
        if ($this->rootItem === null) {
            return [];
        }
        return $tree[$this->id] = MenuItem::find()->withLang($lang)->where([
            'tree' => $this->rootItem->id,
            'menu_id' => $this->id
        ])->orderBy(['lft' => SORT_ASC])->all();
    }
    
    public function listData($lang = null, $index = 'id', $prefix = '  ')
    {
        $tree = $this->getTree($lang);
        if (count($tree) === 0) {
            return [];
        }
        $menu = array($tree[0]->$index => Yii::t('app', 'Without Parent'));
        unset($tree[0]);
        foreach ($tree as $node) {
            $menu[$node->$index] = str_repeat($prefix, $node->depth - 1) . $node->getI18n('name');
        }
        return $menu;
    }
    
    /**
     * Get array for menu widget
     * @return array
     */
    public function getMenu($lang = null)
    {
        $tree = $this->getTree($lang);
        if (count($tree) < 2) {
            return [];
        }
        unset($tree[0]);
        
        // Trees mapped
        $trees = [];
        $l = 0;
        // Node Stack. Used to help building the hierarchy
        $stack = [];

        foreach ($tree as $node) {
            $item = array(
                'url'   => App::createMenuUrl($node->url),
                'label' => $node->getI18n('name'),
                'depth' => $node->depth,                
            );
            $item['active'] = App::detectMenuActive($item['url'], $node->active_condition);
            // Number of stack items
            $l = count($stack);

            // Check if we're dealing with different levels
            while ($l > 0 && $stack[$l - 1]['depth'] >= $node->depth) {
                array_pop($stack);
                $l--;
            }

            // Stack is empty (we are inspecting the root)
            if ($l == 0) {
                // Assigning the root node
                $i = count($trees);
                $trees[$i] = $item;
                $stack[] = & $trees[$i];
            } else {
                // Add node to parent
                if (!isset($stack[$l - 1]['items'])) {
                    $stack[$l - 1]['items'] = [];
                }
                $i = count($stack[$l - 1]['items']);
                $stack[$l - 1]['items'][$i] = $item;
                $stack[] = & $stack[$l - 1]['items'][$i];
            }
        }
        return $trees;
    }
    
    public function afterDelete()
    {
        foreach ($this->getTree() as $item) {
            $item->delete();
        }
        return parent::afterDelete();
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $item = new MenuItem;
            $item->url = '#';
            $item->menu_id = $this->id;
            $item->makeRoot(false);
            $item->setI18n(
                ['parent_id' => $item->id, 'name' => 'root'], 
                Yii::$app->getModule('language')->getDefault(), 
                true
            );
        }
    }
}