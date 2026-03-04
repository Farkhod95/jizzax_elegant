<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093422_add_created_by_column_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client}}', 'created_by', $this->integer());

        $this->createIndex('idx-client-created_by', 'client', 'created_by', false);
        $this->addForeignKey("fk-client-created_by", "client", "created_by", "users", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-client-created_by','client');
        $this->dropIndex('idx-client-created_by','client');

        $this->dropColumn('{{%client}}', 'created_by');
    }
}
