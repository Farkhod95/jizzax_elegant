<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093255_create_warehouse_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%warehouse_history}}', [
            'id' => $this->primaryKey(),
            'sklad_id' => $this->integer()->comment("Sklad"),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'cr_date' => $this->date(),
            'cr_date_time' => $this->datetime(),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-warehouse_history-sklad_id', 'warehouse_history', 'sklad_id', false);
        $this->addForeignKey("fk-warehouse_history-sklad_id", "warehouse_history", "sklad_id", "sklad", "id");

        $this->createIndex('idx-warehouse_history-brand_id', 'warehouse_history', 'brand_id', false);
        $this->addForeignKey("fk-warehouse_history-brand_id", "warehouse_history", "brand_id", "brands", "id");

        $this->createIndex('idx-warehouse_history-product_category_id', 'warehouse_history', 'product_category_id', false);
        $this->addForeignKey("fk-warehouse_history-product_category_id", "warehouse_history", "product_category_id", "product_category", "id");

        $this->createIndex('idx-warehouse_history-created_by', 'warehouse_history', 'created_by', false);
        $this->addForeignKey("fk-warehouse_history-created_by", "warehouse_history", "created_by", "users", "id");

        $this->createIndex('idx-warehouse_history-update_by', 'warehouse_history', 'update_by', false);
        $this->addForeignKey("fk-warehouse_history-update_by", "warehouse_history", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-warehouse_history-sklad_id','warehouse_history');
        $this->dropIndex('idx-warehouse_history-sklad_id','warehouse_history');

        $this->dropForeignKey('fk-warehouse_history-brand_id','warehouse_history');
        $this->dropIndex('idx-warehouse_history-brand_id','warehouse_history');

        $this->dropForeignKey('fk-warehouse_history-product_category_id','warehouse_history');
        $this->dropIndex('idx-warehouse_history-product_category_id','warehouse_history');

        $this->dropForeignKey('fk-warehouse_history-created_by','warehouse_history');
        $this->dropIndex('idx-warehouse_history-created_by','warehouse_history');

        $this->dropForeignKey('fk-warehouse_history-update_by','warehouse_history');
        $this->dropIndex('idx-warehouse_history-update_by','warehouse_history');

        $this->dropTable('{{%warehouse_history}}');
    }
}
