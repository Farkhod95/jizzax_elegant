<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_account}}`.
 */
class m210108_093451_add_warehouse_id_column_to_product_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_account}}', 'warehouse_id', $this->integer());

        $this->createIndex('idx-product_account-warehouse_id', 'product_account', 'warehouse_id', false);
        $this->addForeignKey("fk-product_account-warehouse_id", "product_account", "warehouse_id", "warehouse", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_account-warehouse_id','product_account');
        $this->dropIndex('idx-product_account-warehouse_id','product_account');
        $this->dropColumn('{{%product_account}}', 'warehouse_id');
    }
}
