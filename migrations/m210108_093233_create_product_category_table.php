<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093233_create_product_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->comment("Name"),
            'brand_id' => $this->integer()->comment("Brands"),
        ]);

        $this->createIndex('idx-product_category-brand_id', 'product_category', 'brand_id', false);
        $this->addForeignKey("fk-product_category-brand_id", "product_category", "brand_id", "brands", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_category-brand_id','product_category');
        $this->dropIndex('idx-product_category-brand_id','product_category');

        $this->dropTable('{{%product_category}}');
    }
}
