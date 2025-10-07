<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\db\Query;

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
    
    public function getListDataProvider() 
    {
        $query = (new Query())
            ->select([
                'p.*',
                'IFNULL(v.visitor_count, 0) AS countVisitors',
                'IFNULL(t.track_count, 0) AS countTrackers',
            ])
            ->from(['p' => 'posts'])
            ->innerJoin(['u' => 'user'], 'u.id = p.created_by')
            ->leftJoin([
                'v' => (new Query())
                    ->select(['id_post', 'visitor_count' => new Expression('COUNT(*)')])
                    ->from('posts_visitors')
                    ->groupBy('id_post')
            ], 'v.id_post = p.id')
            ->leftJoin([
                't' => (new Query())
                    ->select(['id_post', 'track_count' => new Expression('COUNT(*)')])
                    ->from('posts_track')
                    ->groupBy('id_post')
            ], 't.id_post = p.id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
        
        $dataProvider->query->andFilterWhere(['=', 'name', $this->name]);
        
        return $dataProvider;
    }  
}
