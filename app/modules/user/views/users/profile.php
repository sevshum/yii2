<?php

use app\modules\core\components\widgets\Alert;
use cebe\gravatar\Gravatar;
use kartik\widgets\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;

/* @var app\modules\user\models\Profile $profile */

$profile = $user->profile;
$prefix = 'User[profileattrs]';
if (!isset($tab) || !in_array($tab, ['user', 'profile', 'social'], true)) {
    $tab = $user->hasErrors() || !$profile->hasErrors() ? 'user' : 'profile';
}
$this->registerJs('$("#profile-form").find(\'a[data-toggle="tab"]\').on("shown.bs.tab", function(e) {
    $("#tab-input").val(e.target.toString().split("#")[1]);
});');

$collection = Yii::$app->get('authClientCollection', false);
?>

<div class="page_bar clearfix">
    <div class="row">
        <div class="col-md-12">
            <div class="media">
                <?php if ($profile->avatar) : ?>
                    <img class="img-thumbnail pull-left" src="<?= $profile->getImageUrl('avatar', 'avatar_60x60') ?>" alt="">
                <?php else : ?>
                    <?php echo Gravatar::widget([
                        'email' => $user->email,
                        'options' => [
                            'class' => 'img-thumbnail pull-left'
                        ],
                        'size' => 60
                    ]); ?>
                <?php endif ?>
                <div class="media-body">
                    <h1 class="page_title"><span class="text-muted"><?= Yii::t('app', 'User') ?>:</span> <?= h($user->username) ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page_content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?= Alert::widget(['alertTypes' => ['user_success' => 'alert-success', 'user_error' => 'alert-danger']])?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="user_profile">
                            <?php $form = ActiveForm::begin([
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'options' => [
                                    'enctype' => 'multipart/form-data',
                                    'id' => 'profile-form'
                                ],
                                'fieldConfig' => [
                                    'labelOptions' => [
                                        'class' => 'col-md-2'
                                    ],
                                    'template' => "{label}\n<div class=\"col-md-10\">{input}</div>\n{hint}\n{error}"
                                ]
                            ]) ?>
                                <?= Html::hiddenInput('tab', $tab, ['id' => 'tab-input']) ?>
                                <div class="tabbable tabs-right">
                                    <ul class="nav nav-tabs">
                                        <li class="<?= $tab === 'user' ? 'active' : '' ?>"><a data-toggle="tab" href="#user" class="tab-default"><?= Yii::t('app', 'General Info') ?></a></li>
                                        <li class="<?= $tab === 'profile' ? 'active' : '' ?>"><a data-toggle="tab" href="#profile" class="tab-default"><?= Yii::t('app', 'Profile Info') ?></a></li>
                                        <?php if ($collection !== null) : ?>
                                        <li class="<?= $tab === 'social' ? 'active' : '' ?>"><a data-toggle="tab" href="#social" class="tab-default"><?= Yii::t('app', 'Social Info') ?></a></li>
                                        <?php endif ?>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="user" class="tab-pane <?= $tab === 'user' ? 'active' : '' ?>">
                                            <?= $form->field($user, 'email') ?>
                                            <?= $form->field($user, 'username') ?>
                                            <?= $form->field($user, 'tempPassword')->passwordInput() ?>                                            
                                        </div>
                                        <div id="profile" class="tab-pane <?= $tab === 'profile' ? 'active' : '' ?>">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <div class="heading_b">Address</div>
                                                </div>
                                            </div>
                                            
                                            <?= $form->field($profile, 'country')->textInput(['name' => $prefix . '[country]']) ?>
                                            <?= $form->field($profile, 'city')->textInput(['name' => $prefix . '[city]']) ?>
                                            <?= $form->field($profile, 'address')->textInput(['name' => $prefix . '[address]']) ?>
                                            
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <div class="heading_b">Other</div>
                                                </div>
                                            </div>
                                            <?= $form->field($profile, 'image')->fileInput(['name' => $prefix . '[image]']) ?>
                                            <?= $form->field($profile, 'description')->textarea(['name' => $prefix . '[description]']) ?>
                                        </div>
                                        <?php if ($collection !== null) : ?>
                                        <div id="social" class="tab-pane <?= $tab === 'social' ? 'active' : '' ?>">
                                            <?= AuthChoice::widget(['baseAuthUrl' => ['/user/session/auth'], 'clients' => $collection->getClients()]) ?>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($user->providers as $p) : ?>
                                                    <tr>
                                                        <td><span class="auth-icon <?= $p->provider ?>"></span></td>
                                                        <td><?= h($p->username) ?></td>
                                                    </tr>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php endif ?>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <button class="btn btn-success"><i class="fa fa-save"></i> <?= Yii::t('app', 'Save profile') ?></button>
                                    </div>
                                </div>
                            <?php ActiveForm::end() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>