<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

function h($text)
{
    return Html::encode($text);
}

function a($text, $url = '#', $options = [])
{
    return Html::a($text, $url, $options);
}

function param($key, $default = null)
{
    return ArrayHelper::getValue(Yii::$app->params, $key, $default);
}

/**
 * Return setting
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function s($key, $default = null)
{
    return Yii::$app->getModule('settings')->getParam($key, $default);
}

function t($category, $message, $params = [], $language = null)
{
    return Yii::t($category, $message, $params, $language);
}

function block($code, $data = [], $lang = null)
{
    return Yii::$app->getModule('contentblock')->render($code, $data, $lang);
}

function categoryList($code, $forMenu = true, $throwException = false)
{
    return Yii::$app->getModule('category')->getListCategory($code, $forMenu, $throwException);
}

function categoryDropDown($code, $throwException = false, $prefix = '  ')
{
    return Yii::$app->getModule('category')->render($code, $throwException, $prefix);
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        if ($objects) {
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . DIRECTORY_SEPARATOR . $object) == "dir")
                        rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

function editLink($model, $class, $params = [])
{
    if ($model === null) {
        Html::a('', $class::getCreateUrl($params), ['class' => 'glyphicon glyphicon-plus']);
    }
    return Html::a('', $model->getEditLink(), ['class' => 'glyphicon glyphicon-pencil']);
}

function toBackend()
{
    return param('backend-link') . Url::to($url);
}
