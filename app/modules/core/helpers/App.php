<?php
namespace app\modules\core\helpers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class App
{
    public static function uid() 
    {
        return strtr(microtime(true), array('.' => ''));
    }
    
    public static function isFront()
    {
        static $isFront;
        if ($isFront === null) {
            $isFront = Yii::$app->get('admin', false) !== null;
        }
        return $isFront;
    }
    
    /**
     * @param ActiveRecord $model
     */
    public static function attachMetaParams($model)
    {
        $view = Yii::$app->controller->getView();
        if ($model->hasMethod('getI18n')) {
            if ($title = $model->getI18n('meta_title')) {
                $view->title = $title;
            } else {
                $view->title = $model->getI18n('title');
            }
            $view->params['metaKeywords'] = $model->getI18n('meta_keywords');
            $view->params['metaDescription'] = $model->getI18n('meta_description');
        } else {
            if ($model->hasAttribute('meta_title')) {
                $view->title = $model->getAttribute('meta_title');
            } 
            if ($model->hasAttribute('meta_keywords')) {
                $view->params['metaKeywords'] = $model->getAttribute('meta_keywords');
            }
            if ($model->hasAttribute('meta_description')) {
                $view->params['metaDescription'] = $model->getAttribute('meta_description');
            }            
        }
    }
    
    public static function createMenuUrl($url)
    {
        if (stripos($url, 'http') === 0 || strpos($url, '/') !== 0) {
            return $url;
        }
        $default = Yii::$app->getModule('language')->getDefault();
        $lang = Yii::$app->language;
        if ($default != $lang) {
            $url = '/' . $lang . $url;
        }
        return $url;
    }
    
    public static function detectMenuActive($url, $condition = null)
    {
        $currentUrl = Yii::$app->getRequest()->getUrl();
        return $url === '/' ? $currentUrl === '/' : stripos($currentUrl, $url) === 0;
    }
    
    public static function saveButtons($model)
    {
        $isNew = $model instanceof ActiveRecord ? $model->getIsNewRecord() : false;
        $buttons = [
            Html::submitButton($isNew ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => 'btn btn-primary']),
            Html::submitButton($isNew ? Yii::t('app', 'Create and continue') : Yii::t('app', 'Save and continue'), ['class' => 'btn btn-success', 'name' => 'continue']),
        ];
        if (!empty($_REQUEST['redirect'])) {
            $buttons[] = Html::submitButton($isNew ? Yii::t('app', 'Create and back') : Yii::t('app', 'Save and back'), ['name' => 'back', 'class' => 'btn btn-info']);
        }
        return Html::tag('div', implode("\n", $buttons), ['class' => 'form-group form-actions']);
    }
    
    public static function printOption($data, $delim = ', ')
    {
        return implode($delim, array_map(function($model) {
            return $model->getI18n('name');
        }, $data));
    }
    
    public static function createForest($rows, $fieldId, $fieldParent)
    {
        $children = []; // children of each ID
        $ids = [];

        // Collect who are children of whom.
        foreach ($rows as $i => $r) {
            $row = & $rows[$i];
            $id = $row->$fieldId;

            if ($id === null) {
                // Rows without an ID are totally invalid and makes the result tree to
                // be empty (because PARENT_ID = null means "a root of the tree"). So
                // skip them totally.
                continue;
            }

            $pid = $row->$fieldParent;
            if ($id == $pid) {
                $pid = null;
            }
            $children[$pid][$id] = & $row;
            if (!isset($children[$id]))
                $children[$id] = [];
            $row->children = & $children[$id];
            $ids[$id] = true;
        }

        // Root elements are elements with non-found PIDs.
        $forest = [];
        foreach ($rows as $i => $r) {
            $row = & $rows[$i];
            $id = $row->$fieldId;
            $pid = $row->$fieldParent;
            if ($pid == $id) {
                $pid = null;
            }
            if (!isset($ids[$pid])) {
                $forest[$row->$fieldId] = & $row;
            }
        }

        return $forest;
    }
}