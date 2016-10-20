<?php
namespace app\modules\language\widgets;

class SelectorWidget extends \yii\base\Widget
{
    public $ulClass = "";
    
    public function run()
    {
        $module = \Yii::$app->getModule('language');
        return $this->render('selector', ['languages' => $module->listing(), 'default' => $module->getDefault()]);
    }

}