<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$prefix = 'MenuItem[translateattrs][' . $lang['id'] . ']'; 

?>

<?= Html::hiddenInput($prefix . '[lang_id]', $lang['id'])?>
<?= $form->field($model, 'name')->widget(AutoComplete::className(), [
    'options' => ['id' => "name-{$lang['id']}", 'name' => $prefix . '[name]', 'class' => 'form-control'],
    'clientOptions' => [
        'source' => Url::toRoute(['/menu/items/suggest', 'lang' => $lang['id']]),
        'minLength' => 3,
        'select' => new JsExpression('function(event, ui) {
            if (ui.item) {
                $("#menuitem-url").val(ui.item.url);
            }
        }')
        
    ]
]); ?>