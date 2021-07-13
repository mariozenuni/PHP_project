<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'language'=>'it',
    'timeZone'=> 'Europe/Rome',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'bPFBtBlUa5DgFKWeFj2i8zc-soTB5mwo',

        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => $db,

        'assetManager'=>[

            'class'=>'yii\web\AssetManager',
            'appendTimestamp'=> true,
            'bundles'=>[
                'yii\web\jqueryAsset' =>[
                    'sourcePath'=> null,
                    'js'=>[
                        'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'
                    ],
                    'jsOption'=>['position'=>\yii\web\View::POS_HEAD],
                ],

                //in this way we can avoid conflicts between yii default css and js
                   /* 'yii\bootstrap\BootstrapAsset'=>[
                        'css'=>[],
                        'js'=>[]
                    ]*/
            ],

         'linkAssets'=>false,


        ],



        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],






    ],

    'params' => $params,

    'modules' => [
        'gridview' =>  [
                             'class' => '\kartik\grid\Module',
        ],
  ],

];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
