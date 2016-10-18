<?php

use app\modules\user\models\forms\ChangePasswordForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model ChangePasswordForm */
/* @var $form ActiveForm  */
$this->title = Yii::t('app', 'Change password');
?>

<h3 class="content-title"><u><?= Yii::t('app', 'Change password') ?></u></h3>
<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes.</p>

<br><br>

<?php $form = ActiveForm::begin([
        'options' => array(
            'id' => 'change-password-form',
            'role' => 'form',
            'class' => 'form account-form'
        ),
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3'],
            'template' => "{label}\n<div class=\"col-md-7\">{input}\n{error}</div>"
        ]
    ]); ?>
    <?= $form->field($model, 'actualPassword')->passwordInput(['autofocus' => 1]) ?>
    <hr />
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'repeatPassword')->passwordInput() ?>
    <div class="form-group">
        <div class="col-md-7 col-md-push-3">
            <button type="submit" class="btn btn-primary"><?= Yii::t('app', 'Save changes') ?></button>
        </div>
    </div>

<?php ActiveForm::end() ?>

