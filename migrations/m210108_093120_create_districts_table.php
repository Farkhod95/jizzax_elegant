<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%districts}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%regions}}`
 */
class m210108_093120_create_districts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%districts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->comment("Наименование"),
            'region_id' => $this->integer()->comment("Регион"),
            'key' => $this->integer()->comment("Key"),
        ]);

        // creates index for column `region_id`
        $this->createIndex(
            '{{%idx-districts-region_id}}',
            '{{%districts}}',
            'region_id'
        );

        // add foreign key for table `{{%regions}}`
        $this->addForeignKey(
            '{{%fk-districts-region_id}}',
            '{{%districts}}',
            'region_id',
            '{{%regions}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%regions}}`
        $this->dropForeignKey(
            '{{%fk-districts-region_id}}',
            '{{%districts}}'
        );

        // drops index for column `region_id`
        $this->dropIndex(
            '{{%idx-districts-region_id}}',
            '{{%districts}}'
        );

        $this->dropTable('{{%districts}}');
    }
}
