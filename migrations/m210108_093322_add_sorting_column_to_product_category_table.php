<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product_category}}`.
 */
class m210108_093322_add_sorting_column_to_product_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_category}}', 'sorting', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_category}}', 'sorting');
    }
}
