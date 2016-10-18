<?php
namespace app\modules\core\components\widgets\fileuploader;


class UploaderAsset extends \yii\web\AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->js = ['fileuploader.js'];
        $this->css = ['fileuploader.css'];
        parent::init();
    }
}
