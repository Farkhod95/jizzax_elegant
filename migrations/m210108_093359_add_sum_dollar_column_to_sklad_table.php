<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093359_add_sum_dollar_column_to_sklad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sklad}}', 'sum_dollar', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sklad}}', 'sum_dollar');
    }
}
