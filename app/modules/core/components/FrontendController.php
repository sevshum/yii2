<?php
namespace app\modules\core\components;

use app\modules\core\helpers\App;
use Yii;
use yii\data\ActiveDataProvider;

class FrontendController extends BaseController
{
    public $modelName;

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->loadModel($this->modelName, $id)]);
    }

    public function actionShow($slug)
    {
        $model = $this->loadModelBySlug($this->modelName, $slug);
        App::attachMetaParams($model);

        return Yii::$app->getRequest()->getIsAjax() ?
            $this->renderAjax('view', ['model' => $model]) :
            $this->render('view', ['model' => $model]);
    }

    public function actionIndex()
    {
        $class = $this->modelName;
        $dataProvider = new ActiveDataProvider(
            ['query' => $class::find()]
        );
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}

