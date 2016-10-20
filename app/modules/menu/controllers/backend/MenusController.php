<?php
namespace app\modules\menu\controllers\backend;

use app\modules\core\components\BackendController;

class MenusController extends BackendController
{
    public $modelName = '\app\modules\menu\models\Menu';
    public $modelSearch = '\app\modules\menu\models\Menu';

    public function actionCreate()
    {
        $this->params['code'] = isset($_GET['code']) ? $_GET['code'] : '';
        unset($_GET['code']);
        return parent::actionCreate();
    }
}
