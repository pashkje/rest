<?php
/**
 * Created by PhpStorm.
 * User: bitrix
 * Date: 01.08.2017
 * Time: 21:37
 */

namespace app\controllers;


use Yii;
use app\models\User;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

class UserController extends Controller
{
    //Создание юзера
    public function actionCreate()
    {
        if (User::findOne(['login' => Yii::$app->request->post('login')])){
            throw new ServerErrorHttpException('User with such login already exists');
        }
        $model = new User();
        $model->login  = Yii::$app->request->post('login');
        $model->password  = $model->setPassword(Yii::$app->request->post('password'));

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }

}