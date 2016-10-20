<?php
namespace app\modules\settings;

use app\modules\settings\models\Item;

class Module extends \app\modules\core\components\AppModule
{
    protected static $_settings = [];
    
    public function getParam($key, $default = null)
    {
        $c = explode('.', $key, 2);
        if (!isset(static::$_settings[$c[0]])) {
            static::$_settings[$c[0]] = Item::getByGroup($c[0]);
        }
        if (isset($c[1])) {
            return isset(static::$_settings[$c[0]][$c[1]]) ? static::$_settings[$c[0]][$c[1]] : $default;
        }
        
        return static::$_settings[$c[0]];
    }
    
    public function setParam($key, $value)
    {
        $c = explode('.', $key, 2);
        return Item::set([
            'group' => $c[0],
            'key' => $c[1],
            'value' => $value,
            'order' => null,
        ]);
    }
}
