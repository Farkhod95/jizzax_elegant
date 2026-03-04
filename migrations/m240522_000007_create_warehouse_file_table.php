<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%warehouse_file}}`.
 */
class m240522_000007_create_warehouse_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%warehouse_file}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(50)->notNull()->comment('Type: Video or Image'),
            'warehouse_id' => $this->integer()->null()->comment('Brand ID'),
            'file_path' => $this->string(255)->null()->comment('Image or Video file path'),
            'created_at' => $this->datetime(),
        ]);

        $this->createIndex('idx-warehouse_file-warehouse_id', 'warehouse_file', 'warehouse_id', false);
        $this->addForeignKey("fk-warehouse_file-warehouse_id", "warehouse_file", "warehouse_id", "warehouse", "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-warehouse_file-warehouse_id','warehouse_file');
        $this->dropIndex('idx-warehouse_file-warehouse_id','warehouse_file');

        $this->dropTable('{{%warehouse_file}}');
    }
}
