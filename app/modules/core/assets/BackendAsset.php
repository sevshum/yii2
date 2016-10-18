<?php
namespace app\modules\core\assets;

use yii\web\AssetBundle;

/**
 * @since 2.0
 */
class BackendAsset extends AssetBundle
{   
    public $css = [
        'css/font-awesome.min.css',
        'css/ionicons.min.css',
        'css/lte.css',
        'css/admin.css',
    ];
    public $js = [
        'js/main.js',
        'js/lte.js'
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
    
    public function init()
    {
        $this->sourcePath = __DIR__ . '/admin_theme';
        $this->jsOptions = ['position' => \yii\web\View::POS_HEAD];
        parent::init();
    }
}
