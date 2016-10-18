<?php

namespace app\modules\core\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\Url;
use yii\i18n\MessageSource;
use yii\web\Application;
use yii\web\View;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function() {
            Yii::$container->set('vova07\imperavi\Widget', [
                'settings' => [
                    'buttonSource' => true,
                    'plugins' => ['table', 'video', 'imagemanager', 'filemanager', 'fullscreen'],
                    'imageUpload' => Url::toRoute(['/attachment/files/redactor-upload', 'type' => 'image']),
                    'imageManagerJson' => Url::toRoute(['/attachment/files/redactor-list', 'type' => 'image']),
                    'fileUpload' => Url::toRoute(['/attachment/files/redactor-upload', 'type' => 'file']),
                    'fileManagerJson' => Url::toRoute(['/attachment/files/redactor-list', 'type' => 'file']),
                ]
            ]);
        });
        
        /** Translate module **/
        /*
        $app->get('i18n')->getMessageSource('app')->on( 
            MessageSource::EVENT_MISSING_TRANSLATION, 
            ['\app\modules\translate\Module', 'handleMissingTranslation']
        );
        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            $app->getView()->on(View::EVENT_END_BODY, [$app->getModule('translate'), 'renderMessages']);
        });
         * 
         */
        /** End Translate module **/
    }
}
