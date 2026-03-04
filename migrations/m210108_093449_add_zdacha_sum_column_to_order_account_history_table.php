<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_account_history}}`.
 */
class m210108_093449_add_zdacha_sum_column_to_order_account_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_account_history}}', 'zdacha_sum', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_account_history}}', 'zdacha_sum');
    }
}
