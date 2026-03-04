<?php

use yii\db\Migration;

/**
 * Handles altering column type for `price` in table `{{%warehouse}}`.
 */
class m210109_093414_alter_price_column_in_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Price ustunini float ga o'zgartirish
        $this->alterColumn('{{%warehouse}}', 'price', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Price ustunini integer ga qaytarish
        $this->alterColumn('{{%warehouse}}', 'price', $this->integer());
    }
}