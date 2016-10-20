<?php 
$currentLang = Yii::$app->language;
?>
<ul class="<?= $this->context->ulClass ?>">
    <li>
        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">
            <?= $languages[$currentLang]['name'] ?>
            <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu">
        <?php foreach ($languages as $key => $lang) { ?>
            <li<?php if ($key == $currentLang) {?> class="active"<?php } ?>><?= yii\helpers\Html::a($lang['name'], ['/language/languages/change', 'lang' => $lang['id']])?></li>
        <?php } ?>
        </ul>
    </li>
</ul>
    