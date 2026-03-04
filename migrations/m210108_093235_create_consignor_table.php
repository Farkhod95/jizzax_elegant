<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093235_create_consignor_table  extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%consignor}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(250)->comment("Name"),
            'phone' => $this->string(250)->comment("phone"),
            'address' => $this->text()->comment("address"),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%consignor}}');
    }
}
