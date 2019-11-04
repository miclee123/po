<?php


namespace backend\controllers;


use backend\models\Apple;
use Yii;
use yii\web\Controller;

class AppleController extends Controller
{
    public function actionIndex()
    {
        $action = Yii::$app->request->post('action');
        switch ($action) {
            case 'generate':
                Apple::generateRandomSet();
                return $this->redirect(['index'], 301);
            case 'fallToGround':
                $apple = Apple::findOne(Yii::$app->request->post('id'));
                if ($apple instanceof Apple) {
                    $apple->fallToGround();
                }
                break;
            case 'eat':
                $apple = Apple::findOne(Yii::$app->request->post('id'));
                if ($apple instanceof Apple) {
                    $apple->eat(Yii::$app->request->post('eat_percent'));
                }
                break;
        }
        $apples = Apple::find()->all();
        return $this->render('index', compact('apples'));
    }
}