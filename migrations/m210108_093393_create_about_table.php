<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210108_093393_create_about_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%about}}', [
            'id' => $this->primaryKey(),
            'nomer_nakladnoy' => $this->string(250)->comment("nomer_nakladnoy"),
            'postavshik' => $this->string(250)->comment("supplier"),
            'phone' => $this->string(250)->comment("phone"),
            'dostavshik' => $this->string(250)->comment("dostavshik"),
            't_p' => $this->string(250)->comment("t_p"),
        ]);
        $this->insert('about',array(
            'nomer_nakladnoy' => '43380421',
            'postavshik' => 'ЧП "ABU SAMI ELEGANCE"',
            'phone' => '+998983646066',
            'dostavshik' => 'Baxodir 910057778 Shamsulo 901286669',
            't_p' => '83-109 Do\'kon',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%about}}');
    }
}
