<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%brands}}`.
 */
class m210108_093311_create_prices_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%prices}}', [
            'id' => $this->primaryKey(),
            'price' => $this->integer()->comment("price"),
            'warehouse_id' => $this->integer()->comment("Brands"),
        ]);

        $this->createIndex('idx-prices-warehouse_id', 'prices', 'warehouse_id', false);
        $this->addForeignKey("fk-prices-warehouse_id", "prices", "warehouse_id", "warehouse", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-prices-warehouse_id','prices');
        $this->dropIndex('idx-prices-warehouse_id','prices');

        $this->dropTable('{{%prices}}');
    }
}
