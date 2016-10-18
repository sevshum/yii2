<?php
namespace app\modules\core\assets;

use yii\web\AssetBundle;

/**
 * @since 2.0
 */
class BackendLoginAsset extends AssetBundle
{   
    public $css = [
        'css/font-awesome.min.css',
        'css/ionicons.min.css',
        'css/lte.css'
    ];
    
    public $depends = [
        'yii\web\YiiAsset',        
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
