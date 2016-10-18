<?php

use app\modules\admin\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Admins');
$this->params['rightTitle'] = Html::a(Yii::t('app', 'Create admin'), ['create'], ['class' => 'btn btn-success pull-right']);

?>
<div class="box">
    <div class="box-body">
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table'],
            'rowOptions' => function($model) {
                $class = '';
                if ($model->status === User::STATUS_BLOCKED) {
                    $class = 'warning';
                } elseif ($model->status === User::STATUS_DELETED) {
                    $class = 'danger';
                }
                return ['class' => $class];
            },
            'columns' => [
                'id',
                'username',
                'email:email',
//                [
//                    'attribute' => 'role',
//                    'value' => function($model) {
//                        return User::roles($model->role);
//                    },
//                    'filter' => User::roles()
//                ],
                [
                    'attribute' => 'status',
                    'value' => function($model) {
                        return User::statuses($model->status);
                    },
                    'filter' => User::statuses()
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width:50px']
                ],
            ],
        ]); ?>
    </div>
</div>