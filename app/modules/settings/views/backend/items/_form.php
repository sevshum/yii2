<?php

use app\modules\core\helpers\App,
    app\modules\settings\models\Item,
    yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Item */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>

<?= $form->field($model, 'group') ?>
<?= $form->field($model, 'key') ?>
<?= $form->field($model, 'value') ?>
<?= $form->field($model, 'order') ?>

<?= App::saveButtons($model) ?>

<?php ActiveForm::end(); ?>