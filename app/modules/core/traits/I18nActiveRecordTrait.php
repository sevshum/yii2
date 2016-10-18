<?php
namespace app\modules\core\traits;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Some cool methods to share amount your models
 */
trait I18nActiveRecordTrait
{    
    public function setI18n($data, $language = null, $forceSave = false)
    {
        if ($language === null) {
            $language = Yii::$app->getModule('language')->getDefault();
        }
        $i18ns = ArrayHelper::index($this->i18ns, 'lang_id');
        $i18nModel = isset($i18ns[$language]) ? $i18ns[$language] : new $this->i18nModel;
        foreach ($data as $key => $value) {
            $i18nModel->$key = $value;
        }
        $i18nModel->lang_id = $language;
        
        $i18ns[$language] = $i18nModel;
        if ($forceSave) {
            $i18nModel->save(false);
        }        
        $this->populateRelation('i18ns', $i18ns);
    }

    /**
     * Return content 
     * @param string $attribute
     * @param boolean $info
     * @param string $language
     * @return mixed if info = true return [text, incorrect flag, model]
     */
    public function getI18n($attribute, $info = false, $language = null)
    {
        $language = $language === null ? Yii::$app->language : $language;
        if ($this->isRelationPopulated('i18ns')) {
            $relations = $this->i18ns;
        } else {
            $relations = $this->getI18ns()->useLang($language)->all();
//            $this->populateRelation('i18ns', $relations);
        }
        $relations = ArrayHelper::index($relations, 'lang_id');
        
        if (count($relations) === 0) {
            return $info ? [null, true, null] : null;
        } elseif (isset($relations[$language]) && $relations[$language]->$attribute !== '') { // exists current language
            return $info ? [$relations[$language]->$attribute, false, $relations[$language]] : $relations[$language]->$attribute;
        } elseif (isset($relations[$defaultLang = Yii::$app->getModule('language')->getDefault()]) && $relations[$defaultLang]->$attribute !== '') { // exists default language           
            return $info ? [$relations[$defaultLang]->$attribute, true, $relations[$defaultLang]] : $relations[$defaultLang]->$attribute;
        }
        return $info ? [null, true, null] : null;
    }

    /**
     * Clear dirty attributes for default language
     * @param array $relations
     */
    public function clearDirtyAttributes($relations) 
    {
        $default = Yii::$app->getModule('language')->getDefault();
        if (isset($relations[$default])) {
            $relations[$default]->dirtyParams = null;
        }
    }

    public function setTranslateAttrs($i18ns)
    {
        $this->setNestedRelation('i18ns', $i18ns);
        $this->clearDirtyAttributes($this->i18ns);
    }

    public function getTranslateAttrs()
    {
        return [];
    }

    /**
     * @return ActiveQuery 
     */
    public function getI18ns()
    {
        $class = $this->i18nModel;
        return $this->hasMany($class::className(), ['parent_id' => 'id'])->indexBy('lang_id');
    }
    
    public function afterDelete()
    {
        parent::afterDelete();
        $class = $this->i18nModel;
        $class::deleteAll(['parent_id' => $this->id]);
    }
}

