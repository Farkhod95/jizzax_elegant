<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093456_add_sum_otkazma_column_to_order_account_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_account_history}}', 'sum_otkazma', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_account_history}}', 'sum_otkazma');
    }
}
