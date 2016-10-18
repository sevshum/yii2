<?php

$config = [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', '\app\modules\core\components\Bootstrap'],
    'components' => [
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => 'V995NovTuoK3Dw'
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'long'
        ],
        'image' => [
            'class'        => '\maxlapko\components\ImageProcessor',
            'imagePath'    => '@webroot/files/img', //save images to this path    
            'imageUrl'     => '@web/files/img',
            'fileMode'     => 0777,
            'imageHandler' => [
                'class' => '\maxlapko\components\handler\ImageHandler',
                'driver' => '\maxlapko\components\handler\drivers\ImageMagic', 
            ],
            'forceProcess' => true, // process image when we call getImageUrl
            'afterUploadProcess' => [
                'condition' => ['maxWidth' => 1280, 'maxHeight' => 1280], // optional
                'actions'   => [
                    'resize' => ['width' => 1280, 'height' => 1280]
                ] 
            ],
            'presets' => [
                'image_preview' => ['thumb' => ['width'  => 100, 'height' => 100]],
                'image_media_preview' => ['adaptiveThumb' => ['width'  => 175, 'height' => 175]],
            ],
        ],
        'assetManager' => [
            'linkAssets' => true
        ],
    ]
];
return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/common.php'), $config);
