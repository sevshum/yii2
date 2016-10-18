<?php
require_once __DIR__ . '/../modules/core/helpers/common.php';
return [
    'id' => 'cms',
    'name' => 'CMS',
    'sourceLanguage' => 'en',
    'vendorPath' => __DIR__ . '/../../vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'modules' => [
        'admin' => ['class' => 'app\modules\admin\Module'],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        'language' => ['class' => 'app\modules\language\Module'],
        'page' => ['class' => 'app\modules\page\Module'],
        'gallery' => ['class' => 'app\modules\gallery\Module'],
        'mail' => ['class' => 'app\modules\mail\Module'],
        'translate' => ['class' => 'app\modules\translate\Module'],
        'blog' => ['class' => 'app\modules\blog\Module'],
        'category' => ['class' => 'app\modules\category\Module'],
        'menu' => ['class' => 'app\modules\menu\Module'],
        'contentblock' => ['class' => 'app\modules\contentblock\Module'],
        'comment' => ['class' => 'app\modules\comment\Module'],
        'settings' => ['class' => 'app\modules\settings\Module'],
        'glossary' => ['class' => 'app\modules\glossary\Module'],

        'image' => ['class' => 'app\modules\image\Module'],
        'attachment' => ['class' => 'app\modules\attachment\Module'],
        'formbuilder' => ['class' => 'app\modules\formbuilder\Module'],

        'search' => [
            'class' => 'app\modules\search\Module',
            'searchModules' => [
                'page' => 'app\modules\page\models\Page',
                'blog' => 'app\modules\blog\models\Post'
            ]
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'consoleRunner' => [
            'class' => 'app\modules\core\components\ConsoleRunner',
            'rootPath' => __DIR__ . '/../..'
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => ['class' => 'yii\authclient\clients\GoogleOAuth'],
                'facebook' => ['class' => 'yii\authclient\clients\Facebook'],
                'vkontakte' => ['class' => 'app\modules\user\components\auth\VKontakte'],
            ],
        ]
    ],
    'params' => [
        'backend-link' => '/backend.php'
    ]
];
