<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%debt_repayment}}`.
 */
class m210108_093454_add_zdacha_sum_column_to_debt_repayment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%debt_repayment}}', 'zdacha_sum', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%debt_repayment}}', 'zdacha_sum');
    }
}
