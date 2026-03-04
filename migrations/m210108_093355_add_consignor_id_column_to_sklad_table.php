<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%brands}}`.
 */
class m210108_093355_add_consignor_id_column_to_sklad_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sklad}}', 'consignor_id', $this->integer());

        $this->createIndex('idx-sklad-consignor_id', 'sklad', 'consignor_id', false);
        $this->addForeignKey("fk-sklad-consignor_id", "sklad", "consignor_id", "consignor", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sklad-consignor_id','sklad');
        $this->dropIndex('idx-sklad-consignor_id','sklad');

        $this->dropColumn('{{%sklad}}', 'consignor_id');
    }
}
