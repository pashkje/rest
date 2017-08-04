<?php
/**
 * Created by PhpStorm.
 * User: bitrix
 * Date: 02.08.2017
 * Time: 20:46
 */

namespace app\models;


use Yii;
use yii\db\ActiveRecord;


class Access extends ActiveRecord
{
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['post_id', 'required'],
        ];
    }

}