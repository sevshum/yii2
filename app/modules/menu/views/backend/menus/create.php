<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = 'Create Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('_form', ['model' => $model]) ?>