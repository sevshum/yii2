<?php

use app\modules\core\helpers\App;
use app\modules\user\models\components\VerifyEmailInterface;
use app\modules\user\models\User;
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

<?= ($model instanceof VerifyEmailInterface) ? $form->field($model, 'is_email_verified')->checkbox() : '' ?>


<?= App::saveButtons($model) ?>

<?php ActiveForm::end(); ?>

