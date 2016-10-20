<?php

use app\modules\settings\models\Item;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel Item */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Settings');
$this->params['rightTitle'] = 
    Html::a(Yii::t('app', 'Create setting item'), ['create'], ['class' => 'btn btn-primary pull-right']) . "\n" .
    Html::a(Yii::t('app', 'Edit all settings'), ['edit'], ['class' => 'btn btn-success pull-right', 'style' => 'margin-right:10px;']);
?>
<div class="box">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                [
                    'attribute' => 'group',
                    'filter' => Item::getGroups()
                ],
                'key',
                'value',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'options' => ['style' => 'width:40px'],
                ]
            ],
        ]); ?>
    </div>
</div>