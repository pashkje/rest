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

class Post extends ActiveRecord
{

    public static function tableName()
    {
        return 'posts';
    }


    public static function getListAnother($user_id)
    {
        $subQuery = Access::find()->select(['post_id'])->where(['user_id' => $user_id])->asArray()->all();
        $where_in = [];
        if (!empty($subQuery)) {
            foreach ($subQuery as $key => $value) {
                $where_in[] = $value['post_id'];
            }
        }


        $result = parent::find()
            ->where(['open' => 1])
            ->orWhere(['in', 'id', $where_in])
            ->andWhere(['user_id' => $user_id])
            ->all();
        ;
        return $result;
    }

    public function rules()
    {
        return [
            ['title', 'required'],
            ['text', 'required'],
            ['user_id', 'required'],
        ];
    }

}