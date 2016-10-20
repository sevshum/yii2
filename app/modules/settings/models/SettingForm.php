<?php

namespace app\modules\settings\models;

use Yii;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SettingForm extends \yii\base\Model
{
    /**
     * Array of key = field name such as group.name
     * value:
     *   validators: array of validators without field name
     *   input: string|callable default `textInput` 
     *      'input' => 'checkbox',
     *      'input' => function($field) {
     *          return $field->dropDownList(['a' => 'A', 'b' => 'B']);
     *      }
     *   default: mixed default value
     *   beforeSave: callable 
     * @return array
     */
    public function fields()
    {   
        return array_merge($this->all(), [
            'app.system_email' => [
                'validators' => [['email']],
            ],
            'app.name' => [
                'validators' => [['required'], ['string', 'min' => 3]]
            ]
        ]);
    }
    
    protected $_fields;
    
    protected $_data = [];
    
    public function all()
    {
        static $all;
        if ($all === null) {
            $all = \yii\helpers\ArrayHelper::map(
                Item::listing(), 
                function($item) {
                    return $item['group'] . '.' . $item['key'];
                }, 
                function() { return []; }
            );
        }
        return $all;
    }
    
    public function rules()
    {
        $rules = [];
        foreach ($this->getFields() as $name => $options) {
            foreach ($options['validators'] as $v) {
                $rules[] = array_merge([$name], $v);
            }
        }
        return $rules;
    }
    
    
    public function getFields()
    {
        if ($this->_fields === null) {
            $this->_fields = [];
            foreach ($this->fields() as $i => $f) {
                if (is_integer($i)) {
                    $i = $f;
                    $f = [];
                }
                if (!isset($f['validators'])) {
                    $f['validators'] = [['safe']];
                }
                $this->_fields[$i] = array_merge([
                    'input' => 'textInput',
                    'default' => null
                ], $f);
            }
        }
        return $this->_fields;
    }
    
    /**
     * 
     * @param \yii\widgets\ActiveForm $form
     * @param SettingForm $model
     */
    public function renderFields($form, $model)
    {
        foreach ($this->getFields() as $name => $options) {
            $field = $form->field($model, $name);
            if (is_callable($options['input'])) {
                echo call_user_func($options['input'], $field);
            } else {
                echo call_user_func([$field, $options['input']]);
            }
        }
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $db = Yii::$app->getDb();
        
        $t = $db->beginTransaction();
        try {
            $fields = $this->getFields();
            $settings = Yii::$app->getModule('settings');
            foreach ($this->_data as $key => $value) {
                if (isset($fields[$key]['beforeSave'])) {
                    $value = $fields[$key]['beforeSave']($value);
                }
                $settings->setParam($key, $value);
            }
            $t->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
            $t->rollBack();
        }
        return false;
    }
    
    public function __set($name, $value)
    {
        $fields = $this->fields();
        if (isset($fields[$name])) {
            $this->_data[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }
    
    public function __get($name)
    {
        $fields = $this->fields();
        if (isset($fields[$name])) {
            if (!array_key_exists($name, $this->_data)) {
                $fields = $this->getFields();
                $this->_data[$name] = Yii::$app->getModule('settings')->getParam($name, $fields[$name]['default']);
            }
            return $this->_data[$name];
        }
        return parent::__get($name);
    }
    
}

