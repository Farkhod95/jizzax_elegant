<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093380_create_debt_repayment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%debt_repayment}}', [
            'id' => $this->primaryKey(),
            'summa' => $this->float()->comment("summa"),
            'text' => $this->text()->comment("text"),
            'date' => $this->date(),
            'order_account_id' => $this->integer()->comment("regions"),
        ]);

        $this->createIndex('idx-debt_repayment-order_account_id', 'debt_repayment', 'order_account_id', false);
        $this->addForeignKey("fk-debt_repayment-order_account_id", "debt_repayment", "order_account_id", "order_account", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-debt_repayment-order_account_id','debt_repayment');
        $this->dropIndex('idx-debt_repayment-order_account_id','debt_repayment');

        $this->dropTable('{{%debt_repayment}}');
    }
}
