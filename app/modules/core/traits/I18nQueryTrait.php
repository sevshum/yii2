<?php
namespace app\modules\core\traits;

use Yii;

/**
 * Some cool methods to share amount your models
 */
trait I18nQueryTrait
{
    
    /**
     * @param string $lang
     * @return static
     */
    public function withLang($lang = null)
    {
        $this->with['i18ns'] = function($q) use($lang) {
            $q->useLang($lang);
        };
        return $this;
    }
    
    /**
     * @param string $language
     * @return @return static
     */
    public function useLang($language = null)
    {
        if ($language === null) {
            $language = Yii::$app->language;
        }
        $langs = [$language, Yii::$app->getModule('language')->getDefault()];
        return $this->onCondition(['lang_id' => array_unique($langs)])->indexBy(null);
    }
    
}
