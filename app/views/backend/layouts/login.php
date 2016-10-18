<?php

use app\modules\core\assets\BackendLoginAsset;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
BackendLoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en" class="bg-black">
    <head>
        <meta charset="utf-8">
        <title><?= $this->title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?= Html::csrfMetaTags(); ?>
        <?php $this->head() ?>
    </head>

    <body class="bg-black">
        <?php $this->beginBody() ?>
        <?= $content ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
