<?php
/* @var $this app\modules\menu\controllers\backend\MenusController */
/* @var $model app\modules\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>
<br />
<?php $form = yii\widgets\ActiveForm::begin(array(
    'id' => 'menu-' . $model->id,
    
)); ?>
    <?= $form->field($model, 'code'); ?>
    
    <?= $form->field($model, 'name'); ?>

<?= app\modules\core\helpers\App::saveButtons($model); ?>

<?php yii\widgets\ActiveForm::end(); ?>