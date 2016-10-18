<?php
$env = require(__DIR__ . '/env.php');
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/web.php'),
    [
        'components' => [
            'admin' => [
                'class' => '\yii\web\User',
                'identityClass' => 'app\modules\user\models\User',
                'idParam' => '__bId',
                'authTimeoutParam' => '__bExpire',
                'returnUrlParam' => '__bReturnUrl',
                'identityCookie' => ['name' => '_bIdentity', 'httpOnly' => true],
            ],
            'urlManager' => [
                'class' => 'app\modules\core\components\I18nUrlManager',
                'rules' => [
                    '/' => '/site/index',                    
                    '/page/<slug:[-\w]+>' => 'page/pages/show',
                    '/blog/category/<code:[-\w]+>' => 'blog/posts/index',
                    '/blog/<slug:[-\w]+>' => 'blog/posts/show',
                    '/blog' => 'blog/posts/index',
                    '/glossary' => '/glossary/terms/index',
                
                    '<_m:\w+>/<_c:\w+>/<id:\d+>' => '<_m>/<_c>/view',
                    '<_m:\w+>/<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_m>/<_c>/<_a>',
                    '<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',

                    '<_c:\w+>/<id:\d+>' => '<_c>/view',
                    '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
                    '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
                ]
            ],
        ]
    ],
    $env
);