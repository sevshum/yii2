<?php

use app\modules\user\models\forms\PasswordResetRequestForm;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model PasswordResetRequestForm */
/* @var $form ActiveForm  */
$this->title = Yii::t('app', 'Reset password');
?>
<div class="login_container">    
    <?php $form = ActiveForm::begin([
        'options' => array(
            'id' => 'forgot-form',
            'role' => 'form',
            'class' => 'form account-form'
        ),
    ]); ?>
    <h1 class="login_heading"><?= Yii::t('app', 'Reset password') ?> / <span><a href="<?= Url::toRoute(['/user/session/create'])?>"><?= Yii::t('app', 'Login') ?></a></span></h1>
    <?= $form->field($model, 'email')->textInput(['autofocus' => 1, 'class' => 'form-control input-lg']); ?>


    <div class="submit_section text-center">
        <button type="submit" class="btn btn-secondary btn-lg">
            <?= Yii::t('app', 'Reset password') ?>
        </button>
    </div>

    <?php ActiveForm::end(); ?>
</div>                
