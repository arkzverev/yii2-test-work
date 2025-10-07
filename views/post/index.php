<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Post;

$this->title = 'Post';
$this->params['breadcrumbs'][] = ['label' => 'Post'];

?>

<div class="post-index">
    
<?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
            ],   
        ],
    ]);
?>