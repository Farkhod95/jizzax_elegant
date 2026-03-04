<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093375_add_old_my_total_debt_column_to_sklad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sklad}}', 'old_my_total_debt', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sklad}}', 'old_my_total_debt');
    }
}
