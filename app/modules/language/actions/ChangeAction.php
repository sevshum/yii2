<?php
namespace app\modules\language\actions;

use Yii;
use yii\base\Action;

class ChangeAction extends Action
{
    public function run($lang)
    {
        $request = Yii::$app->getRequest();
        $urlManager = Yii::$app->getUrlManager();
        $referrer = strtr($request->getReferrer(), [$request->getHostInfo() => '']);
        $module = Yii::$app->getModule('language');
        if ($lang !== Yii::$app->language &&
            array_key_exists($lang, $module->listing())
        ) {
            $default = $lang === $module->getDefault();
            $referrer = strtr($referrer, [
                '/' . Yii::$app->language => $default ? ($urlManager->showScriptName ? '' : '/') : ('/' . $lang)
            ]);
            $referrer = rtrim($referrer, '/');
            if (stripos($referrer, '/' . $lang) === false && !$default) {
                if ($urlManager->showScriptName) {
                    $url = $request->getScriptUrl();
                    $referrer = strtr($referrer, [$url => $url . '/' . $lang]);
                } else {
                    $referrer = '/' . $lang . $referrer;
                }
            }
            $module->setLanguage($lang);
        }
        return $this->controller->redirect('/' . ltrim($referrer, '/'));
    }
}
