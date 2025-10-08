<?php

namespace app\models\Post;

use yii\db\ActiveRecord;
use app\models\Post\Visitor;
use app\models\Post\Tracker;
use app\models\Post;

class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'authKey', 'email'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'authKey' => 'Ключ авторизации',
        ];
    }

    public function getPosts()
    {
        return $this->hasMany(Post::class, ['created_by' => 'id']);
    }

    public function getVisitors()
    {
        return $this->hasMany(Visitor::class, ['id_visitor' => 'id']);
    }

    public function getTrackers()
    {
        return $this->hasMany(Tracker::class, ['id_user' => 'id']);
    }
}