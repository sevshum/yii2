<?php


/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = 'Update "' . h($model->name) . '" Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['admin']];
$this->params['breadcrumbs'][] = $model->name;
?>

<?= $this->render('_form', ['model' => $model]) ?>

