<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093355_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'fio' => $this->string(250)->comment("fio"),
            'phone' => $this->string(250)->comment("phone"),
            'region_id' => $this->integer()->comment("regions"),
            'district_id' => $this->integer()->comment("districts"),
            'address' => $this->string(250)->comment("address"),
        ]);

        $this->createIndex('idx-client-region_id', 'client', 'region_id', false);
        $this->addForeignKey("fk-client-region_id", "client", "region_id", "regions", "id");

        $this->createIndex('idx-client-district_id', 'client', 'district_id', false);
        $this->addForeignKey("fk-client-district_id", "client", "district_id", "districts", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-client-region_id','client');
        $this->dropIndex('idx-client-region_id','client');

        $this->dropForeignKey('fk-client-district_id','client');
        $this->dropIndex('idx-client-district_id','client');

        $this->dropTable('{{%client}}');
    }
}
