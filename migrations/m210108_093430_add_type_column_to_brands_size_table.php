<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands_size}}`.
 */
class m210108_093430_add_type_column_to_brands_size_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%brands_size}}', 'type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%brands_size}}', 'type');
    }
}
