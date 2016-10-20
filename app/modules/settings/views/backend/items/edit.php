<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\SettingForm */

$this->title = Yii::t('app', 'Edit all settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Settings'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => true,
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]); ?>

            <?php $model->renderFields($form, $model) ?>

            <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>