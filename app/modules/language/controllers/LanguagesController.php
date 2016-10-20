<?php
namespace app\modules\language\controllers;

use app\modules\core\components\BaseController;

class LanguagesController extends BaseController
{
    public function actions()
    {
        return [
            'change' => [
                'class' => 'app\modules\language\actions\ChangeAction',
            ]
        ];
    }
}