<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%warehouse}}`.
 */
class m210108_093299_add_price_column_to_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%warehouse}}', 'price', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%warehouse}}', 'price');
    }
}
