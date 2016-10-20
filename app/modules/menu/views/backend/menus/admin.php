<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\Menu */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menu';
$this->params['rightTitle'] = Html::a('Create Menu', ['create'], ['class' => 'btn btn-success pull-right']);
if ($menuId = Yii::$app->getRequest()->get('menu_id')) {
    $this->registerJs('$("#menu-list").find(".related-link[data-id=\'' . $menuId . '\']").trigger("click");');
}
?>
<div class="box">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'options' => ['id' => 'menu-list'],
            'columns' => [
                [
                    'attribute'     => 'name',
                    'enableSorting' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a($model->name, '#', [
                            'data-url' => Url::toRoute(['/menu/items/index', 'id' => $model->id]), 
                            'data-id' => $model->id,
                            'class' => 'related-link'
                        ]);
                    }
                ],
                [
                    'attribute'     => 'code',
                    'enableSorting' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width:60px;']
                ],
            ],
        ]); ?>
    </div>
</div>
