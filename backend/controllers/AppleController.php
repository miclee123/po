<?php


namespace backend\controllers;


use backend\models\Apple;
use Yii;
use yii\web\Controller;

class AppleController extends Controller
{
    public function actionIndex()
    {
        $post = Yii::$app->request->post();
        if ($post['action'] ?? null == 'generate') {
            Apple::generateRandomSet();
            return $this->redirect(['index'], 301);
        } else {
            $apples = Apple::find()->all();
        }
        return $this->render('index', compact('apples'));
    }
}