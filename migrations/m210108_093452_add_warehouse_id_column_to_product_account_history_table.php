<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_account_history}}`.
 */
class m210108_093452_add_warehouse_id_column_to_product_account_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_account_history}}', 'warehouse_id', $this->integer());

        $this->createIndex('idx-product_account_history-warehouse_id', 'product_account_history', 'warehouse_id', false);
        $this->addForeignKey("fk-product_account_history-warehouse_id", "product_account_history", "warehouse_id", "warehouse", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_account_history-warehouse_id','product_account_history');
        $this->dropIndex('idx-product_account_history-warehouse_id','product_account_history');
        $this->dropColumn('{{%product_account_history}}', 'warehouse_id');
    }
}
