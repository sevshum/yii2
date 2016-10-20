<?php
namespace app\modules\settings\controllers\backend;

use app\modules\core\components\BackendController;
use app\modules\settings\models\SettingForm;
use Yii;

class ItemsController extends BackendController
{
    public $modelName = '\app\modules\settings\models\Item';
    public $modelSearch = '\app\modules\settings\models\Item';
    
    public function actionEdit()
    {
        $model = new SettingForm();
        if ($model->load($_POST) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully updated.'));
            return $this->refresh();
        }
        return $this->render('edit', ['model' => $model]);
    }
}