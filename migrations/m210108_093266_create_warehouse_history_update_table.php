<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093266_create_warehouse_history_update_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%warehouse_history_update}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'count_update' => $this->integer()->comment("Count update"),
            'cr_date' => $this->date(),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-warehouse_history_update-brand_id', 'warehouse_history_update', 'brand_id', false);
        $this->addForeignKey("fk-warehouse_history_update-brand_id", "warehouse_history_update", "brand_id", "brands", "id");

        $this->createIndex('idx-warehouse_history_update-product_category_id', 'warehouse_history_update', 'product_category_id', false);
        $this->addForeignKey("fk-warehouse_history_update-product_category_id", "warehouse_history_update", "product_category_id", "product_category", "id");

        $this->createIndex('idx-warehouse_history_update-created_by', 'warehouse_history_update', 'created_by', false);
        $this->addForeignKey("fk-warehouse_history_update-created_by", "warehouse_history_update", "created_by", "users", "id");

        $this->createIndex('idx-warehouse_history_update-update_by', 'warehouse_history_update', 'update_by', false);
        $this->addForeignKey("fk-warehouse_history_update-update_by", "warehouse_history_update", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-warehouse_history_update-brand_id','warehouse_history_update');
        $this->dropIndex('idx-warehouse_history_update-brand_id','warehouse_history_update');

        $this->dropForeignKey('fk-warehouse_history_update-product_category_id','warehouse_history_update');
        $this->dropIndex('idx-warehouse_history_update-product_category_id','warehouse_history_update');

        $this->dropForeignKey('fk-warehouse_history_update-created_by','warehouse_history_update');
        $this->dropIndex('idx-warehouse_history_update-created_by','warehouse_history_update');

        $this->dropForeignKey('fk-warehouse_history_update-update_by','warehouse_history_update');
        $this->dropIndex('idx-warehouse_history_update-update_by','warehouse_history_update');

        $this->dropTable('{{%warehouse_history_update}}');
    }
}
