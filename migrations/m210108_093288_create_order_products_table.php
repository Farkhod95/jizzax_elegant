<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093288_create_order_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_products}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'cr_date' => $this->date(),
            'order_id' => $this->integer()->comment("create user"),
        ]);

        $this->createIndex('idx-order_products-brand_id', 'order_products', 'brand_id', false);
        $this->addForeignKey("fk-order_products-brand_id", "order_products", "brand_id", "brands", "id");

        $this->createIndex('idx-order_products-product_category_id', 'order_products', 'product_category_id', false);
        $this->addForeignKey("fk-order_products-product_category_id", "order_products", "product_category_id", "product_category", "id");

        $this->createIndex('idx-order_products-order_id', 'order_products', 'order_id', false);
        $this->addForeignKey("fk-order_products-order_id", "order_products", "order_id", "orders", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-order_products-brand_id','order_products');
        $this->dropIndex('idx-order_products-brand_id','order_products');

        $this->dropForeignKey('fk-order_products-product_category_id','order_products');
        $this->dropIndex('idx-order_products-product_category_id','order_products');

        $this->dropForeignKey('fk-order_products-order_id','order_products');
        $this->dropIndex('idx-order_products-order_id','order_products');

        $this->dropTable('{{%order_products}}');
    }
}
