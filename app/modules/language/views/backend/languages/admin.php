<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\language\models\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Languages';
$this->params['rightTitle'] = Html::a('Create Language', ['create'], ['class' => 'btn btn-success pull-right']);
?>

<?= $this->render('_list', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]) ?>