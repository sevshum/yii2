<?php
namespace app\modules\core\components;

use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class BackendController extends BaseController
{
    public $defaultAction = 'admin';

    public $params = [];
    public $photoUpload = false;
    public $scenario = null;
    public $with = [];
    public $redirectTo = ['admin'];

    public $modelName;
    public $modelSearch;

    protected $_model = null;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => [$this, 'checkAdminCallback']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->loadModel($this->modelName, $id)]);
    }

    public function actionCreate()
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelName;
        if ($this->scenario) {
            $model->setScenario($this->scenario);
        }
        $validation = $this->performAjaxValidation($model);
        if (!empty($validation)) {
            return $validation;
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {

            if ($model->hasAttribute('image') && $model->getBehavior('mImage') !== null) {
                $model->uploadImage(UploadedFile::getInstance($model, 'image'), 'image');
            }

            try {
                $model->save(false);
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully created.'));
                return $this->_redirect($model);
            } catch (Exception $err) {
                throw $err;
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app', 'System error: {error}.', ['error' => $err->getMessage()])
                );
            }
        }

        return $this->render('create', array_merge(['model' => $model], $this->params));
    }

    public function actionUpdate($id)
    {
        if ($this->_model === null) {
            $model = $this->loadModel($this->modelName, $id);
        } else {
            $model = $this->_model;
        }
        if ($this->scenario) {
            $model->setScenario($this->scenario);
        }

        $validation = $this->performAjaxValidation($model);
        if (!empty($validation)) {
            return $validation;
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {

            if ($model->hasAttribute('image') && $model->getBehavior('mImage') !== null) {
                $this->photoUpload = $model->uploadImage(UploadedFile::getInstance($model, 'image'), 'image');
            }

            try {
                $model->save(false);
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully updated.'));
                return $this->_redirect($model);
            } catch (Exception $err) {
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app', 'System error: {error}.', ['error' => $err->getMessage()])
                );
            }
        }
        return $this->render('update', array_merge(['model' => $model], $this->params));
    }

    public function actionDelete($id)
    {
        $this->loadModel($this->modelName, $id)->delete();
        return $this->redirect(['admin']);
    }

    public function actionIndex()
    {
        $class = $this->modelName;
        $dataProvider = new ActiveDataProvider(['query' => $class::find()]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionAdmin()
    {
        $searchModel = new $this->modelSearch;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render(
            'admin',
            array_merge(['searchModel' => $searchModel, 'dataProvider' => $dataProvider], $this->params)
        );
    }

    public function actionMove($id, $dir)
    {
        $model = $this->loadModel($this->modelName, $id);

        if ($dir == 'up' || $dir == 'down') {
            return $model->moveEntry($dir);
        }
        return $model;
    }

    protected function _redirect($model)
    {
        if (isset($_POST['continue'])) {
            return $this->redirect(['update', 'id' => $model->id]);
        } elseif (isset($_POST['back']) && !empty($_REQUEST['redirect'])) {
            return $this->redirect($_REQUEST['redirect']);
        } elseif ($this->redirectTo) {
            return $this->redirect($this->redirectTo);
        } else {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }
}

