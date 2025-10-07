<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Post;

class PostController extends Controller
{
    public function actionIndex() {
        $searchModel = new Post();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'Post');
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}