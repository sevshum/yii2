<?php
namespace app\modules\core\components;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 *
 * Has some useful methods for your Controllers
 */
class BaseController extends Controller
{
    public static $permissions = [];
    public $skipAjaxRequest = false;

    /**
     * @param type $rule
     * @param \yii\base\InlineAction $action
     */
    public function matchCallback($rule, $action)
    {
        $c = $action->controller;
        $route = ($c->module ? "/{$c->module->id}" : '') . "/{$c->id}/{$action->id}";
        if (in_array($route, static::$permissions)) {
            return Yii::$app->getUser()->can($route);
        }
        return false;
    }
    
    public function checkAdminCallback($action)
    {
        $user = $this->getUser();
        return !$user->getIsGuest() && $user->idParam === '__bId';
    }

    public function getPermission($actionId)
    {
        return ($this->module ? "/{$this->module->id}" : '') . "/{$this->id}/{$actionId}";
    }

    /**
     * @return \yii\web\User
     */
    public function getUser()
    {
        static $user;
        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        return $user;
    }

    /**
     * Loads the requested data model.
     * @param string the model class name
     * @param integer the model ID
     * @param array additional search criteria
     * @param boolean whether to throw exception if the model is not found. Defaults to true.
     * @return ActiveRecord the model instance.
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function loadModel($class, $id, $criteria = [], $exceptionOnNull = true)
    {
        if (empty($criteria)) {
            $model = $class::findOne($id);
        } else {
            /** @var ActiveQuery $finder */
            $criteria[$class::primaryKey()] = $id;
            $model = $class::findOne($criteria);
        }
        if (isset($model)) {
            return $model;
        } else if ($exceptionOnNull) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function loadModelBySlug($class, $slug, $criteria = [], $exceptionOnNull = true)
    {
        if (empty($criteria)) {
            $model = $class::findOne(['slug' => $slug]);
        } else {
            $criteria['slug'] = $slug;
            $model = $class::findOne($criteria);
        }

        if (isset($model)) {
            return $model;
        } else if ($exceptionOnNull) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Performs the AJAX validation.
     * @param \yii\base\Model the model to be validated
     */
    protected function performAjaxValidation($models, $formId = null)
    {
        if (!is_array($models)) {
            $models = [$models];
        }
        $request = Yii::$app->getRequest();
        if ($request->isAjax && $request->post('ajax') == $formId) {
            $request->format = Response::FORMAT_JSON;
            return ActiveForm::validateMultiple($models);
        }
        return false;
    }

    /**
     * Outputs (echo) json representation of $data, prints html on debug mode.
     * NOTE: json_encode exists in PHP > 5.2, so it's safe to use it directly without checking
     * @param array $data the data (PHP array) to be encoded into json array
     */
    public function renderJson($data)
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return $data;
    }

    public function render($view, $params = [])
    {
        $request = Yii::$app->getRequest();

        if (!$this->skipAjaxRequest && $request->getIsAjax()) {
            $am = Yii::$app->getAssetManager();
            $am->bundles = array_merge($am->bundles, [
                'yii\web\JqueryAsset' => false,
                'yii\bootstrap\BootstrapAsset' => false,
                'yii\bootstrap\BootstrapPluginAsset' => false,
            ]);
            return $this->renderAjax($view, $params);
        }
        return parent::render($view, $params);
    }
    
}

