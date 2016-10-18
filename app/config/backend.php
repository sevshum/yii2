<?php
$env = require_once(__DIR__ . '/env.php');

return yii\helpers\ArrayHelper::merge(
    require_once(__DIR__ . '/web.php'),
    [
        'language' => 'en',
        'as runEnd' => [
            'class' => 'app\modules\core\components\behaviors\AppEndBehavior'
        ],
        'components' => [
            'urlManager' => [
                'showScriptName' => true,
                'rules' => [
                    '/' => '/user/users',
                    '<_m:\w+>/<_c:\w+>/<id:\d+>' => '<_m>/<_c>/view',
                    '<_m:\w+>/<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_m>/<_c>/<_a>',
                    '<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',

                    '<_c:\w+>/<id:\d+>' => '<_c>/view',
                    '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
                    '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
                ]
            ],
            'user' => [
                'identityClass' => 'app\modules\admin\models\User',
                'loginUrl' => ['/admin/session/create'],
                'idParam' => '__bId',
                'authTimeoutParam' => '__bExpire',
                'returnUrlParam' => '__bReturnUrl',
                'identityCookie' => ['name' => '_bIdentity', 'httpOnly' => true],
            ]
        ]
        
    ],
    $env
);