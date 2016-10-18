<?php
namespace app\modules\user\controllers;

use app\modules\user\models\components\VerifyEmailInterface;
use app\modules\user\models\forms\LoginForm;
use app\modules\user\models\Provider;
use app\modules\user\models\SocialService;
use app\modules\user\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;


class SessionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
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
    
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onSuccessCallback'],
            ],
        ];
    }
    
    public function beforeAction($action)
    {
        if ($action->id === 'auth') {
            $client = Yii::$app->getRequest()->get('authclient');
            Yii::$app->get('authClientCollection')->getClient($client)->setReturnUrl(
                Url::toRoute(['/user/session/auth', 
                    'authclient' => $client, 
                    'language' => Yii::$app->getModule('language')->getDefault()
                ], true)
            );
        }
        return parent::beforeAction($action);
    }

    
    public function onSuccessCallback($client)
    {
        $service = new SocialService([
            'user' => Yii::$app->getUser()->getIdentity(), 
            'client' => $client,
            'context' => $this->action
        ]);
        return $service->run();
    }


    public function actionCreate()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack(['/user/users/profile']);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionDelete()
    {
        Yii::$app->getUser()->logout(false);
        return $this->goHome();
    }
}
