<?php

use yii\base\ActionEvent;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'language' => 'ru',
    'on beforeAction' => function (ActionEvent $event) {
        if (Yii::$app->user->isGuest && !in_array($event->action->id, ['login', 'error'])) {
            return Yii::$app->response->redirect(['/site/login']);
        }
        return true;
    },
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => ''
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '/',
            'rules' => [
                '' => 'site/index',
                '<_a:(login)>' => 'site/<_a>',
                '<_c:[a-z\-_]+>' => '<_c>/index',
                '<c_>/<a_>' => '<c_>/<a_>'
            ],
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'appendTimestamp' => true,
            'linkAssets' => true
        ]
    ],
    'params' => $params,
];
