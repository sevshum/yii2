<?php

use kartik\widgets\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Html;
$prefix = 'Template[translateattrs][' . $lang['id'] . ']'; 

/* @var $form ActiveForm */
?>


<?= Html::hiddenInput($prefix . '[lang_id]', $lang['id'])?>
<?= $form->field($model, 'subject')->textInput(['id' => "subject-{$lang['id']}", 'name' => $prefix . '[subject]']); ?>        
<?= $form->field($model, 'content')->widget(Widget::className(), [
    'options' => [
        'id' => "content-{$lang['id']}", 
        'name' => $prefix . '[content]',
    ]
]); ?>
<?= $form->field($model, 'content_plain')->textarea(['id' => "content_plain-{$lang['id']}", 'name' => $prefix . '[content_plain]']); ?>