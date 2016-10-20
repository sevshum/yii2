<?php
use yii\helpers\Html;
$this->title = 'Update language "' . $model->name . '"';
$this->params['breadcrumbs'][] = ['label' => 'Languages', 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', ['model' => $model]);?>