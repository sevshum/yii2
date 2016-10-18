<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var \app\modules\user\models\User $user */

$this->title = Yii::t('app', 'User settings');
?>

<h3 class="content-title"><u>Edit Profile</u></h3>

<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes. Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>

<br><br>

<?php $form = ActiveForm::begin([
    'options' => [
        'id' => 'profile-form',
        'role' => 'form',
        'class' => 'form-horizontal'
    ],
]); ?>
    <div class="form-group">
        <?= Html::activeLabel($user, 'username', ['class' => 'col-md-3']); ?>
        <div class="col-md-7">
            <?= Html::activeTextInput($user, 'username', array('disabled' => 1, 'class' => 'form-control')); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::activeLabel($user, 'email', array('class' => 'col-md-3')); ?>
        <div class="col-md-7">
            <?= Html::activeTextInput($user, 'email', array('disabled' => 1, 'class' => 'form-control')); ?>
        </div>
    </div>

<!--    <div class="form-group">
        <div class="col-md-7 col-md-push-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            &nbsp;
            <button type="reset" class="btn btn-default">Cancel</button>
        </div>  /.col 
    </div>-->

<?php ActiveForm::end(); ?>