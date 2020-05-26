<?php

use yii\db\Migration;

/**
 * Class m200407_142438_initial_migration
 */
class m200407_142438_initial_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('dashboard', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->notNull(),
            'server_host' => $this->string(255)->notNull(),
            'aplication_host' => $this->string(255)->null(),
            'elasticsearch_host' => $this->string(255)->notNull(),
            'elasticsearch_hash' => $this->string(255)->notNull(),
            'cpu' => $this->boolean()->null()->defaultValue(0),
            'memory' => $this->boolean()->null()->defaultValue(0),
            'os' => $this->boolean()->null()->defaultValue(0),
            'browsers' => $this->boolean()->null()->defaultValue(0),
            'unique_users' => $this->boolean()->null()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('dashboard');

        return true;
    }
}
