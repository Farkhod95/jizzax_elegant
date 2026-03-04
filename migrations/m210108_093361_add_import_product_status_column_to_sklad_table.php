<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093361_add_import_product_status_column_to_sklad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sklad}}', 'import_product_status', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sklad}}', 'import_product_status');
    }
}
