<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093381_create_loss_of_profit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%loss_of_profit}}', [
            'id' => $this->primaryKey(),
            'profit' => $this->float()->comment("profit"),
            'loss' => $this->float()->comment("loss"),
            'date_end' => $this->date(),
            'date_start' => $this->date(),
            'created_by' => $this->integer()->comment("create user"),
        ]);

        $this->createIndex('idx-loss_of_profit-created_by', 'loss_of_profit', 'created_by', false);
        $this->addForeignKey("fk-loss_of_profit-created_by", "loss_of_profit", "created_by", "users", "id");  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-loss_of_profit-created_by','loss_of_profit');
        $this->dropIndex('idx-loss_of_profit-created_by','loss_of_profit');

        $this->dropTable('{{%loss_of_profit}}');
    }
}
