<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \app\models\forms\LoginForm $model
 */
$this->title = 'Sign in';
?>
<div class="form-box">
    <div class="header">Please Sign In</div>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => [
            'role' => 'form'
        ]                    
    ]); ?>
        <div class="body bg-gray">
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
        </div>
        <div class="footer">
            <?= Html::submitButton('Sign in', ['class' => 'btn btn-lg btn-success btn-block', 'name' => 'login-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
    