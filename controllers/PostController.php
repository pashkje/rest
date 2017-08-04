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
use app\models\Post;
use app\models\Access;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;

class PostController extends Controller
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
    public function auth($login, $password)
    {

        $user = User::findOne(['login' => $login]);

        if (empty($user) || empty($login) || $user->validatePassword($password, $user->password) == false) {
            throw new ServerErrorHttpException('Wrong username or password');
        } else {
            $this->user_id = $user->id;
            return $user;
        }
    }

    //Создание статьи
    public function actionCreate()
    {
        $model = new Post();
        $fields = [
            'title' => Yii::$app->request->post('title'),
            'text' => Yii::$app->request->post('text'),
            'open' => (!empty(Yii::$app->request->post('open'))) ? 1 : 0,
            'book_id' => (!empty(Yii::$app->request->post('book_id')) && intval(Yii::$app->request->post('book_id')) > 0) ? intval(Yii::$app->request->post('book_id')) : 'null',
            'user_id' => $this->user_id
        ];
        $model->load($fields, '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }

    //Открытие доступа всем
    public function actionOpen($post_id)
    {
        $model = Post::findOne(['id' => $post_id, 'user_id' => $this->user_id]);
        if (empty($model)) {
            throw new NotFoundHttpException('Post not found OR permission dined');
        }
        $model->open = 1;

        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    public function actionLink($post_id)
    {
        $post = Post::findOne(['id' => $post_id, 'user_id' => $this->user_id]);
        if (empty($post)) {
            throw new NotFoundHttpException('Post not found OR permission dined');
        }
        if (!empty(Yii::$app->request->post('book_id')) && intval(Yii::$app->request->post('book_id') > 0)){
            $post->book_id = intval(Yii::$app->request->post('book_id'));
            if ($post->save() === false && !$post->hasErrors()) {
                throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
            }

        } else {
            throw new ServerErrorHttpException('Book_id must be integer!');
        }

        return $post;
    }

    //Частичное открытие статей определенным пользователям
    public function actionPart($post_id)
    {

        $response = [];
        $post = Post::findOne(['id' => $post_id, 'user_id' => $this->user_id]);
        if (empty($post)) {
            throw new NotFoundHttpException('Post not found OR permission dined');
        }

        if (!empty(Yii::$app->request->post('user_id')) && is_array(Yii::$app->request->post('user_id'))) {

            foreach (Yii::$app->request->post('user_id') as $value) {
                //Избавимся от дубликатов
                $access = Access::findOne(['user_id' => $value, 'post_id' => $post_id]);
                if (!empty($access)) {
                    continue;
                }
                $model = new Access();

                $model->user_id = intval($value);
                $model->post_id = $post_id;


                if ($model->save() === false && !$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
                } else {
                    $response[] = $model;
                }

            }
        } else {
            throw new ServerErrorHttpException('user_id must be is_array.');
        }

        return $response;


    }

    //Просмотр чужих статей
    public function actionAnother($id)
    {

        $model = Post::getListAnother($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Posts not found.');
        }
        return $model;
    }

    //Список статей
    public function actionIndex()
    {
        $model = Post::findAll(['user_id' => $this->user_id]);
        return $model;
    }
}