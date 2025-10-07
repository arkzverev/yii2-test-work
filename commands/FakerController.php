<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Faker\Factory;

/**
 * Пример запуска:
 * php yii faker/generate --users=100000 --posts=1000000 --visitorsPerPost=100 --trackPerPost=10 --batch=10000
 */
class FakerController extends Controller
{
    private string $tablePosts = '{{%posts}}';
    private string $tableVisitors = '{{%posts_visitors}}';
    private string $tableTrack = '{{%posts_track}}';
    private string $tableUser = '{{%user}}';
    
    private $faker; 
    
    public function __construct($id, $module, $config = []) {
        $this->faker = Factory::create();
        parent::__construct($id, $module, $config);
    }
    
    public function actionGenerate(
        int $countUsers = 100000,
        int $countPosts = 1000000,
        int $visitorsPerPost = 100,
        int $trackPerPost = 10,
        int $batchSize = 5000,
    ) {
        // Users
//        echo "Генерация пользователей ({$countUsers})\n";
//        $this->generateUsers($countUsers, $batchSize);
//
//        // Posts
//        echo "Генерация постов ({$countPosts})\n";
//        $this->generatePosts($countPosts, $batchSize);
        $postMaxId = (int) Yii::$app->db->createCommand("SELECT MAX(id) FROM {$this->tablePosts}")->queryScalar();

        // Visitors
        echo "Генерация просмотров ({$visitorsPerPost} на пост)\n";
        $this->generateVisitors(1, $postMaxId, $visitorsPerPost, $batchSize);

        // Tracks
        echo "Генерация подписок ({$trackPerPost} на пост)\n";
        $this->generateTracks(1, $postMaxId, $trackPerPost, $batchSize);

        echo "\nГЕНЕРАЦИЯ ЗАВЕРШЕНА УСПЕШНО\n";
        
        return ExitCode::OK;
    }

    private function generateUsers(int $total, int $batch): void
    {
        $inserted = 0;
        for ($i = 0; $i < $total; $i += $batch) {
            $rows = [];
            $limit = min($batch, $total - $i);

            for ($j = 0; $j < $limit; $j++) {
                $createdDate = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s');
                $rows[] = [
                    uniqid(),
                    uniqid() . '.' . $this->faker->email(),
                    md5(uniqid().time()),
                    $this->faker->word(),
                    $createdDate,
                    $createdDate,
                    0,
                ];
            }

            Yii::$app->db->createCommand()
                ->batchInsert($this->tableUser, [
                    'username', 
                    'email', 
                    'password_hash', 
                    'auth_key',
                    'created_at',
                    'updated_at',
                    'flags',
                ], $rows)
                ->execute();

            $inserted += $limit;
            echo "Inserted {$inserted}/{$total} users\r";
        }

        echo "\n";
    }

    private function generatePosts(int $total, int $batch): void
    {
        $inserted = 0;
        for ($i = 0; $i < $total; $i += $batch) {
            $rows = [];
            $limit = min($batch, $total - $i);
            
            $userIds = $this->getRandomUserIds(1000);

            for ($j = 0; $j < $limit; $j++) {
                $rows[] = [
                    $this->faker->sentence(6),
                    $this->faker->paragraph(5),
                    json_encode(['tags' => $this->faker->words(3), 'rating' => $this->faker->randomFloat(2, 0, 5)]),
                    $this->faker->randomElement($userIds),
                    $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                ];
            }

            Yii::$app->db->createCommand()
                ->batchInsert($this->tablePosts, ['name', 'text', 'fields', 'created_by', 'created_at'], $rows)
                ->execute();

            $inserted += $limit;
            echo "Inserted {$inserted}/{$total} posts\r";
        }

        echo "\n";
    }

    private function generateVisitors(int $postMinId, int $postMaxId, int $visitorsPerPost, int $batch): void
    {
        $inserted = 0;
        for ($postId = $postMinId; $postId < $postMaxId; $postId++) {
            $rows = [];
            $userIds = $this->getRandomUserIds($visitorsPerPost);
            
            for ($k = 0; $k < $visitorsPerPost; $k++) {
                $rows[] = [
                    $postId,
                    $userIds[$k],
                    $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
                ];
            }

            foreach (array_chunk($rows, $batch) as $chunk) {
                Yii::$app->db->createCommand()
                    ->batchInsert($this->tableVisitors, ['id_post', 'id_visitor', 'view_at'], $chunk)
                    ->execute();
            }

            $inserted += count($rows);
            if ($postId % 100 == 0) {
                echo "Processed visitors for post #{$postId} ({$inserted} total)\r";
            }
        }

        echo "\n";
    }

    private function generateTracks(int $postMinId, int $postMaxId, int $trackPerPost, int $batch): void
    {
        $inserted = 0;
        for ($postId = $postMinId; $postId < $postMaxId; $postId++) {
            $rows = [];
            $userIds = $this->getRandomUserIds($visitorsPerPost);
            
            for ($k = 0; $k < $trackerPerPost; $k++) {
                $rows[] = [
                    $postId,
                    $userIds[$k],
                    $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d H:i:s'),
                ];
            }

            foreach (array_chunk($rows, $batch) as $chunk) {
                Yii::$app->db->createCommand()
                    ->batchInsert($this->tableTrack, ['id_post', 'id_user', 'track_at'], $chunk)
                    ->execute();
            }

            $inserted += count($rows);
            if ($postId % 100 == 0) {
                echo "Processed tracks for post #{$postId} ({$inserted} total)\r";
            }
        }

        echo "\n";
    }
    
    private function getRandomUserIds(int $count) 
    {
        $sql = "SELECT id FROM {$this->tableUser} ORDER BY rand() LIMIT {$count}";
        return Yii::$app->db->createCommand($sql)->queryColumn();
    }
}
