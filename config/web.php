<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '8LKKZISTAhVymavm8PaQB8NaCkotoF3t',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                //Создание пользователя
                'POST users' => 'user/create',
                //Список своих книг
                'GET books' => 'book/index',
                //Просмотр определенной книги
                'GET books/<book_id:\d+>' => 'book/index',
                //Просмотр чужой книги
                'GET books/user/<id:\d+>' => 'book/another',
                //Создание книги
                'POST books' => 'book/create',
                //Список всоих статей
                'GET posts' => 'post/index',
                //Просмотр чужих статей
                'GET posts/user/<id:\d+>' => 'post/another',
                //Создание статьи
                'POST posts' => 'post/create',
                //Открытие полного доступа своей определенной статьи
                'PUT posts/open/<post_id:\d+>' => 'post/open',
                //Открытие частичного доступа своей определенной статьи
                'POST posts/open-part/<post_id:\d+>' => 'post/part',
                //Привязка статьи к книге
                'PUT posts/link-book/<post_id:\d+>' => 'post/link',

            ],
        ],
    ],
    'params' => $params,
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
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
