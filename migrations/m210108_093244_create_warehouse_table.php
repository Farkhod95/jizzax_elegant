<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093244_create_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%warehouse}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'cr_date' => $this->date(),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-warehouse-brand_id', 'warehouse', 'brand_id', false);
        $this->addForeignKey("fk-warehouse-brand_id", "warehouse", "brand_id", "brands", "id");

        $this->createIndex('idx-warehouse-product_category_id', 'warehouse', 'product_category_id', false);
        $this->addForeignKey("fk-warehouse-product_category_id", "warehouse", "product_category_id", "product_category", "id");

        $this->createIndex('idx-warehouse-created_by', 'warehouse', 'created_by', false);
        $this->addForeignKey("fk-warehouse-created_by", "warehouse", "created_by", "users", "id");

        $this->createIndex('idx-warehouse-update_by', 'warehouse', 'update_by', false);
        $this->addForeignKey("fk-warehouse-update_by", "warehouse", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-warehouse-brand_id','warehouse');
        $this->dropIndex('idx-warehouse-brand_id','warehouse');

        $this->dropForeignKey('fk-warehouse-product_category_id','warehouse');
        $this->dropIndex('idx-warehouse-product_category_id','warehouse');

        $this->dropForeignKey('fk-warehouse-created_by','warehouse');
        $this->dropIndex('idx-warehouse-created_by','warehouse');

        $this->dropForeignKey('fk-warehouse-update_by','warehouse');
        $this->dropIndex('idx-warehouse-update_by','warehouse');

        $this->dropTable('{{%warehouse}}');
    }
}
