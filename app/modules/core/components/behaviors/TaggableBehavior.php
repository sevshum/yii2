<?php

namespace app\modules\core\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\ModelEvent;
use yii\caching\Cache;
use yii\caching\DummyCache;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * Provides tagging ability for a model.
 */
class TaggableBehavior extends Behavior
{

    /**
     * @var string tags table name.
     */
    public $tagTable = 'Tag';

    /**
     * @var string tag table field that contains tag name.
     */
    public $tagTableName = 'name';

    /**
     * @var string tag table PK name.
     */
    public $tagTablePk = 'id';

    /**
     * @var string tag to Model binding table name.
     * Defaults to `{model table name}Tag`.
     */
    public $tagBindingTable;

    /**
     * @var Expression Custom expression for finding the tag if we are using magic fields
     */
    public $tagTableCondition;

    /**
     * @var string binding table tagId name.
     */
    public $tagBindingTableTagId = 'tagId';

    /**
     * @var string|null tag table count field. If null don't uses database.
     */
    public $tagTableCount;

    /**
     * @var string binding table model FK field name.
     * Defaults to `{model table name with first lowercased letter}Id`.
     */
    public $modelTableFk;

    /**
     * @var boolean which create tags automatically or throw exception if tag does not exist.
     */
    public $createTagsAutomatically = true;

    /**
     * @var string|boolean caching component Id. If false don't use cache.
     * Defaults to false.
     */
    public $cacheID = false;
    private $tags = [];
    private $originalTags = [];

    /**
     * @var Connection
     */
    private $_conn;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array|Query Used as filter in find, load, create or update tags.
     */
    public $query = [];

    /**
     * @var array these values are added on inserting tag into DB.
     */
    public $insertValues = [];
    
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * Get DB connection.
     * @return Connection
     */
    protected function getConnection()
    {
        if (!isset($this->_conn)) {
            $this->_conn = $this->owner->getDb();
        }
        return $this->_conn;
    }

    /**
     * @param ActiveRecord $owner
     * @return void
     */
    public function attach($owner)
    {
        // Prepare cache component
        if ($this->cacheID !== false)
            $this->cache = Yii::$app->get($this->cacheID);
        if (!($this->cache instanceof Cache)) {
            // If not set cache component, use dummy cache.
            $this->cache = new DummyCache();
        }

        parent::attach($owner);
    }

    /**
     * Allows to print object.
     * @return string
     */
    public function toString()
    {
        $this->loadTags();
        return implode(', ', $this->tags);
    }

    /**
     * Get tag binding table name.
     * @access private
     * @return string
     */
    private function getTagBindingTableName()
    {
        if ($this->tagBindingTable === null) {
            $this->tagBindingTable = $this->owner->tableName() . 'Tag';
        }
        return $this->tagBindingTable;
    }

    /**
     * Get model table FK name.
     * @access private
     * @return string
     */
    private function getModelTableFkName()
    {
        if ($this->modelTableFk === null) {
            $tableName = $this->owner->tableName();
            $tableName[0] = strtolower($tableName[0]);
            $this->modelTableFk = $tableName . 'Id';
        }
        return $this->modelTableFk;
    }

    /**
     * Set one or more tags.
     * @param string|array $tags
     * @return void
     */
    public function setTags($tags)
    {
        $tags = $this->toTagsArray($tags);
        $this->tags = array_unique($tags);

        return $this->owner;
    }

    /**
     * Add one or more tags.
     * @param string|array $tags
     * @return void
     */
    public function addTags($tags)
    {
        $this->loadTags();

        $tags = $this->toTagsArray($tags);
        $this->tags = array_unique(array_merge($this->tags, $tags));

        return $this->owner;
    }

    /**
     * Alias of {@link addTags()}.
     * @param string|array $tags
     * @return void
     */
    public function addTag($tags)
    {
        return $this->addTags($tags);
    }

    /**
     * Remove one or more tags.
     * @param string|array $tags
     * @return void
     */
    public function removeTags($tags)
    {
        $this->loadTags();

        $tags = $this->toTagsArray($tags);
        $this->tags = array_diff($this->tags, $tags);

        return $this->owner;
    }

    /**
     * Alias of {@link removeTags}.
     * @param string|array $tags
     * @return void
     */
    public function removeTag($tags)
    {
        return $this->removeTags($tags);
    }

    /**
     * Remove all tags.
     * @return void
     */
    public function removeAllTags()
    {
        $this->loadTags();
        $this->tags = [];
        return $this->owner;
    }

