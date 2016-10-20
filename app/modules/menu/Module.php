<?php
namespace app\modules\menu;

use app\modules\core\components\AppModule;
use app\modules\menu\models\Menu;
use yii\db\Exception;

class Module extends AppModule
{
    public $controllerNamespace = 'app\modules\menu\controllers';
    
    public function getMenu($code, $throwException = true) 
    {
        $menu = $this->getByCode($code, $throwException);
        if ($menu === null) {
            return [];
        }
        return $menu->getMenu();
    }
    
    public function getByCode($code, $throwException = true)
    {
        $menu = Menu::getByCode($code);
        if ($menu === null && $throwException) {
            throw new Exception('The menu "' . $code . '" was not found.');                
        }
        return $menu;
    }
}
