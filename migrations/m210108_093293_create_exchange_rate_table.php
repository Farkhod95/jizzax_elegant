<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exchange_rate}}`.
 */
class m210108_093293_create_exchange_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exchange_rate}}', [
            'id' => $this->primaryKey(),
            'dollar' => $this->integer()->comment("Dollar"),
        ]);
        $this->insert('exchange_rate',array(
            'dollar' => 12450,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exchange_rate}}');
    }
}
