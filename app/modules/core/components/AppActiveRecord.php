<?php
namespace app\modules\core\components;

use app\modules\core\components\AppActiveQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * EActiveRecord class
 *
 * Some cool methods to share amount your models
 */
class AppActiveRecord extends ActiveRecord
{
    
    const MOVE_UP = 'up';
    const MOVE_DOWN = 'down';
    
    protected $_nestedRelations = [];    
    
    /**
     * @return AppActiveQuery
     */
    public static function find()
    {
        return new AppActiveQuery(get_called_class());
    }

    public function moveEntry($action, $criteria = [])
    {
        if ($action == self::MOVE_UP) {
            $criteria['order'] = $this->order - 1;
            $this->updateAllCounters(['order' => 1], $criteria);
            $this->updateCounters(['order' => -1]);
        } elseif ($action == self::MOVE_DOWN) {
            $criteria['order'] = $this->order + 1;
            $this->updateAllCounters(['order' => -1], $criteria);
            $this->updateCounters(['order' => 1]);
        }
        return $this;
    }
    
    public function getMaxOrder($condition = null, $params = [])
    {
        $q = new Query;
        
        $q->select('MAX(`order`) as maxOrder')
          ->from($this->tableName());
        
        if ($condition !== null) {
            $q->where($condition, $params);
        }
        return $q->createCommand()->queryScalar();
    }
    
    
    /**
     *
     * @param string $relation
     * @param mixed $data
     * @throws Exception 
     */
    public function setNestedRelation($relation, $data) 
    {
        $rel = $this->getRelation($relation, false);
        if ($rel === null) {
            throw new Exception('Could not found "' . $relation . '" relation.');
        }
        $this->_nestedRelations[$relation] = $rel;
        if ($rel->multiple) {
            $this->_setNestedManyRelation($relation, $data, $rel);
        } else {
            $this->_setNestedOneRelation($relation, $data, $rel);
        }
    }
    
    /**
     *
     * @param array $data
     * @param ActiveQuery $relationData
     * @throws Exception 
     */
    protected function _setNestedManyRelation($relation, $data, $relationData)
    {
        $rels = [];
        $modelClass = $relationData->modelClass;
        $index = $relationData->indexBy ? $relationData->indexBy : 'id';
        $isIndex = $relationData->indexBy === $index;
        if ($this->getIsNewRecord()) {
            foreach ($data as $attr) {
                $q = new $modelClass();
                $q->setAttributes($attr);
                if ($isIndex) {
                    $rels[$attr[$index]] = $q;
                } else {
                    $rels[] = $q;
                }
            }            
        } else {
            $relationIds = [];
            foreach ($data as $attr) {
                if (!empty($attr[$index])) {
                    $relationIds[] = $attr[$index]; 
                }                
            }
            if (count($relationIds) > 0) {
                $relationIds = $relationData->andWhere([$index => $relationIds])->indexBy($index)->all();
            }
            $id = microtime(true);
            foreach ($data as $attr) {
                if (!empty($attr[$index]) && isset($relationIds[$attr[$index]])) {
                    $q = $relationIds[$attr[$index]];
                    $q->setAttributes($attr);
                    if ($isIndex) {
                        $rels[$attr[$index]] = $q;
                    } else {
                        $rels[] = $q;
                    }
                } else {
                    $q = new $modelClass();
                    $q->setAttributes($attr);
                    if ($isIndex) {
                        $rels[$attr[$index] ?: ++$id] = $q;
                    } else {
                        $rels[] = $q;
                    }
                }             
            }
        }
        $this->populateRelation($relation, $rels);
    }
    
    /**
     *
     * @param array $data
     * @param ActiveQuery $relationData
     * @throws Exception 
     */
    protected function _setNestedOneRelation($relation, $data, $relationData)
    {
        $rel = null;
        $modelClass = $relationData->modelClass;
        
        if ($this->getIsNewRecord()) {
            $rel = new $modelClass();
            $rel->setAttributes($data);
        } else {
            $rel = $relationData->one();
            $rel->setAttributes($data);
        }
        $this->populateRelation($relation, $rel);
    }
    
    /**
     *
     * @param mixed $attributes
     * @param boolean $clearErrors
     * @return boolean 
     */
    public function validate($attributes = null, $clearErrors = true)
    {
        $valid = true;
        if (!empty($this->_nestedRelations)) {
            foreach ($this->_nestedRelations as $rel => $option) {
                if ($option->multiple) {
                    foreach ($this->$rel as $r) {
                        $values = $r->getDirtyAttributes();
                        if (empty($r->dirtyParams) || 
                            (!empty($values) && $this->_isDirty($values, $r->dirtyParams))
                        ) {
                            $valid &= $r->validate();                            
                        }
                    }
                } else {
                    $valid &= $this->{$rel}->validate();
                }
                
            }
        }
        return parent::validate($attributes, $clearErrors) && $valid;
    }
    
    /**
     * Custom after save 
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->_nestedRelations)) {
            foreach ($this->_nestedRelations as $rel => $option) {
                if ($option->multiple) {
                    $index = $option->indexBy ? $option->indexBy : 'id';
                    $oldModels = $option->indexBy($index)->all();
                    foreach ($this->$rel as $r) {
                        if (isset($oldModels[$r->$index])) {
                            unset($oldModels[$r->$index]);
                        }
                        if (!$this->_isDirty($r->getDirtyAttributes(), isset($r->dirtyParams) ? $r->dirtyParams : null)) {
                            continue;
                        }
                        if ($r->getIsNewRecord()) {
                            $this->link($rel, $r);
                        } else {
                            $r->save(false);
                        }
                    }
                    foreach ($oldModels as $m) {
                        $m->delete();
                    }
                } else {
                    if ($this->{$rel}->getIsNewRecord()) {
                        $this->link($rel, $this->$rel);
                    } else {
                        $this->{$rel}->save(false);
                    }
                }
            }           
        }
        parent::afterSave($insert, $changedAttributes);
    }
    
    public static function sorting($table, $ids)
    {
        $command = static::getDb()->createCommand(
            'UPDATE `' . $table . '` SET `order` = :order WHERE `id` = :id'
        );
        foreach ($ids as $order => $id) {
            $command->bindValues([':order' => $order, ':id' => $id])->execute();
        }
    }
    
    /**
     * @return string
     */
    public function getEntityType()
    {
        return strtr(get_class($this), ['\\' => '_']);
    }
    
    protected function _isDirty($data, $attributes)
    {
        if ($attributes === null) {
            return true;
        }
        foreach ($attributes as $a) {
            if (!empty($data[$a])) {
                return true;
            }
        }
        return false;
    }
}