    /**
     * Get default scope criteria.
     * @return Query
     */
    protected function getQuery()
    {
        if (is_array($this->query)) {
            $q = new Query;
            $this->query = $q->where($this->query);
        }
        return $this->query;
    }

    /**
     * Get tags.
     * @return array
     */
    public function getTags()
    {
        $this->loadTags();
        return $this->tags;
    }

    /**
     * Get current model's tags with counts.
     * @todo: quick implementation, rewrite!
     * @param Query $criteria
     * @return array
     */
    public function getTagsWithModelsCount($criteria = null)
    {
        if (!($tags = $this->cache->get($this->getCacheKey() . 'WithModelsCount'))) {

            $builder = new Query;

            if ($this->tagTableCount !== null) {
                $builder->select("t.{$this->tagTableName} as `name`, t.{$this->tagTableCount} as `count`")
                        ->innerJoin($this->getTagBindingTableName() . ' et', "t.{$this->tagTablePk} = et.{$this->tagBindingTableTagId}")
                        ->where(['et.' . $this->getModelTableFkName() => $this->owner->getPrimaryKey()]);
            } else {
                $builder->select("t.{$this->tagTableName} as `name`, count(*) as `count` ")
                        ->innerJoin($this->getTagBindingTableName() . ' et', "t.{$this->tagTablePk} = et.{$this->tagBindingTableTagId}")
                        ->where(['et.' . $this->getModelTableFkName() => $this->owner->getPrimaryKey()])
                        ->groupBy('t.' . $this->tagTablePk);
            }

            if ($criteria) {
                $builder;
            }

            $tags = $builder->from($this->tagTable . ' t')->all();

            $this->cache->set($this->getCacheKey() . 'WithModelsCount', $tags);
        }

        return $tags;
    }

    /**
     * Get tags array from comma separated tags string.
     * @access private
     * @param string|array $tags
     * @return array
     */
    protected function toTagsArray($tags)
    {
        if (!is_array($tags)) {
            $tags = explode(',', trim(strip_tags($tags), ' ,'));
        }

        array_walk($tags, [$this, 'trim']);
        return $tags;
    }

    /**
     * Used as a callback to trim tags.
     * @access private
     * @param string $item
     * @param string $key
     * @return string
     */
    private function trim(&$item, $key)
    {
        $item = trim($item);
    }

    /**
     * If we need to save tags.
     * @access private
     * @return boolean
     */
    private function needToSave()
    {
        $diff = array_merge(
            array_diff($this->tags, $this->originalTags), array_diff($this->originalTags, $this->tags)
        );

        return !empty($diff);
    }
    
    public function afterInsert($event)
    {
        return $this->afterSave($event, true);
    }
    
    public function afterUpdate($event)
    {
        return $this->afterSave($event, false);
    }
    
    /**
     * Saves model tags on model save.
     * @param ModelEvent $event
     * @throw Exception
     */
    public function afterSave($event, $insert)
    {
        if ($this->needToSave()) {

            $builder = $this->getConnection()->createCommand();

            if (!$this->createTagsAutomatically) {
                // checking if all of the tags are existing ones
                
                foreach ($this->tags as $tag) {

                    $find = new Query();
                    $find->select($this->tagTablePk)->from($this->tagTable)->where([$this->tagTableName => $tag]);
                    if ($q = $this->query) {
                        $find->andWhere($q);
                    }
                    $tagId = $find->createCommand()->queryScalar();

                    if (!$tagId) {
                        throw new Exception("Tag \"$tag\" does not exist. Please add it before assigning or enable createTagsAutomatically.");
                    }
                }
            }

            if (!$insert) {
                // delete all present tag bindings if record is existing one
                $this->deleteTags();
            }

            // add new tag bindings and tags if there are any
            if (!empty($this->tags)) {
                foreach ($this->tags as $tag) {
                    if (empty($tag)) return;

                    $find = new Query();
                    $find->select($this->tagTablePk)->from($this->tagTable)->where([$this->tagTableName => $tag]);
                    if ($q = $this->query) {
                        $find->andWhere($q);
                    }
                    $tagId = $find->createCommand()->queryScalar();
                    // if there is no existing tag, create one
                    if (!$tagId) {
                        $this->createTag($tag);
                        // reset all tags cache
                        $this->resetAllTagsCache();
                        $this->resetAllTagsWithModelsCountCache();

                        $tagId = $this->getConnection()->getLastInsertID();
                    }
                    
                    $builder->insert($this->getTagBindingTableName(), [
                        $this->getModelTableFkName() => $this->owner->primaryKey,
                        $this->tagBindingTableTagId  => $tagId
                    ])->execute();
                }
                $this->updateCount(+1);
            }


            $this->cache->set($this->getCacheKey(), $this->tags);
        }

        return $event;
    }

