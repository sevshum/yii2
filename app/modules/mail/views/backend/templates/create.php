<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\mail\models\Template */
$this->title = Yii::t('app', 'Create mail template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app' , 'Mail templates'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>