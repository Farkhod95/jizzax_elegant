<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093248_create_check_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%check_warehouse}}', [
            'id' => $this->primaryKey(),
            'check_id' => $this->integer()->comment("Check"),
            'brand_id' => $this->integer()->comment("Brands"),
            'product_category_id' => $this->integer()->comment("Product category"),
            'size' => $this->float()->comment("Size"),
            'count' => $this->integer()->comment("Count"),
            'cr_date' => $this->date(),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-check_warehouse-check_id', 'check_warehouse', 'check_id', false);
        $this->addForeignKey("fk-check_warehouse-check_id", "check_warehouse", "check_id", "check", "id");

        $this->createIndex('idx-check_warehouse-brand_id', 'check_warehouse', 'brand_id', false);
        $this->addForeignKey("fk-check_warehouse-brand_id", "check_warehouse", "brand_id", "brands", "id");

        $this->createIndex('idx-check_warehouse-product_category_id', 'check_warehouse', 'product_category_id', false);
        $this->addForeignKey("fk-check_warehouse-product_category_id", "check_warehouse", "product_category_id", "product_category", "id");

        $this->createIndex('idx-check_warehouse-created_by', 'check_warehouse', 'created_by', false);
        $this->addForeignKey("fk-check_warehouse-created_by", "check_warehouse", "created_by", "users", "id");

        $this->createIndex('idx-check_warehouse-update_by', 'check_warehouse', 'update_by', false);
        $this->addForeignKey("fk-check_warehouse-update_by", "check_warehouse", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-check_warehouse-check_id','check_warehouse');
        $this->dropIndex('idx-check_warehouse-check_id','check_warehouse');

        $this->dropForeignKey('fk-check_warehouse-brand_id','check_warehouse');
        $this->dropIndex('idx-check_warehouse-brand_id','check_warehouse');

        $this->dropForeignKey('fk-check_warehouse-product_category_id','check_warehouse');
        $this->dropIndex('idx-check_warehouse-product_category_id','check_warehouse');

        $this->dropForeignKey('fk-check_warehouse-created_by','check_warehouse');
        $this->dropIndex('idx-check_warehouse-created_by','check_warehouse');

        $this->dropForeignKey('fk-check_warehouse-update_by','check_warehouse');
        $this->dropIndex('idx-check_warehouse-update_by','check_warehouse');

        $this->dropTable('{{%check_warehouse}}');
    }
}
