<?php

namespace app\models\Post;

use yii\db\ActiveRecord;
use app\models\User;
use app\models\Post;

class Tracker extends ActiveRecord
{
    public $username;

    public static function tableName()
    {
        return 'posts_track';
    }

    public function rules()
    {
        return [
            [['id_post', 'id_user'], 'required'],
            [['id_post', 'id_user'], 'integer'],
            [['track_at'], 'safe'],
            [['username'], 'string'],
            [['id_post', 'id_user'], 'unique', 'targetAttribute' => ['id_post', 'id_user']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_post' => 'ID Post',
            'id_user' => 'ID User',
            'track_at' => 'Track At',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'id_post']);
    }
}