<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_account}}`.
 */
class m210108_093431_add_vozvrat_order_id_column_to_product_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_account}}', 'vozvrat_order_id', $this->integer());

        $this->createIndex('idx-product_account-vozvrat_order_id', 'product_account', 'vozvrat_order_id', false);
        $this->addForeignKey("fk-product_account-vozvrat_order_id", "product_account", "vozvrat_order_id", "vozvrat_order", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product_account-vozvrat_order_id','product_account');
        $this->dropIndex('idx-product_account-vozvrat_order_id','product_account');
        $this->dropColumn('{{%product_account}}', 'vozvrat_order_id');
    }
}
