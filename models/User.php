<?php

namespace app\models;

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{


    public static function findIdentity($id)
    {
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password, $hash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $hash);
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['login', 'required'],
        ];
    }

}
