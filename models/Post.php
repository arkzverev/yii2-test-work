<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;

class Post extends ActiveRecord
{
    public $countVisitors;
    public $countTrackers;
    
    public static function tableName()
    {
        return 'posts';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['text'], 'string'],
            [['fields'], 'safe'],
            [['created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Назва посту',
            'text' => 'Текст посту',
            'fields' => 'Додаткові поля',
            'created_by' => 'Автор',
            'created_at' => 'Дата створення',
        ];
    }
    
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function afterFind()
    {
        parent::afterFind();

        if (is_string($this->fields)) {
            $decoded = Json::decode($this->fields);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->fields = $decoded;
            }
        }
    }
    
    public function search() 
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Post::find()
                ->select('posts.*, count(pv.id_post) as countVisitors, count(pt.id_post) as countTrackers')
                ->join('join', 'user', 'created_by = user.id')
                ->join('left join', 'posts_visitors pv', 'pv.id_post = posts.id')
                ->join('left join', 'posts_track pt', 'pt.id_post = posts.id')
                ->groupBy('pv.id_post, pt.id_post'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);
        
        $dataProvider->query->andFilterWhere(['=', 'name', $this->name]);
        
        return $dataProvider;
    }
}
