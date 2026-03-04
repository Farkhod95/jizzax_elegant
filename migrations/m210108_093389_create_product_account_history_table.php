<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093389_create_product_account_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_account_history}}', [
            'id' => $this->primaryKey(),
            'order_account_id' => $this->integer()->comment("order_account"),
            'order_account_history_id' => $this->integer()->comment("order_account_history"),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'type' => $this->integer()->comment("Karobka, dona"),
            'price' => $this->float()->comment("price"),
            'real_price' => $this->float()->comment("real_price"),
            'type_sklad_id' => $this->integer()->comment("type_sklad_id"),
            'profit' => $this->float()->comment("profit"),
            'cr_date' => $this->date(),
            'created_by' => $this->integer()->comment("create user"),
        ]);

        $this->createIndex('idx-product_account_history-created_by', 'product_account_history', 'created_by', false);
        $this->addForeignKey("fk-product_account_history-created_by", "product_account_history", "created_by", "users", "id");

        $this->createIndex('idx-product_account_history-order_account_id', 'product_account_history', 'order_account_id', false);
        $this->addForeignKey("fk-product_account_history-order_account_id", "product_account_history", "order_account_id", "order_account", "id");

        $this->createIndex('idx-product_account_history-order_account_history_id', 'product_account_history', 'order_account_history_id', false);
        $this->addForeignKey("fk-product_account_history-order_account_history_id", "product_account_history", "order_account_history_id", "order_account_history", "id");

        $this->createIndex('idx-product_account_history-product_category_id', 'product_account_history', 'product_category_id', false);
        $this->addForeignKey("fk-product_account_history-product_category_id", "product_account_history", "product_category_id", "product_category", "id");

        $this->createIndex('idx-product_account_history-brand_id', 'product_account_history', 'brand_id', false);
        $this->addForeignKey("fk-product_account_history-brand_id", "product_account_history", "brand_id", "brands", "id");

        $this->createIndex('idx-product_account_history-type_sklad_id', 'product_account_history', 'type_sklad_id', false);
        $this->addForeignKey("fk-product_account_history-type_sklad_id", "product_account_history", "type_sklad_id", "type_sklad", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_account_history-created_by','product_account_history');
        $this->dropIndex('idx-product_account_history-created_by','product_account_history');

        $this->dropForeignKey('fk-product_account_history-order_account_id','product_account_history');
        $this->dropIndex('idx-product_account_history-order_account_id','product_account_history');

        $this->dropForeignKey('fk-product_account_history-order_account_history_id','product_account_history');
        $this->dropIndex('idx-product_account_history-order_account_history_id','product_account_history');

        $this->dropForeignKey('fk-product_account_history-product_category_id','product_account_history');
        $this->dropIndex('idx-product_account_history-product_category_id','product_account_history');

        $this->dropForeignKey('fk-product_account_history-brand_id','product_account_history');
        $this->dropIndex('idx-product_account_history-brand_id','product_account_history');

        $this->dropForeignKey('fk-product_account_history-type_sklad_id','product_account_history');
        $this->dropIndex('idx-product_account_history-type_sklad_id','product_account_history');

        $this->dropTable('{{%product_account_history}}');
    }
}
