<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Post;
use yii\web\NotFoundHttpException;

class PostController extends Controller
{
    public $layout = 'main-admin';
    
    public function actionIndex() 
    {
        $searchModel = new Post();
        $dataProvider = $searchModel->getListDataProvider(Yii::$app->request->queryParams, 'Post');
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = Post::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $visitorDataProvider = $model->getVisitorDataProvider();
        $trackerDataProvider = $model->getTrackerDataProvider();

        return $this->render('view', [
            'model' => $model,
            'visitorDataProvider' => $visitorDataProvider,
            'trackerDataProvider' => $trackerDataProvider,
        ]);
    }
}