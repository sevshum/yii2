<?php

use yii\grid\GridView,
    yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\mail\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Mail templates');

$this->params['rightTitle'] = Html::a(Yii::t('app', 'Create mail template'), ['create'], ['class' => 'btn btn-success pull-right']);
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'columns' => [
                [
                    'header'    => Yii::t('app', 'Subject'),
                    'format'    => 'raw',
                    'attribute' => 'searchTitle',
                    'value'     => function($model) {
                        return h($model->getI18n('subject'));
                    },
                    'enableSorting' => false,
                ],
                'token',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width:50px'],
                ],
            ],
        ]); ?>
    </div>
</div>