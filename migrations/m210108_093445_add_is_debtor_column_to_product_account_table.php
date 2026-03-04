<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_account}}`.
 */
class m210108_093445_add_is_debtor_column_to_product_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_account}}', 'is_debtor', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_account}}', 'is_debtor');
    }
}
