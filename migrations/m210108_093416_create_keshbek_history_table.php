<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clients}}`.
 */
class m210108_093416_create_keshbek_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%keshbek_history}}', [
            'id' => $this->primaryKey(),
            'keshbek' => $this->float(),
            'keshbek_sum' => $this->float(),
            'cr_date' => $this->datetime(),
            'client_id' => $this->integer()->comment("client"),
            'order_account_history_id' => $this->integer()->comment("order_account_history"),
        ]);

        $this->createIndex('idx-keshbek_history-client_id', 'keshbek_history', 'client_id', false);
        $this->addForeignKey("fk-keshbek_history-client_id", "keshbek_history", "client_id", "client", "id");

        $this->createIndex('idx-keshbek_history-order_account_history_id', 'keshbek_history', 'order_account_history_id', false);
        $this->addForeignKey("fk-keshbek_history-order_account_history_id", "keshbek_history", "order_account_history_id", "order_account_history", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-keshbek_history-client_id','keshbek_history');
        $this->dropIndex('idx-keshbek_history-client_id','keshbek_history');

        $this->dropForeignKey('fk-keshbek_history-order_account_history_id','keshbek_history');
        $this->dropIndex('idx-keshbek_history-order_account_history_id','keshbek_history');

        $this->dropTable('{{%keshbek_history}}');
    }
}
