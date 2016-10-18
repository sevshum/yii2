<?php

namespace app\modules\core\components\behaviors;

use yii\base\Behavior;


/**
 * Description of AppEndBehavior
 *
 */
class AppEndBehavior extends Behavior
{
    /**
     * @var yii\web\Application 
     */
    public $owner;
    
    private $_endName;

    public function getEndName() 
    {
        return $this->_endName;
    }

    public function runEnd($name) 
    {
        $this->_endName = $name;
        $this->_changeModulePaths();
        return $this->owner->run();
    }

    protected function _changeModulePaths() 
    {
        $this->owner->controllerNamespace .= '\\' . $this->_endName;
        $this->owner->setViewPath($this->owner->getViewPath() . DIRECTORY_SEPARATOR . $this->_endName);
    }
    
}
