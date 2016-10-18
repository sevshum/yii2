<?php

require(__DIR__ . '/../vendor/autoload.php');
$config = require(__DIR__ . '/../app/config/backend.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
(new yii\web\Application($config))->runEnd('backend');