    /**
     * Reset cache used for {@link getAllTags()}.
     * @return void
     */
    public function resetAllTagsCache()
    {
        $this->cache->delete('Taggable' . $this->owner->tableName() . 'All');
    }

    /**
     * Reset cache used for {@link getAllTagsWithModelsCount()}.
     * @return void
     */
    public function resetAllTagsWithModelsCountCache()
    {
        $this->cache->delete('Taggable' . $this->owner->tableName() . 'AllWithCount');
    }

    /**
     * Deletes tag bindings on model delete.
     * @param Event $event
     * @return void
     */
    public function afterDelete($event)
    {
        // delete all present tag bindings
        $this->deleteTags();

        $this->cache->delete($this->getCacheKey());
        $this->resetAllTagsWithModelsCountCache();
        return $event;
    }

    /**
     * Load tags into model.
     * @params array $criteria, defaults to null.
     * @access protected
     * @return void
     */
    protected function loadTags($criteria = null)
    {
        if ($this->tags != null)
            return;
        if ($this->owner->getIsNewRecord())
            return;

        if (!($tags = $this->cache->get($this->getCacheKey()))) {
            $q = new Query;
            $q->select("{$this->tagTableName} as `name`")
              ->from($this->tagTable . ' t')
              ->innerJoin("{$this->getTagBindingTableName()} et", "t.{$this->tagTablePk} = et.{$this->tagBindingTableTagId}" )
              ->where(["et.{$this->getModelTableFkName()}" => $this->owner->getPrimaryKey()]);
            
            if ($criteria) {
                $q->andWhere($criteria);
            }
            if ($this->query) {
                $q->andWhere($this->query);
            }

            $tags = $q->createCommand()->queryColumn();
            $this->cache->set($this->getCacheKey(), $tags);
        }

        $this->originalTags = $this->tags = $tags;
    }

    /**
     * Returns key for caching specific model tags.
     * @return string
     */
    private function getCacheKey()
    {
        return $this->getCacheKeyBase() . $this->owner->primaryKey;
    }

    /**
     * Returns cache key base.
     * @return string
     */
    private function getCacheKeyBase()
    {
        return 'Taggable' .
            $this->owner->tableName() .
            $this->tagTable .
            $this->tagBindingTable .
            $this->tagTableName .
            $this->getModelTableFkName() .
            $this->tagBindingTableTagId .
            json_encode($this->query);
    }

    /**
     * Get criteria to limit query by tags.
     * @access private
     * @param array $tags
     * @return Query
     */
    protected function getFindByTagsCriteria($tags)
    {
        $q = new Query();

        $pk = $this->owner->tableSchema->primaryKey;

        if (!empty($tags)) {
            $conn = $this->getConnection();
            $q->select('t.*');
            for ($i = 0, $count = count($tags); $i < $count; $i++) {
                $tag = $conn->quoteValue($tags[$i]);
                $q->innerJoin("{$this->getTagBindingTableName()} bt$i", "t.{$pk} = bt$i.{$this->getModelTableFkName()}");
                $q->innerJoin("{$this->tagTable} tag$i", "tag$i.{$this->tagTablePk} = bt$i.{$this->tagBindingTableTagId} AND tag$i.`{$this->tagTableName}` = $tag");
            }
        }

        if ($this->query) {
            $q->andWhere($this->query);
        }

        return $q;
    }

    /**
     * Get all possible tags for current model class.
     * @param array $criteria
     * @return array
     */
    public function getAllTags($criteria = null)
    {
        if (!($tags = $this->cache->get('Taggable' . $this->owner->tableName() . 'All'))) {
            // getting associated tags
            $q = new Query();
            $q->select($this->tagTableName);
            if ($criteria) {
                $q->andWhere($criteria);
            }
            if ($this->query) {
                $q->andWhere($this->query);
            }
            $tags = $q->from($this->tagTable . ' t')->createCommand()->queryColumn();

            $this->cache->set('Taggable' . $this->owner->tableName() . 'All', $tags);
        }

        return $tags;
    }

