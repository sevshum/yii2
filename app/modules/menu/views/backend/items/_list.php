<?php

use yii\grid\GridView;
use yii\helpers\Html;

?>

<?= GridView::widget([
    'dataProvider' => $provider,
    'layout' => "{items}",
    'options' => [
        'id' => 'menu-' . $menu->id,
    ],
    'columns' => [
        [
            'header' => 'Name', 
            'value' => function($model) {
                return str_repeat("&nbsp;&nbsp;&nbsp;", $model->depth) . $model->getI18n('name');
            }, 
            'format' => 'raw'
        ],
        'url',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{up} {down} {update} {delete}',
            'options' => array('style' => 'width:100px;'),
            'buttons' => array(
                'update' => function ($url, $model) use ($menu) {
                    return $model->depth != 0 ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/menu/items/edit', 'id' => $model->id, 'menuId' => $menu->id], [
                        'title' => 'Update',
                        'data-op' => 'modal', 
                        'data-title' => 'Edit menu', 
                        'data-skip' => 1,
                    ]) : '';
                },               
                'delete' => function ($url, $model) {
                    return $model->depth != 0 ? Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/menu/items/delete', 'id' => $model->id], [
                        'title' => 'Delete',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    ]) : '';
                },
                'up' => function ($url, $model) use ($provider) {
                    return $model->canMove($provider->getModels(), 'up') ? Html::a('<span class="glyphicon glyphicon-chevron-up"></span>', ['/menu/items/move', 'id' => $model->id, 'dir' => 'up'], [
                        'title' => 'Up',
                        'data-op' => 'ajax'
                    ]) : '';
                },
                'down' => function ($url, $model) use ($provider) {
                    return $model->canMove($provider->getModels(), 'down') ? Html::a('<span class="glyphicon glyphicon-chevron-down"></span>', ['/menu/items/move', 'id' => $model->id, 'dir' => 'down'], [
                        'title' => 'Down',
                        'data-op' => 'ajax'
                    ]) : '';
                },
            )
        ]
    ],
]);