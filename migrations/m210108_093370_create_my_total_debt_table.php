<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093370_create_my_total_debt_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%my_total_debt}}', [
            'id' => $this->primaryKey(),
            'cr_date' => $this->date(),
            'total_debt' => $this->float()->comment("total debt"),
            'consignor_id' => $this->integer()->comment("consignor"),
            'created_by' => $this->integer()->comment("create user"),
            'update_by' => $this->integer()->comment("update user"),
        ]);

        $this->createIndex('idx-my_total_debt-consignor_id', 'my_total_debt', 'consignor_id', false);
        $this->addForeignKey("fk-my_total_debt-consignor_id", "my_total_debt", "consignor_id", "consignor", "id");

        $this->createIndex('idx-my_total_debt-created_by', 'my_total_debt', 'created_by', false);
        $this->addForeignKey("fk-my_total_debt-created_by", "my_total_debt", "created_by", "users", "id");

        $this->createIndex('idx-my_total_debt-update_by', 'my_total_debt', 'update_by', false);
        $this->addForeignKey("fk-my_total_debt-update_by", "my_total_debt", "update_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-my_total_debt-consignor_id','my_total_debt');
        $this->dropIndex('idx-my_total_debt-consignor_id','my_total_debt');

        $this->dropForeignKey('fk-my_total_debt-created_by','my_total_debt');
        $this->dropIndex('idx-my_total_debt-created_by','my_total_debt');

        $this->dropForeignKey('fk-my_total_debt-update_by','my_total_debt');
        $this->dropIndex('idx-my_total_debt-update_by','my_total_debt');

        $this->dropTable('{{%my_total_debt}}');
    }
}
