<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=database',
            'username' => 'root',
            'password' => '...',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 7200,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'viewPath' => '@app/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mandrillapp.com',
                'username' => '...',
                'password' => '...',
                'port' => '587',
    //                'encryption' => 'tls',
            ],            
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
