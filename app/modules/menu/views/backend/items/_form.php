<?php

use app\modules\menu\models\MenuItem,
    kartik\widgets\ActiveForm,
    yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $item MenuItem */
/* @var $form ActiveForm */
$formId = 'menu-item-' . $item->id;
?>

<?php $form = ActiveForm::begin([
    'options' => [
        'id' => $formId,
        'enctype' => 'multipart/form-data'
    ],
    'action' => ['/menu/items/edit', 'menuId' => $item->menu_id, 'id' => $item->id],
]); ?>

<?= $this->render('_i18ns', array('model' => $item, 'form' => $form)) ?>
<?= $form->field($item, 'parent_id')->dropDownList($parents, ['encodeSpaces' => true]) ?>
<?= $form->field($item, 'url') ?>

<?php // $form->field($item, 'url') ?>

<?= Html::submitButton($item->getIsNewRecord() ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
<?php $this->registerJs('    
    $("#' . $formId . '").submit(function() {
        var form = $(this),
            submitButton = form.find(":submit");
        if (submitButton.hasClass("disabled")) {
            return false;
        }
        form.data("dataType", "json");
        submitButton.attr("disabled", "disabled").addClass("disabled");
        CMS.submitForm(form, function(err, rsp) {
            if (err) {
                console.log(err);
                submitButton.removeAttr("disabled").removeClass("disabled");
                return;
            } else if (rsp.target && rsp.html) {
                $(rsp.target).html($(rsp.target, $("<div>" + rsp.html + "</div>")).html());
            }
            if (rsp.success) {
                CMS.popup.el.modal("hide");
            }
        });
        return false;
    }); 

');

    