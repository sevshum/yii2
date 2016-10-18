<?php
namespace app\modules\core\traits;

use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\db\Query;

/**
 * Description of DynamicParams
 * 
 * for use this trait you need include to class and define : 
 *  `public static $dynamicOptions = [
 *      'a' => ['saveEmpty' => false],
 *      'b' => [
 *          'rules' => [['b', 'required']],
 *      ],
 *  ];`
 *  
 *  add to rules default safe rules or custom rules
 * 
 *  $rules = $this->applyDynamicRules($rules);
 *
 */
trait DynamicParamTrait
{
    public static $dynamicParamsTable = 'dynamic_params';
    
    protected $_dynamicAttributes;
    protected $_dynamicNeedSave = false;
    
    public function init()
    {
        parent::init();
        $this->attachEvents();
    }
    
    public function attachEvents()
    {
        $this->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'dynamicAfterSave']);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'dynamicAfterSave']);
        $this->on(ActiveRecord::EVENT_AFTER_DELETE, [$this, 'dynamicAfterDelete']);
    }
    
    public function applyDynamicRules($rules)
    {
        if (!empty(static::$dynamicOptions)) {
            foreach (static::$dynamicOptions as $key => $options) {
                if (empty($options['rules'])) {
                    $rules[] = [[$key], 'safe'];
                } else {
                    foreach ($options['rules'] as $rule) {
                        $rules[] = $rule;
                    }
                }
            }
        }
        return $rules;
    }
    
    public function getDynamicParams()
    {
        if ($this->_dynamicAttributes === null) {
            $this->_dynamicAttributes = [];
            if (!empty(static::$dynamicOptions) && !$this->getIsNewRecord()) {
                $q = new Query;
                $params = $q->from(static::$dynamicParamsTable)->where([
                    'entity_type' => get_class($this), 'entity_id' => $this->getAttribute('id')
                ])->all();
                $this->_dynamicAttributes = ArrayHelper::map($params, 'key', 'value');
            }
        }
        return $this->_dynamicAttributes;
    }
    
    protected function _saveDynamicParams()
    {
        $this->_removeDynamicParams();
        if (empty($this->_dynamicAttributes)) {
            return 0;
        }
        $type = get_class($this);
        $data = [];
        foreach ($this->_dynamicAttributes as $key => $value) {
            $data[] = [$this->id, $type, $key, $value];
        }

        return static::getDb()->createCommand()
            ->batchInsert(static::$dynamicParamsTable, ['entity_id', 'entity_type', 'key', 'value'], $data)
            ->execute();
    }

    protected function _removeDynamicParams()
    {
        if ($this->getIsNewRecord()) {
            return 0;
        }
        return static::getDb()->createCommand()->delete(static::$dynamicParamsTable, [
            'entity_type' => get_class($this), 'entity_id' => $this->getAttribute('id')
        ])->execute();
    }

    public function __get($name)
    {
        $params = $this->getDynamicParams();
        if (isset($params[$name])) {
            return $params[$name];
        } elseif (isset(static::$dynamicOptions[$name])) {
            return null;
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (isset(static::$dynamicOptions[$name])) {
            if (!empty($value) || !empty(static::$dynamicOptions[$name]['saveEmpty'])) { //save empty value or not
                $this->_dynamicAttributes[$name] = $value;
            }
            return;
        }
        parent::__set($name, $value);
    }
    
    /**
     * @param AfterSaveEvent  $e
     */
    public function dynamicAfterSave($e)
    {
        if (!empty(static::$dynamicOptions)) {
            $this->_saveDynamicParams();
        }
    }
    
    public function dynamicAfterDelete()
    {
        if (!empty(static::$dynamicOptions)) {
            $this->_removeDynamicParams();
        }
    }
}
