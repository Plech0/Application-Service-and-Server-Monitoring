<?php

use yii\db\Migration;

/**
 * Class m200407_142438_initial_migration
 */
class m200523_142438_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('history', [
            'id' => $this->primaryKey(11),
            'server_host' => $this->string(255)->notNull(),
            'resource' => $this->string(64)->notNull(),
            'value' => $this->integer(11)->notNull(),
            'timestamp' => $this->integer(11)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('history');

        return true;
    }
}
