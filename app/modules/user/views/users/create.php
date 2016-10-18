<?php

use app\modules\user\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model User */
/* @var $form ActiveForm  */
$this->title = Yii::t('app', 'Sign up');
?>
<div class="login_container">              
    <?php $form = ActiveForm::begin([
        'options' => array(
            'id' => 'register_form',
            'role' => 'form',
        ),
    ]); ?>
        <h1 class="login_heading"><?= Yii::t('app', 'Register') ?> / <span><?= Html::a(Yii::t('app', 'Login'), ['/user/session/create'])?></span></h1>
        
        <?= $form->field($model, 'username')->textInput(['autofocus' => 1, 'class' => 'form-control input-lg']) ?>
        <?= $form->field($model, 'email')->textInput(['class' => 'form-control input-lg']) ?>
        <?= $form->field($model, 'tempPassword')->passwordInput(['class' => 'form-control input-lg', 'autocomplete' => 'off']) ?>

        <div class="submit_section text-center">
            <?= Html::submitButton(Yii::t('app', 'Sign up'), ['class' => 'btn btn-lg btn-success', 'name' => 'login-button']) ?>
        </div>
        <?php if ($collection = Yii::$app->get('authClientCollection', false)) : ?>
            <hr />
            <p class="text-center">
                <span><?= Yii::t('app', 'Sign up with:') ?></span>
            </p>
            <?= \yii\authclient\widgets\AuthChoice::widget([
                'clients' => $collection->getClients(),
                'baseAuthUrl' => ['/user/session/auth'],
                'options' => ['class' => 'text-center']
            ]) ?>
        <?php endif ?>
    <?php ActiveForm::end(); ?>
</div>

