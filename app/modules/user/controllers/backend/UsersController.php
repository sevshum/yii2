<?php

namespace app\modules\user\controllers\backend;

class UsersController extends \app\modules\core\components\BackendController
{
    public $modelName = '\app\modules\user\models\User';
    public $modelSearch = '\app\modules\user\models\UserSearch';
    
    public function actionCreate()
    {
        $this->scenario = 'insert';
        return parent::actionCreate();
    }
    
    public function actionUpdate($id)
    {
        $this->scenario = 'update';
        return parent::actionUpdate($id);
    }
    
}
