<?php

use app\modules\core\helpers\App;
use app\modules\mail\models\Template;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Template */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
]); ?>
<?= $this->render('_i18ns', ['model' => $model, 'form' => $form]) ?>
<?= $form->field($model, 'token') ?>
<?= $form->field($model, 'from') ?>
<?= $form->field($model, 'from_name') ?>
<?= $form->field($model, 'bcc') ?>


<?= App::saveButtons($model) ?>

<?php ActiveForm::end(); ?>
