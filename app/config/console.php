<?php
$env = require_once(__DIR__ . '/env.php');

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('@webroot', dirname(__DIR__) . '/../web');
Yii::setAlias('@web', '/');
Yii::setAlias('@root', __DIR__ . '/../../');


return yii\helpers\ArrayHelper::merge(
    require_once(__DIR__ . '/common.php'),
    [
        'basePath' => dirname(__DIR__),
        'bootstrap' => ['log'],
        'controllerNamespace' => 'app\commands',
    ], 
    $env
);
