<?php

/* @var $assetDir string */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
        <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Posts Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?=$assetDir?>/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= Yii::$app->user->isGuest ? 'Гость' : Html::encode(Yii::$app->user->identity->username) ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [
                    [
                        'label' => 'Главная',
                        'icon' => 'home',
                        'url' => ['/site/index']
                    ],
                    ['label' => 'УПРАВЛЕНИЕ ПОСТАМИ', 'header' => true],
                    [
                        'label' => 'Посты',
                        'icon' => 'file-alt',
                        'url' => ['/post/index'],
                        'items' => [
                            ['label' => 'Список постов', 'url' => ['/post/index'], 'icon' => 'list'],
                        ]
                    ],
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
