<?php

use app\modules\user\models\forms\ChangePasswordForm;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model ChangePasswordForm */
/* @var $form ActiveForm  */
$this->title = Yii::t('app', 'Change password');
?>
<div class="login_container">
    <?php $form = ActiveForm::begin([
        'options' => array(
            'id' => 'reset-password-form',
            'role' => 'form',
            'class' => 'form account-form'
        ),
    ]); ?>
    <h1 class="login_heading"><?= Yii::t('app', 'Change password') ?> / <span><a href="<?= Url::toRoute(['/user/session/create'])?>"><?= Yii::t('app', 'Login') ?></a></span></h1>
    <?= $form->field($model, 'password')->passwordInput(['autofocus' => 1, 'class' => 'form-control input-lg']) ?>
    <?= $form->field($model, 'repeatPassword')->passwordInput(['class' => 'form-control input-lg']) ?>

    <div class="submit_section text-center">
        <button type="submit" class="btn btn-secondary btn-lg">
            <?= Yii::t('app', 'Change password') ?>
        </button>
    </div>
        
    <?php ActiveForm::end(); ?>
</div>

