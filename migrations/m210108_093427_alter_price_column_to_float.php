<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093427_alter_price_column_to_float extends Migration
{
    public function safeUp()
    {
        // price ustunini float yoki decimal tipga o‘zgartiramiz
        $this->alterColumn('{{%prices}}', 'price', $this->decimal(10, 2)->comment("price"));
    }

    public function safeDown()
    {
        // rollback holatida eski integer tipga qaytaramiz
        $this->alterColumn('{{%prices}}', 'price', $this->integer()->comment("price"));
    }
}
