<?php

namespace app\models\Post;

use yii\db\ActiveRecord;
use app\models\User;
use app\models\Post;

class Visitor extends ActiveRecord
{
    public $username;

    public static function tableName()
    {
        return 'posts_visitors';
    }

    public function rules()
    {
        return [
            [['id_post', 'id_visitor'], 'required'],
            [['id_post', 'id_visitor'], 'integer'],
            [['view_at'], 'string'],
            [['username'], 'string'],
            [['id_post', 'id_visitor'], 'unique', 'targetAttribute' => ['id_post', 'id_visitor']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_post' => 'ID Post',
            'id_visitor' => 'ID Visitor',
            'view_at' => 'View At',
        ];
    }

    public function getVisitor()
    {
        return $this->hasOne(User::class, ['id' => 'id_visitor']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'id_post']);
    }
}