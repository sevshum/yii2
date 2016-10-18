<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $name
 * @var string $message
 * @var Exception $exception
 */

$this->title = $name;
?>
<div class="error-page">
    <h2 class="headline text-info"> <?= $exception->statusCode ?></h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops!</h3>
        <p>
            <?= nl2br(Html::encode($message)) ?>
        </p>
    </div><!-- /.error-content -->
</div>
