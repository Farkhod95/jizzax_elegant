<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093383_create_expenses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expenses}}', [
            'id' => $this->primaryKey(),
            'nomi' => $this->string(250)->comment("nomi"),
            'summa' => $this->float()->comment("summa"),
            'date_cr' => $this->date(),
            'type_id' => $this->integer()->comment("type_id"),
            'loss_of_profit_id' => $this->integer()->comment("loss_of_profit"),
        ]);

        $this->createIndex('idx-expenses-type_id', 'expenses', 'type_id', false);
        $this->addForeignKey("fk-expenses-type_id", "expenses", "type_id", "type_expense", "id");  

        $this->createIndex('idx-expenses-loss_of_profit_id', 'expenses', 'loss_of_profit_id', false);
        $this->addForeignKey("fk-expenses-loss_of_profit_id", "expenses", "loss_of_profit_id", "loss_of_profit", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-expenses-type_id','expenses');
        $this->dropIndex('idx-expenses-type_id','expenses');
        
        $this->dropForeignKey('fk-expenses-loss_of_profit_id','expenses');
        $this->dropIndex('idx-expenses-loss_of_profit_id','expenses');

        $this->dropTable('{{%expenses}}');
    }
}
