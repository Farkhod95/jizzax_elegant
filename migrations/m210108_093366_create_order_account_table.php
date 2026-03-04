<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093366_create_order_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_account}}', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->comment("client"),
            'date' => $this->date(),
            'exchange_rate' => $this->float()->comment("dollar exchange rate"),
            'discount_amount' => $this->float()->comment("сумма скидки"),
            'all_summ_dollar' => $this->float()->comment("all_summ_dollar"),
            'all_profit_dollar' => $this->float()->comment("all_profit_dollar"),
            'total_debt' => $this->float()->comment("total debt"),
            'date_last_debt_payment' => $this->date(),
            'number_of_orders' => $this->float()->comment("number_of_orders"),
            'last_order_date' => $this->date(),
            'sum_som' => $this->float()->comment("sum_som"),
            'sum_dollar' => $this->float()->comment("summ_dollar"),
            'sum_cart' => $this->float()->comment("summ_cart"),
            'sum_transfers' => $this->float()->comment("summ_transfers"),
            'created_by' => $this->integer()->comment("create user"),
            'cr_date' => $this->date(),
        ]);

        $this->createIndex('idx-order_account-client_id', 'order_account', 'client_id', false);
        $this->addForeignKey("fk-order_account-client_id", "order_account", "client_id", "client", "id");

        $this->createIndex('idx-order_account-created_by', 'order_account', 'created_by', false);
        $this->addForeignKey("fk-order_account-created_by", "order_account", "created_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order_account-client_id','order_account');
        $this->dropIndex('idx-order_account-client_id','order_account');

        $this->dropForeignKey('fk-order_account-created_by','order_account');
        $this->dropIndex('idx-order_account-created_by','order_account');

        $this->dropTable('{{%order_account}}');
    }
}
