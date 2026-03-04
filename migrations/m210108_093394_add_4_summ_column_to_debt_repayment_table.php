<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093394_add_4_summ_column_to_debt_repayment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%debt_repayment}}', 'sum_som', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'summ_dollar', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'summ_cart', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'sum_transfers', $this->float());

        $this->addColumn('{{%debt_repayment}}', 'total_debt', $this->float());
        
        $this->addColumn('{{%debt_repayment}}', 'exchange_rate', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'discount_amount', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'all_summ_dollar', $this->float());
        $this->addColumn('{{%debt_repayment}}', 'created_by', $this->integer());

        $this->createIndex('idx-debt_repayment-created_by', 'debt_repayment', 'created_by', false);
        $this->addForeignKey("fk-debt_repayment-created_by", "debt_repayment", "created_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%debt_repayment}}', 'sum_som');
        $this->dropColumn('{{%debt_repayment}}', 'summ_dollar');
        $this->dropColumn('{{%debt_repayment}}', 'summ_cart');
        $this->dropColumn('{{%debt_repayment}}', 'sum_transfers');

        $this->dropColumn('{{%debt_repayment}}', 'total_debt');
        $this->dropColumn('{{%debt_repayment}}', 'exchange_rate');
        $this->dropColumn('{{%debt_repayment}}', 'discount_amount');
        $this->dropColumn('{{%debt_repayment}}', 'all_summ_dollar');
        $this->dropColumn('{{%debt_repayment}}', 'created_by');

        $this->dropForeignKey('fk-debt_repayment-created_by','debt_repayment');
        $this->dropIndex('idx-debt_repayment-created_by','debt_repayment');
    }
}
