<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%short_links}}`.
 */
class m250409_072333_create_short_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица для хранения коротких ссылок
        $this->createTable('{{%short_links}}', [
            'id' => $this->primaryKey(),
            'original_url' => $this->string(2000)->notNull(),
            'short_code' => $this->string(10)->unique()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'clicks_count' => $this->integer()->defaultValue(0),
        ]);

        // Таблица для логов переходов
        $this->createTable('{{%link_logs}}', [
            'id' => $this->primaryKey(),
            'link_id' => $this->integer()->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'accessed_at' => $this->integer()->notNull(),
        ]);

        // Индекс для link_id
        $this->createIndex('idx-link_logs-link_id', '{{%link_logs}}', 'link_id');
        $this->addForeignKey('fk-link_logs-link_id', '{{%link_logs}}', 'link_id', '{{%short_links}}', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%link_logs}}');
        $this->dropTable('{{%short_links}}');
    }
}
