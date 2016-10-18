<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\models\forms\LoginForm $model
 */

$this->title = Yii::t('app', 'Sign in');
?>
<div class="login_container">

    <?php $form = ActiveForm::begin([
        'options' => [
            'role' => 'form',
            'id' => 'login_form',
        ]                    
    ]); ?>
        <h1 class="login_heading"><?= Yii::t('app', 'Login') ?> / <span><?= Html::a(Yii::t('app', 'Register'), ['/user/users/create'])?></span></h1>
        <?= $form->field($model, 'username')->textInput(['class' => 'form-control input-lg']) ?>
        <?= $form
            ->field($model, 'password', ['template' => "{label}\n{input}\n{error}\n{hint}"])
            ->passwordInput(['class' => 'form-control input-lg', 'autocomplete' => 'off' ])->hint(
                Html::a(Yii::t('app', 'Forgot password?'), ['/user/users/forgotpassword'])
            ) 
        ?>
            
        <?php // $form->field($model, 'rememberMe')->checkbox() ?>
        
        <div class="submit_section text-center">
            <?= Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-lg btn-success', 'name' => 'login-button']) ?>
        </div>
        <?php if ($collection = Yii::$app->get('authClientCollection', false)) : ?>
            <hr />
            <p class="text-center">
                <span><?= Yii::t('app', 'Sign in with:') ?></span>
            </p>
            <?= yii\authclient\widgets\AuthChoice::widget([
                'clients' => $collection->getClients(),
                'baseAuthUrl' => ['/user/session/auth'],
                'options' => ['class' => 'text-center']
            ]) ?>
        <?php endif ?>
    <?php ActiveForm::end(); ?>
</div>
    