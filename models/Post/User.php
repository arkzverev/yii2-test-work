<?php

namespace app\models\Post;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'authKey', 'accessToken'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['accessToken'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'authKey' => 'Ключ авторизации',
            'accessToken' => 'Токен доступа',
        ];
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(\app\models\Post::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[Visitors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisitors()
    {
        return $this->hasMany(\app\models\Post\Visitor::class, ['id_visitor' => 'id']);
    }

    /**
     * Gets query for [[Trackers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrackers()
    {
        return $this->hasMany(\app\models\Post\Tracker::class, ['id_user' => 'id']);
    }
}