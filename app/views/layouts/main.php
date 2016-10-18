<?php

use app\assets\AppAsset;
use app\modules\core\components\widgets\Alert;
use app\modules\language\widgets\SelectorWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View $this
 * @var string $content
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <?= Html::csrfMetaTags(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => s('app.name'),
                'brandUrl' => ['/site/index'],
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            
            
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => Yii::$app->getModule('menu')->getMenu('main_menu', false)
            ]);
            
            
            $items = [];
            
            if (Yii::$app->user->isGuest) {
                $items[] = ['label' => Yii::t('app', 'Sign up'), 'url' => ['/user/users/create']];
                $items[] = ['label' => Yii::t('app', 'Sign in'), 'url' => ['/user/session/create']];
            } else {
                $items[] = [
                    'label' => Yii::t('app', 'Logout') . ' (' . Yii::$app->getUser()->getIdentity()->username . ')',
                    'url' => ['/user/session/delete'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }
            
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $items
            ]);
            echo SelectorWidget::widget([
                'ulClass' => 'nav navbar-nav navbar-right'
            ]);
            
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; <?= s('app.name') ?> <?= date('Y') ?></p>
        </div>
    </footer>
    <?= $this->render('partials/_modal') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
