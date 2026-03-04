<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093379_create_product_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_account}}', [
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

        $this->createIndex('idx-product_account-created_by', 'product_account', 'created_by', false);
        $this->addForeignKey("fk-product_account-created_by", "product_account", "created_by", "users", "id");

        $this->createIndex('idx-product_account-order_account_id', 'product_account', 'order_account_id', false);
        $this->addForeignKey("fk-product_account-order_account_id", "product_account", "order_account_id", "order_account", "id");

        $this->createIndex('idx-product_account-order_account_history_id', 'product_account', 'order_account_history_id', false);
        $this->addForeignKey("fk-product_account-order_account_history_id", "product_account", "order_account_history_id", "order_account_history", "id");

        $this->createIndex('idx-product_account-product_category_id', 'product_account', 'product_category_id', false);
        $this->addForeignKey("fk-product_account-product_category_id", "product_account", "product_category_id", "product_category", "id");

        $this->createIndex('idx-product_account-brand_id', 'product_account', 'brand_id', false);
        $this->addForeignKey("fk-product_account-brand_id", "product_account", "brand_id", "brands", "id");

        $this->createIndex('idx-product_account-type_sklad_id', 'product_account', 'type_sklad_id', false);
        $this->addForeignKey("fk-product_account-type_sklad_id", "product_account", "type_sklad_id", "type_sklad", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_account-created_by','product_account');
        $this->dropIndex('idx-product_account-created_by','product_account');

        $this->dropForeignKey('fk-product_account-order_account_id','product_account');
        $this->dropIndex('idx-product_account-order_account_id','product_account');

        $this->dropForeignKey('fk-product_account-order_account_history_id','product_account');
        $this->dropIndex('idx-product_account-order_account_history_id','product_account');

        $this->dropForeignKey('fk-product_account-product_category_id','product_account');
        $this->dropIndex('idx-product_account-product_category_id','product_account');

        $this->dropForeignKey('fk-product_account-brand_id','product_account');
        $this->dropIndex('idx-product_account-brand_id','product_account');

        $this->dropForeignKey('fk-product_account-type_sklad_id','product_account');
        $this->dropIndex('idx-product_account-type_sklad_id','product_account');

        $this->dropTable('{{%product_account}}');
    }
}
