<?php

use app\modules\core\assets\BackendAsset;
use app\modules\core\components\widgets\Alert;
use app\modules\core\components\widgets\SideMenu;
use cebe\gravatar\Gravatar;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$user = Yii::$app->getUser()->getIdentity();

/**
 * @var View $this
 * @var string $content
 */
BackendAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?= Html::csrfMetaTags(); ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-black">

<?php $this->beginBody() ?>
    
    <header class="header">
        <a href="/" class="logo">
            <!-- Add the class icon to your logo image or logo icon to add the margining -->
            <?= s('app.project_name', 'AdminTool') ?>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-right">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav navbar-nav'
                    ],
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => '<i class="glyphicon glyphicon-user"></i> <span>' . $user->username . ' </span>',
                            'url' => '#',
                            'options' => ['class' => 'user'],
                            'items' => [
                                [
                                    'label' => '<i class="fa fa-gear fa-fw"></i>' . Yii::t('app', 'Profile'), 
                                    'url' => ['/user/users/update', 'id' => $user->id],
                                ],
                                '<li class="divider"></li>',
                                [
                                    'label' => '<i class="fa fa-sign-out fa-fw"></i> ' . Yii::t('app', 'Logout'),
                                    'url' => ['/admin/session/delete'],
                                    'linkOptions' => ['data-method' => 'post'],
                                ]
                            ],
                        ],
                    ]
                ]); ?>
            </div>
        </nav>
    </header>
    
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <aside class="left-side sidebar-offcanvas">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <?php echo Gravatar::widget([
                            'email' => $user->email,
                            'defaultImage' => 'identicon',
                            'options' => [
                                'alt' => $user->username,
                                'class' => 'img-circle',
                            ],
                            'size' => 45
                        ]); ?>
                    </div>
                    <div class="pull-left info">
                        <p><?= Yii::t('app', 'Hello, {name}', ['name' => Html::encode($user->username)]) ?></p>

                        <a><?= $user->email ?></a>
                    </div>
                </div>
                <!-- search form -->
                <form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <?= AutoComplete::widget([
                            'options' => ['class' => 'form-control', 'placeholder' => 'Search ...'],
                            'name' => 'q', 
                            'clientOptions' => [
                                'source' => Url::toRoute(['/site/suggest']),
                                'minLength' => 3,
                                'select' => new JsExpression('function(event, ui) {
                                    if (ui.item) {
                                        document.location = ui.item.url;
                                    }
                                }')

                            ]
                        ])?>
                        <span class="input-group-btn">
                            <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <!-- /.search form -->
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?= SideMenu::widget([
                    'options' => ['class' => 'sidebar-menu'],
                    
                    'items' => [
                        [
                            'label' => '<span>' . Yii::t('app', 'Dashboard') . '</span>',
                            'icon' => 'fa fa-dashboard',
                            'url' => ['/site/index']
                        ],
                        [
                            'label' => '<span>' . Yii::t('app', 'Content') . '</span>',
                            'icon' => 'fa fa-globe fa-fw',
                            'url' => '#',
                            'items' => [
                                ['label' => Yii::t('app', 'Users'), 'url' => ['/user/users/admin']],
                                ['label' => Yii::t('app', 'Pages'), 'url' => ['/page/pages/admin']],
                                ['label' => Yii::t('app', 'Blog'), 'url' => ['/blog/posts/admin']],
                                ['label' => Yii::t('app', 'Galleries'), 'url' => ['/gallery/galleries/admin']],
                                ['label' => Yii::t('app', 'Categories'), 'url' => ['/category/categories/admin']],
                                ['label' => Yii::t('app', 'Menus'), 'url' => ['/menu/menus/admin']],
                                ['label' => Yii::t('app', 'Content blocks'), 'url' => ['/contentblock/contents/admin']],
                                ['label' => Yii::t('app', 'Glossary'), 'url' => ['/glossary/terms/admin']],
                                [
                                    'label' => Yii::t('app', 'Comments ({count})', ['count' => Yii::$app->getModule('comment')->getNewCount()]), 
                                    'url' => ['/comment/comments/admin']
                                ],
                            ]
                        ],
                        [
                            'label' => '<span>' . Yii::t('app', 'System') . '</span>',
                            'icon' => 'fa fa-wrench fa-fw',
                            'url' => '#',
                            'items' => [
                                ['label' => Yii::t('app', 'Admins'), 'url' => ['/admin/users/admin']],
                                ['label' => Yii::t('app', 'Languages'), 'url' => ['/language/languages/admin']],
                                ['label' => Yii::t('app', 'Translations'), 'url' => ['/translate/languages/admin']],
                                ['label' => Yii::t('app', 'Mail templates'), 'url' => ['/mail/templates/admin']],
                                ['label' => Yii::t('app', 'Settings'), 'url' => ['/settings/items/admin']],
                            ]
                        ],
                    ]
                ])?>
                
            </section>
            <!-- /.sidebar -->
        </aside>
        
        
        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="right-side">
            <!-- Content Header (Page header) -->
            
            <div class="pad no-print"><?= Alert::widget() ?></div>
            
            <section class="content-header">
                <h1>
                    <?= Html::encode($this->title) ?>
                    <?= isset($this->params['rightTitle']) ? $this->params['rightTitle'] : ''?>
                </h1>
                
                <?php 
                    if (isset($this->params['breadcrumbs'])) {
                        echo Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]);
                    }
                ?>
            </section>
            <!-- Main content -->
            <section class="content" id="main-content">
                <div class="row">
                    <div class="col-xs-12">                        
                        <?= $content ?>
                    </div>
                </div>
            </section><!-- /.content -->
        </aside><!-- /.right-side -->
    </div>

<?= $this->render('partials/_modal') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
