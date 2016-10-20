<?php

use yii\grid\GridView,
    yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\language\models\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$maxOrder = $searchModel->getMaxOrder();
?>
<div class="box">
    <div class="box-body">
        
        <?= GridView::widget([
            'id' => 'languages',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-stripped'],    
            'columns' => [
                'id',
                'name',
                [
                    'attribute' => 'is_default',
                    'value' => function($model) {
                        return $model->is_default ? 'Yes' : 'No';
                    },
                    'filter' => false
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{up} {down} {update} {delete}',
                    'options' => ['style' => 'width:100px'],
                    'buttons' => [
                        'delete' => function ($url, $model) {

                            return $model->is_default ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => 'Delete',
                                'data-confirm' => 'Are you sure you want to delete this item?',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]);
                        },
                        'up' => function ($url, $model) {
                            return $model->order == 1 ? '' : Html::a('<span class="glyphicon glyphicon-chevron-up"></span>', ['move', 'id' => $model->id, 'dir' => 'up'], [
                                'title' => 'Up',
                                'data-op' => 'ajax',
                                'data-pjax' => '0',
                            ]);
                        },
                        'down' => function ($url, $model) use ($maxOrder) {
                            return $model->order == $maxOrder ? '' : Html::a('<span class="glyphicon glyphicon-chevron-down"></span>', ['move', 'id' => $model->id, 'dir' => 'down'], [
                                'title' => 'Down',
                                'data-op' => 'ajax',
                                'data-pjax' => '0',
                            ]);
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>