    /**
     * Get all possible tags with models count for each for this model class.
     * @param array $criteria
     * @return array
     */
    public function getAllTagsWithModelsCount($criteria = null)
    {
        if (!($tags = $this->cache->get('Taggable' . $this->owner->tableName() . 'AllWithCount'))) {
            // getting associated tags
            $q = new Query();

            if ($this->tagTableCount !== null) {
                $q->select(sprintf(
                    "t.%s as `name`, %s as `count`", $this->tagTableName, $this->tagTableCount
                ));
            } else {
                $q->select(sprintf(
                    "t.%s as `name`, count(*) as `count`", $this->tagTableName
                ));
                $q->innerJoin("{$this->getTagBindingTableName()} et", "t.{$this->tagTablePk} = et.{$this->tagBindingTableTagId}");
                $q->groupBy('t.' . $this->tagTablePk);
            }

            if ($criteria !== null)
                $q->andWhere($criteria);

            if ($this->query)
                $q->andWhere($this->query);


            $tags = $q->from("$this->tagTable t")->createCommand()->queryAll();

            $this->cache->set('Taggable' . $this->owner->tableName() . 'AllWithCount', $tags);
        }

        return $tags;
    }

    /**
     * Finds out if model has all tags specified.
     * @param string|array $tags
     * @return boolean
     */
    public function hasTags($tags)
    {
        $this->loadTags();

        $tags = $this->toTagsArray($tags);
        foreach ($tags as $tag) {
            if (!in_array($tag, $this->tags)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Alias of {@link hasTags()}.
     * @param string|array $tags
     * @return boolean
     */
    public function hasTag($tags)
    {
        return $this->hasTags($tags);
    }

    /**
     * Limit current AR query to have all tags specified.
     * @param string|array $tags
     * @return ActiveRecord
     */
    public function taggedWith($tags)
    {
        $tags = $this->toTagsArray($tags);

        if (!empty($tags)) {
            $criteria = $this->getFindByTagsCriteria($tags);
            $this->owner->getDbCriteria()->mergeWith($criteria);
        }

        return $this->owner;
    }

    /**
     * Alias of {@link taggedWith()}.
     * @param string|array $tags
     * @return ActiveRecord
     */
    public function withTags($tags)
    {
        return $this->taggedWith($tags);
    }

    /**
     * Delete all present tag bindings.
     * @return void
     */
    protected function deleteTags()
    {
        $this->updateCount(-1);
        $where = [];
        $connection = $this->getConnection();
        $sql = 'DELETE FROM ' . $connection->quoteTableName($this->getTagBindingTableName());
        if ($this->query) {
            $sql .= ' INNER JOIN ' . $connection->quoteTableName($this->tagTable) . ' AS t' . 
                "ON t.{$this->tagTablePk} = " . $connection->quoteTableName($this->getTagBindingTableName()) . '.' . $this->tagBindingTableTagId;
            $where = $this->query;
        }
        $where[$this->getModelTableFkName()] = $this->owner->getPrimaryKey();
        $params = [];
        $where = $connection->getQueryBuilder()->buildWhere($where, $params);
        return $connection->createCommand($sql . ' ' . $where, $params)->execute();
    }

    /**
     * Creates a tag.
     * Method is for future inheritance.
     * @param string $tag tag name.
     * @return void
     */
    protected function createTag($tag)
    {

        $builder = $this->getConnection()->createCommand();

        $values = [
            $this->tagTableName => $tag
        ];
        if (is_array($this->insertValues)) {
            $values = array_merge($this->insertValues, $values);
        }

        $builder->insert($this->tagTable, $values)->execute();
    }

    /**
     * Updates counter information in database.
     * Used if {@link tagTableCount} is not null.
     * @param int $count incremental ("1") or decremental ("-1") value.
     * @return void
     */
    protected function updateCount($count)
    {
        if ($this->tagTableCount !== null) {
            $conn = $this->getConnection();
            $conn->createCommand(
                sprintf(
                    "UPDATE %s SET %s = %s + %s WHERE %s in (SELECT %s FROM %s WHERE %s = %d)", 
                    $this->tagTable, 
                    $this->tagTableCount, 
                    $this->tagTableCount, 
                    $count, 
                    $this->tagTablePk, 
                    $this->tagBindingTableTagId, 
                    $this->getTagBindingTableName(), 
                    $this->getModelTableFkName(), 
                    $this->owner->primaryKey
                )
            )->execute();
        }
    }

}
