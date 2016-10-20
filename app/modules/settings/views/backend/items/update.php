<?php
use yii\helpers\Html;
$this->title = Yii::t('app', 'Update "{title}"', ['title' => $model->group . '.' . $model->key]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Settings'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', ['model' => $model]);?>