<?php

use yii\db\Migration;

class m251005_123037_add_posts_tables extends Migration
{
    private const TABLE_POSTS = '{{%posts}}';
    private const TABLE_POST_VISITORS = '{{%posts_visitors}}';
    private const TABLE_POST_TRACK = '{{%posts_track}}';
    private const TABLE_USER = '{{%user}}';
    
    public function safeUp()
    {
        $this->createTable(self::TABLE_POSTS, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'text' => $this->text(),
            'fields' => $this->text()->null()->comment('JSON additional data'),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-posts-created_by', self::TABLE_POSTS, 'created_by');
        $this->addForeignKey(
            'fk-posts-created_by',
            self::TABLE_POSTS,
            'created_by',
            self::TABLE_USER,
            'id',
            'CASCADE'
        );

        $this->createTable(self::TABLE_POST_VISITORS, [
            'id_post' => $this->integer()->notNull(),
            'id_visitor' => $this->integer()->notNull(),
            'view_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addPrimaryKey('pk-posts_visitors', self::TABLE_POST_VISITORS, ['id_post', 'id_visitor']);
        $this->createIndex('idx-posts_visitors-id_post', self::TABLE_POST_VISITORS, 'id_post');
        $this->createIndex('idx-posts_visitors-id_visitor', self::TABLE_POST_VISITORS, 'id_visitor');

        $this->addForeignKey(
            'fk-posts_visitors-post',
            self::TABLE_POST_VISITORS,
            'id_post',
            self::TABLE_POSTS,
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-posts_visitors-visitor',
            self::TABLE_POST_VISITORS,
            'id_visitor',
            self::TABLE_USER,
            'id',
            'CASCADE'
        );

        $this->createTable(self::TABLE_POST_TRACK, [
            'id_post' => $this->integer()->notNull(),
            'id_user' => $this->integer()->notNull(),
            'track_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addPrimaryKey('pk-posts_track', self::TABLE_POST_TRACK, ['id_post', 'id_user']);
        $this->createIndex('idx-posts_track-id_post', self::TABLE_POST_TRACK, 'id_post');
        $this->createIndex('idx-posts_track-id_user', self::TABLE_POST_TRACK, 'id_user');

        $this->addForeignKey(
            'fk-posts_track-post',
            self::TABLE_POST_TRACK,
            'id_post',
            self::TABLE_POSTS,
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-posts_track-user',
            self::TABLE_POST_TRACK,
            'id_user',
            self::TABLE_USER,
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-posts_track-user', self::TABLE_POST_TRACK);
        $this->dropForeignKey('fk-posts_track-post', self::TABLE_POST_TRACK);
        $this->dropTable(self::TABLE_POST_TRACK);

        $this->dropForeignKey('fk-posts_visitors-visitor', self::TABLE_POST_VISITORS);
        $this->dropForeignKey('fk-posts_visitors-post', self::TABLE_POST_VISITORS);
        $this->dropTable(self::TABLE_POST_VISITORS);

        $this->dropForeignKey('fk-posts-created_by', self::TABLE_POSTS);
        $this->dropTable(self::TABLE_POSTS);
    }
}
