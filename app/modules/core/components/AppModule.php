<?php
namespace app\modules\core\components;

use Yii;

class AppModule extends \yii\base\Module
{
    protected $_dependencies = [];
    
    public function init()
    {
        parent::init();
        $b = Yii::$app->getBehavior('runEnd');
        if ($b !== null) {
            $this->controllerNamespace .= '\\' . $b->getEndName();
            $this->setViewPath($this->getViewPath() . DIRECTORY_SEPARATOR . $b->getEndName());
        }
        $app = Yii::$app;
        foreach ($this->_dependencies as $id) {
            if ($app->getModule($id) === null) {
                throw new \InvalidArgumentException('The module "' . $this->id . '" depend of "' . $id . '"');
            }
        }
    }
}
