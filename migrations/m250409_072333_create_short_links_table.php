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
        $this->createTable('{{%short_links}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%short_links}}');
    }
}
