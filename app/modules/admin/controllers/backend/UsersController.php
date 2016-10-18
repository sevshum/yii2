<?php

namespace app\modules\admin\controllers\backend;

class UsersController extends \app\modules\core\components\BackendController
{
    public $modelName = '\app\modules\admin\models\User';
    public $modelSearch = '\app\modules\admin\models\UserSearch';
    
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
