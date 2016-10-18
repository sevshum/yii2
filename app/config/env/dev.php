<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

return [
    'bootstrap' => ['debug', 'gii'],
    'modules' => [
        'debug' => 'yii\debug\Module',
        'gii' => 'yii\gii\Module'
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=database',
            'username' => 'root',
            'password' => '...',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            'viewPath' => '@app/mail',
                        
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => ['clientId' => '...', 'clientSecret' => '...'],
                'facebook' => ['clientId' => '...', 'clientSecret' => '...'],
                'vkontakte' => ['clientId' => '...', 'clientSecret' => '...',],
            ],
        ]
    ],
    'params' => [
        'backend-link' => '/backend.php'
    ],
];

