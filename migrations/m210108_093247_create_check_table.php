<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093247_create_check_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%check}}', [
            'id' => $this->primaryKey(),
            'cr_date' => $this->date(),
            'cr_date_time' => $this->datetime(),
            'created_by' => $this->integer()->comment("create user"),
            'test' => $this->text()->comment("Test"),
            'status' => $this->integer()->comment("status"),
        ]);

        $this->createIndex('idx-check-created_by', 'check', 'created_by', false);
        $this->addForeignKey("fk-check-created_by", "check", "created_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-check-created_by','check');
        $this->dropIndex('idx-check-created_by','check');

        $this->dropTable('{{%check}}');
    }
}
