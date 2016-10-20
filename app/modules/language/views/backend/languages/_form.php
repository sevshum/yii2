<?php

use app\modules\core\helpers\App,
    app\modules\user\models\Language,
    yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Language */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>

<?= $form->field($model, 'id')->textInput(['maxlength' => 2]) ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'locale')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'is_default')->checkbox() ?>
<?= $form->field($model, 'is_active')->checkbox() ?>


<?= App::saveButtons($model) ?>

<?php ActiveForm::end(); ?>