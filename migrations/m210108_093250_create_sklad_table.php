<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093250_create_sklad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sklad}}', [
            'id' => $this->primaryKey(),
            'cr_date' => $this->date(),
            'cr_date_time' => $this->datetime(),
            'created_by' => $this->integer()->comment("create user"),
            'status' => $this->integer()->comment("status"),
        ]);

        $this->createIndex('idx-sklad-created_by', 'sklad', 'created_by', false);
        $this->addForeignKey("fk-sklad-created_by", "sklad", "created_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sklad-created_by','sklad');
        $this->dropIndex('idx-sklad-created_by','sklad');

        $this->dropTable('{{%sklad}}');
    }
}
