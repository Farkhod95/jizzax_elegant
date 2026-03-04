<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093385_create_price_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%price_product}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'real_price' => $this->float()->comment("Size"),
            'cr_date' => $this->date(),
        ]);

        $this->createIndex('idx-price_product-brand_id', 'price_product', 'brand_id', false);
        $this->addForeignKey("fk-price_product-brand_id", "price_product", "brand_id", "brands", "id");

        $this->createIndex('idx-price_product-product_category_id', 'price_product', 'product_category_id', false);
        $this->addForeignKey("fk-price_product-product_category_id", "price_product", "product_category_id", "product_category", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-price_product-brand_id','price_product');
        $this->dropIndex('idx-price_product-brand_id','price_product');

        $this->dropForeignKey('fk-price_product-product_category_id','price_product');
        $this->dropIndex('idx-price_product-product_category_id','price_product');

        $this->dropTable('{{%price_product}}');
    }
}
