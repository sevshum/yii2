<?php
namespace app\modules\core\components;

use Yii;
use yii\web\Request;
use yii\web\UrlManager;


class I18nUrlManager extends UrlManager
{
    public $languageParam = 'language';
    public $languageModule  = 'language';
    
    public function init()
    {
        $langs = Yii::$app->getModule($this->languageModule)->listing();

        if (count($langs) < 2) {
            return parent::init();
        }
        $strLang = implode('|', array_keys($langs));
        $newRules = [];
        foreach ($this->rules as $rule => $p) {
            $rule = ($rule[0] == '/'
                ? '/<' . $this->languageParam . ':(' . $strLang. ')>'
                : '<' . $this->languageParam . ':(' . $strLang . ')>/'
            ) . $rule;
            $newRules[$rule] = $p;
        }
        $this->rules = array_merge(
            $newRules, $this->rules
        );
        parent::init();
    }

    /**
     * Parses the user request.
     * @param Request $request the request application component
     * @return string the route (controllerID/actionID) and perhaps GET parameters in path format.
     */
    public function parseRequest($request)
    {
        $route = parent::parseRequest($request);
        $module = Yii::$app->getModule($this->languageModule);
        $module->setLanguage(
            isset($route[1][$this->languageParam]) ? $route[1][$this->languageParam] : null
        );
        return $route;
    }
    
    public function createUrl($params)
    {
        $defaultLanguage = Yii::$app->getModule($this->languageModule)->getDefault();
        
        if ($defaultLanguage !== Yii::$app->language && !isset($params[$this->languageParam])) {
            $params[$this->languageParam] = Yii::$app->language;            
        } elseif (isset($params[$this->languageParam]) && $params[$this->languageParam] == $defaultLanguage) {
            unset($params[$this->languageParam]);
        }
        return parent::createUrl($params);
    }
        
}