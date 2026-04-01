<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%debt_repayment}}`.
 */
class m210108_093457_add_sum_otkazma_column_to_debt_repayment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%debt_repayment}}', 'sum_otkazma', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%debt_repayment}}', 'sum_otkazma');
    }
}
