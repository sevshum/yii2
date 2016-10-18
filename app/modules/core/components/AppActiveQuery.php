<?php
namespace app\modules\core\components;

class AppActiveQuery extends \yii\db\ActiveQuery
{
    use \app\modules\core\traits\I18nQueryTrait;
    
    /**
     * @param string $status
     * @return AppActiveQuery
     */
    public function byStatus($status)
    {
        $this->andWhere(['status' => $status]);
        return $this;
    }
    
    /**
     * @param string $category
     * @return AppActiveQuery
     */
    public function byCategory($category)
    {
        $this->andWhere(['category_id' => $category]);
        return $this;
    }
    
    /**
     * @return AppActiveQuery
     */
    public function ordered()
    {
        $this->orderBy(['order' => SORT_ASC]);
        return $this;
    }
    
    
}
