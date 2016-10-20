<?php
namespace app\modules\language\controllers\backend;

use app\modules\core\components\BackendController;
use Yii;

class LanguagesController extends BackendController
{
    public $modelName = '\app\modules\language\models\Language';
    public $modelSearch = '\app\modules\language\models\LanguageSearch';
    
    public function actionMove($id, $dir)
    {
        $model = parent::actionMove($id, $dir);
        
        $searchModel = new $this->modelSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->renderJson(array(
            'html' => $this->renderPartial('_list', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]),
            'success' => true,
            'target' => '#languages'
        ));
    }
}