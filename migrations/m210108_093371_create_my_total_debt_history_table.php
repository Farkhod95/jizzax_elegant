<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093371_create_my_total_debt_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%my_total_debt_history}}', [
            'id' => $this->primaryKey(),
            'cr_date' => $this->date(),
            'total_debt' => $this->float()->comment("total debt"),
            'discount_amount' => $this->float()->comment("discount amount"),
            'my_total_debt_id' => $this->integer()->comment("my total debt"),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-my_total_debt_history-my_total_debt_id', 'my_total_debt_history', 'my_total_debt_id', false);
        $this->addForeignKey("fk-my_total_debt_history-my_total_debt_id", "my_total_debt_history", "my_total_debt_id", "my_total_debt", "id");

        $this->createIndex('idx-my_total_debt_history-created_by', 'my_total_debt_history', 'created_by', false);
        $this->addForeignKey("fk-my_total_debt_history-created_by", "my_total_debt_history", "created_by", "users", "id");

        $this->createIndex('idx-my_total_debt_history-update_by', 'my_total_debt_history', 'update_by', false);
        $this->addForeignKey("fk-my_total_debt_history-update_by", "my_total_debt_history", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-my_total_debt_history-my_total_debt_id','my_total_debt_history');
        $this->dropIndex('idx-my_total_debt_history-my_total_debt_id','my_total_debt_history');

        $this->dropForeignKey('fk-my_total_debt_history-created_by','my_total_debt_history');
        $this->dropIndex('idx-my_total_debt_history-created_by','my_total_debt_history');

        $this->dropForeignKey('fk-my_total_debt_history-update_by','my_total_debt_history');
        $this->dropIndex('idx-my_total_debt_history-update_by','my_total_debt_history');

        $this->dropTable('{{%my_total_debt_history}}');
    }
}
