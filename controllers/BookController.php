<?php
/**
 * Created by PhpStorm.
 * User: bitrix
 * Date: 02.08.2017
 * Time: 1:25
 */

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Book;
use app\models\Post;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class BookController extends Controller
{
    public $user_id;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'auth' => [$this, 'auth']
        ];
        return $behaviors;
    }

    //Проверка авторизации
    public function auth($login,$password)
    {
        $user = User::findOne(['login' => $login]);

        if (empty($user) || empty($login) || $user->validatePassword($password, $user->password) == false) {
            throw new ServerErrorHttpException('Wrong username or password');
        } else {
            $this->user_id = $user->id;
            return $user;
        }
    }

    //Создание книги
    public function actionCreate()
    {
        $model = new Book();
        $fields = [
            'title' => Yii::$app->request->post('title'),
            'user_id' => $this->user_id
        ];
        $model->load($fields, '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }

    //Список книг
    public function actionIndex($book_id = null)
    {
        if (intval($book_id) > 0){
            $books = Book::findOne(['id' => $book_id]);
        }  else {
            $books = Book::findAll(['user_id' => $this->user_id]);
        }


        if (empty($books)) {
            throw new NotFoundHttpException('Books not found.');
        }

        return $books;
    }

    //Спиоск чужих книг
    public function actionAnother($id){
        $books = Book::findAll(['user_id' => $id]);

        if (empty($books)) {
            throw new NotFoundHttpException('Books not found.');
        }

        return $books;
    }
}