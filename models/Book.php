<?php
/**
 * Created by PhpStorm.
 * User: bitrix
 * Date: 02.08.2017
 * Time: 1:32
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Post;

class Book extends ActiveRecord
{

    public static function tableName()
    {
        return 'books';
    }

    public function rules()
    {
        return [
            ['title', 'required'],
            ['user_id', 'required'],
        ];
    }

    public function getPosts($model)
    {
        //Id залогиненого юзера
        $user_id = \Yii::$app->user->identity->id;

        //если книгу смотрит создатель то выводим все статьи, если другие пользователи то выводим статьи,
        //которые данный пользователь может видить (все публичные + «секретные» к которым дали доступ)
        if ($user_id == $model->user_id) {
            $result = Post::findAll(['book_id' => $model->id]);
        } else {
            $subQuery = Access::find()->select(['post_id'])->where(['user_id' => $model->user_id])->asArray()->all();
            $where_in = [];
            if (!empty($subQuery)) {
                foreach ($subQuery as $key => $value) {
                    $where_in[] = $value['post_id'];
                }
            }

            $result = Post::find()
                ->where(['open' => 1])
                ->orWhere(['in', 'id', $where_in])
                ->orWhere(['user_id' => $model->user_id])
                ->andWhere(['book_id' => $model->id])
                ->all();
        }


        return $result;
    }


    public function fields()
    {
        return [
            'title',
            'id',
            'user_id',
            'posts' => function ($model) {
                return $this->getPosts($model);
            }
        ];
    }

}