<?php

use app\modules\core\helpers\App;
use app\modules\admin\models\User;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model User */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(['enableClientValidation' => true]); ?>

<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'tempPassword')->passwordInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'status')->dropDownList($model::statuses()) ?>

<?= App::saveButtons($model) ?>

<?php ActiveForm::end(); ?>

