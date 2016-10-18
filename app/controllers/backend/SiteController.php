<?php
namespace app\controllers\backend;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['suggest'],
                'rules' => [
                    [
                        'actions' => ['suggest'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
        ];
    }

    public function actionSuggest()
    {
        if (isset($_GET['term']) && ($q = trim($_GET['term'])) !== '') {
            $params = ['lang' => isset($_GET['lang']) ? $_GET['lang'] : null];
            $params['editUrl'] = 1;
            return Json::encode(Yii::$app->getModule('search')->suggest($q, $params));
        }
    }
}

