<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093384_add_client_id_column_to_debt_repayment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%debt_repayment}}', 'client_id', $this->integer());

        $this->createIndex('idx-debt_repayment-client_id', 'debt_repayment', 'client_id', false);
        $this->addForeignKey("fk-debt_repayment-client_id", "debt_repayment", "client_id", "client", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-debt_repayment-client_id','debt_repayment');
        $this->dropIndex('idx-debt_repayment-client_id','debt_repayment');

        $this->dropColumn('{{%debt_repayment}}', 'client_id');
    }
}
