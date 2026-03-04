<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%vozvrat_order}}`.
 */
class m210108_093453_add_update_status_column_to_vozvrat_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%vozvrat_order}}', 'update_status', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%vozvrat_order}}', 'update_status');
    }
}
