<?php

require(__DIR__ . '/../vendor/autoload.php');
$config = require(__DIR__ . '/../app/config/frontend.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
Yii::setAlias('@root', __DIR__ . '/../');
(new yii\web\Application($config))->run();
