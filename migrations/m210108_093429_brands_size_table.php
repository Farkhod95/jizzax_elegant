<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands_size}}`.
 */
class m210108_093429_brands_size_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%brands_size}}', [
            'id' => $this->primaryKey(),
            'size' => $this->float()->comment("Size"),
            'brand_id' => $this->integer()->comment("Product category"),
            'product_category_id' => $this->integer()->comment("Product category"),
            
        ]);

        $this->createIndex('idx-brands_size-brand_id', 'brands_size', 'brand_id', false);
        $this->addForeignKey("fk-brands_size-brand_id", "brands_size", "brand_id", "brands", "id");

        $this->createIndex('idx-brands_size-product_category_id', 'brands_size', 'product_category_id', false);
        $this->addForeignKey("fk-brands_size-product_category_id", "brands_size", "product_category_id", "product_category", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        

        $this->dropForeignKey('fk-brands_size-brand_id','brands_size');
        $this->dropIndex('idx-brands_size-brand_id','brands_size');

        $this->dropForeignKey('fk-brands_size-product_category_id','brands_size');
        $this->dropIndex('idx-brands_size-product_category_id','brands_size');

        $this->dropTable('{{%brands_size}}');
    }
}
