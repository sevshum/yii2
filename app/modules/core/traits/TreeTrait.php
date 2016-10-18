<?php
namespace app\modules\core\traits;

use Yii;

/**
 * Some cool methods to share amount your models
 */
trait TreeTrait
{
    public $parent_id;
    
    public function populateParent()
    {
        $this->parent_id = $this->parents()->one()->id;
    }
    
    public function canMove($data, $dir)
    {
        if ($this->depth == 0) {
            return false;
        }
        if ($dir === 'up') {
            foreach ($data as $item) {
                if ($item->depth == $this->depth && $item->lft < $this->lft) {
                    return true;
                }
            }
        } else {
            foreach ($data as $item) {
                if ($item->depth == $this->depth && $item->rgt > $this->rgt) {
                    return true;
                }
            }
        }
        return false;
    }
    
}
