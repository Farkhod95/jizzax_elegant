<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clients}}`.
 */
class m210108_093405_create_elegant_history_update_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%elegant_history_update}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(500)->comment("title"),
            'comment' => $this->text()->comment("Comment"),
            'status' => $this->integer()->comment("Статус"),
            'cr_date' => $this->datetime(),
            'created_by' => $this->integer()->comment("create user"),
            'order_account_history_id' => $this->integer()->comment("order_account_history"),
        ]);

        $this->createIndex('idx-elegant_history_update-created_by', 'elegant_history_update', 'created_by', false);
        $this->addForeignKey("fk-elegant_history_update-created_by", "elegant_history_update", "created_by", "users", "id");

        $this->createIndex('idx-elegant_history_update-order_account_history_id', 'elegant_history_update', 'order_account_history_id', false);
        $this->addForeignKey("fk-elegant_history_update-order_account_history_id", "elegant_history_update", "order_account_history_id", "order_account_history", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-elegant_history_update-created_by','elegant_history_update');
        $this->dropIndex('idx-elegant_history_update-created_by','elegant_history_update');

        $this->dropForeignKey('fk-elegant_history_update-order_account_history_id','elegant_history_update');
        $this->dropIndex('idx-elegant_history_update-order_account_history_id','elegant_history_update');

        $this->dropTable('{{%elegant_history_update}}');
    }
}
