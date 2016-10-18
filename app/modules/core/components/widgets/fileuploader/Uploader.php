<?php
namespace app\modules\core\components\widgets\fileuploader;

use app\modules\core\components\widgets\fileuploader\UploaderAsset;
use yii\helpers\Json;
use yii\jui\Widget;
use yii\web\JsExpression;


class Uploader extends Widget
{
    public $options = [];
    public $selector = 'fileupload';

    public function run()
    {
        $view = $this->getView();
        UploaderAsset::register($view);
        $this->options['element'] = new JsExpression('document.getElementById("' . $this->selector . '")');
        $view->registerJs('var u' . $this->selector . ' = new qq.FileUploader(' . Json::encode($this->options) . ');');
    }
}