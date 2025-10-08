<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Детали поста';
$this->params['breadcrumbs'][] = ['label' => 'Посты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= Html::encode($model->name) ?></h3>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-arrow-left"></i> Назад', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Автор:</strong> <?= Html::encode($model->author->username) ?><br>
                        <strong>Дата создания:</strong> <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h5>Текст поста:</h5>
                        <p><?= nl2br(Html::encode($model->text)) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i> Посетители (<?= $visitorDataProvider->getTotalCount() ?>)
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <?php
                echo GridView::widget([
                    'dataProvider' => $visitorDataProvider,
                    'tableOptions' => ['class' => 'table table-striped table-valign-middle'],
                    'layout' => "{items}\n{pager}",
                    'pager' => [
                        'class' => 'yii\bootstrap4\LinkPager',
                        'options' => ['class' => 'pagination justify-content-center'],
                        'linkOptions' => ['class' => 'page-link'],
                        'listOptions' => ['class' => 'pagination'],
                    ],
                    'columns' => [
                        [
                            'attribute' => 'username',
                            'label' => 'Пользователь',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<i class="fas fa-user"></i> ' . Html::encode($model['username']);
                            }
                        ],
                        [
                            'attribute' => 'view_at',
                            'label' => 'Время просмотра',
                            'format' => 'datetime',
                        ],   
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell"></i> Подписчики (<?= $trackerDataProvider->getTotalCount() ?>)
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <?php
                echo GridView::widget([
                    'dataProvider' => $trackerDataProvider,
                    'tableOptions' => ['class' => 'table table-striped table-valign-middle'],
                    'layout' => "{items}\n{pager}",
                    'pager' => [
                        'class' => 'yii\bootstrap4\LinkPager',
                        'options' => ['class' => 'pagination justify-content-center'],
                        'linkOptions' => ['class' => 'page-link'],
                        'listOptions' => ['class' => 'pagination'],
                    ],
                    'columns' => [
                        [
                            'attribute' => 'username',
                            'label' => 'Пользователь',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<i class="fas fa-user"></i> ' . Html::encode($model['username']);
                            }
                        ],
                        [
                            'attribute' => 'track_at',
                            'label' => 'Время подписки',
                            'format' => 'datetime',
                        ]
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>