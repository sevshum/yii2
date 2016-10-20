<?php
namespace app\modules\mail\controllers\backend;

class TemplatesController extends \app\modules\core\components\BackendController
{
    public $modelName = '\app\modules\mail\models\Template';
    public $modelSearch = '\app\modules\mail\models\TemplateSearch';
}