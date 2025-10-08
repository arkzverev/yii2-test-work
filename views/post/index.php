<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Post;

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Посты</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-striped table-valign-middle'],
                    'layout' => "{items}\n{pager}",
                    'pager' => [
                        'class' => 'yii\bootstrap4\LinkPager',
                        'options' => ['class' => 'pagination justify-content-center'],
                        'linkOptions' => ['class' => 'page-link'],
                        'listOptions' => ['class' => 'pagination'],
                    ],
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'name',
                        ], 
                        [
                            'attribute' => 'text',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'username',
                        ],
                        [
                            'attribute' => 'created_at',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'countVisitors',
                        ],
                        [
                            'attribute' => 'countTrackers',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => "{view}",
                        ]
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>