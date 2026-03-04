<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_category}}`.
 */
class m210108_093336_add_sup_status_column_to_product_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_category}}', 'sup_status', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_category}}', 'sup_status');
    }
}
