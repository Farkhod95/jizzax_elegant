<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vozvrat_order}}`.
 */
class m210108_093376_create_vozvrat_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vozvrat_order}}', [
            'id' => $this->primaryKey(),
            'client_id' => $this->integer()->comment("client"),
            'date' => $this->date(),
            'exchange_rate' => $this->float()->comment("dollar exchange rate"),
            'discount_amount' => $this->float()->comment("сумма скидки"),
            'sum_som' => $this->float()->comment("sum_som"),
            'sum_dollar' => $this->float()->comment("summ_dollar"),
            'sum_cart' => $this->float()->comment("summ_cart"),
            'product_summ_dollar' => $this->float()->comment("product_summ_dollar"),
            'all_summ_dollar' => $this->float()->comment("all_summ_dollar"),
            'old_total_debt' => $this->float()->comment("Old total debt"),
            'total_debt' => $this->float()->comment("total debt"),
            'confirmation' => $this->boolean()->comment("Confirmation"),
            'cr_date_time' => $this->datetime(),
            'large_price' => $this->integer()->comment("large price"),
            'is_delete' => $this->integer()->comment("is delete"),
            'comment' => $this->text()->comment("Comment"),
            'created_by' => $this->integer()->comment("create user"),
        ]);

        $this->createIndex('idx-vozvrat_order-created_by', 'vozvrat_order', 'created_by', false);
        $this->addForeignKey("fk-vozvrat_order-created_by", "vozvrat_order", "created_by", "users", "id");

        $this->createIndex('idx-vozvrat_order-client_id', 'vozvrat_order', 'client_id', false);
        $this->addForeignKey("fk-vozvrat_order-client_id", "vozvrat_order", "client_id", "client", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-vozvrat_order-created_by','vozvrat_order');
        $this->dropIndex('idx-vozvrat_order-created_by','vozvrat_order');

        $this->dropForeignKey('fk-vozvrat_order-client_id','vozvrat_order');
        $this->dropIndex('idx-vozvrat_order-client_id','vozvrat_order');

        $this->dropTable('{{%vozvrat_order}}');
    }
}
