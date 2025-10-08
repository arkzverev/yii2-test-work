<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\Post\Visitor;
use app\models\Post\Tracker;
use app\models\Post\User;


class Post extends ActiveRecord
{
    public $countVisitors;
    public $countTrackers;
    public $username;
    
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
            'name' => 'Название',
            'text' => 'Текст',
            'fields' => 'Дополнительные поля',
            'created_by' => 'Автор',
            'created_at' => 'Дата создания',
            'countVisitors' => 'Количество просмотров',
            'countTrackers' => 'Количество подписок',
            'username' => 'Автор',
        ];
    }
    
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getVisitorDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Visitor::find()
                ->select(['pv.view_at', 'u.username'])
                ->alias('pv')
                ->innerJoin('user u', 'pv.id_visitor = u.id')
                ->where(['pv.id_post' => $this->id]),
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);    
    }

    public function getTrackerDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Tracker::find()
                ->select(['pt.track_at', 'u.username'])
                ->alias('pt')
                ->innerJoin('user u', 'pt.id_user = u.id')
                ->where(['pt.id_post' => $this->id]),
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);
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
    
    public function getListDataProvider($params, $modelName): ActiveDataProvider
    {
        $this->load($params, $modelName);

        $query = (new Query())
            ->select([
                'p.*',
                'u.username',
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
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ],
                'attributes' => [
                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['name' => SORT_ASC],
                        'desc' => ['name' => SORT_DESC],
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                    ],
                    'created_at' => [
                        'asc' => ['created_at' => SORT_ASC],
                        'desc' => ['created_at' => SORT_DESC],
                    ],
                    'countVisitors' => [
                        'asc' => ['countVisitors' => SORT_ASC],
                        'desc' => ['countVisitors' => SORT_DESC],
                    ],
                    'countTrackers' => [
                        'asc' => ['countTrackers' => SORT_ASC],
                        'desc' => ['countTrackers' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);
        
        $dataProvider->query->andFilterWhere(['=', 'name', $this->name]);
        
        return $dataProvider;
    }  
}
