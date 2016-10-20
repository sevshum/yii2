<?php

namespace app\modules\language;

use app\modules\core\components\AppModule;
use app\modules\language\models\Language;
use Yii;
use yii\web\Cookie;

class Module extends AppModule
{
    public $controllerNamespace = 'app\modules\language\controllers';

    public function getDefault()
    {
        return Language::getDefault();
    }
    
    public function listing($onlyActive = true)
    {
        return Language::listing($onlyActive);
    }
    
    public function setLanguage($lang = null)
    {
        if ($lang === null) {
            $lang = $this->getLanguage();            
        }
        $app = Yii::$app;
        $app->getSession()->set('language', $lang);
        $cookie = new Cookie([
            'name' => 'language', 
            'value' => $lang,
            'expire' => time()+60*60*24*180
        ]);
        $app->getResponse()->getCookies()->add($cookie);
        $app->language = $lang;
        $listing = $this->listing();
        if (!empty($listing[$lang]['locale'])) {
            $app->getFormatter()->locale = $listing[$lang]['locale'];
        }
        return $lang;
    }
    
    public function getLanguage()
    {
        $key = 'language';
        $app = Yii::$app;
        $request = $app->getRequest();
        if (isset($_REQUEST[$key]) && !empty($_REQUEST[$key])) {
            $lang = $_REQUEST[$key];
        } elseif ($app->getSession()->has($key)) {
            $lang = $app->getSession()->get($key);
        } elseif (isset($request->cookies['language'])) {
            $lang = $request->cookies['language']->value;
        } elseif ($request->getPreferredLanguage()) {
            $lang = $request->getPreferredLanguage();
        } else {
            $lang = $this->getDefault();
        }
        
        if (!array_key_exists($lang, $this->listing())) {
            if (strpos($lang, '_') !== false) {
                $lang = explode('_', $lang)[0];
                if (!array_key_exists($lang, $this->listing())) {
                    $lang = $this->getDefault();                    
                }
            } else {
                $lang = $this->getDefault();
            }
        }
        return $lang;
    }
    
}
