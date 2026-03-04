<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093277_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'order_number' => $this->string(150)->comment("order number"),
            'customer_fio' => $this->string(255)->comment("customer fio"),
            'cr_date' => $this->date(),
            'cr_date_time' => $this->datetime(),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
            'status' => $this->integer()->comment("status"),
        ]);

        $this->createIndex('idx-orders-created_by', 'orders', 'created_by', false);
        $this->addForeignKey("fk-orders-created_by", "orders", "created_by", "users", "id");

        $this->createIndex('idx-orders-update_by', 'orders', 'update_by', false);
        $this->addForeignKey("fk-orders-update_by", "orders", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-orders-created_by','orders');
        $this->dropIndex('idx-orders-created_by','orders');

        $this->dropForeignKey('fk-orders-update_by','orders');
        $this->dropIndex('idx-orders-update_by','orders');

        $this->dropTable('{{%orders}}');
    }
}
