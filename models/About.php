<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "about".
 *
 * @property int $id
 * @property string $nomer_nakladnoy nomer_nakladnoy
 * @property string $postavshik supplier
 * @property string $phone phone
 * @property string $dostavshik dostavshik
 * @property string $t_p t_p
 */
class About extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'about';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomer_nakladnoy', 'postavshik', 'phone', 'dostavshik', 't_p'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomer_nakladnoy' => 'Nomer Nakladnoy',
            'postavshik' => 'Firma',
            'phone' => 'Telefon',
            'dostavshik' => 'Yetkazib beruvchi',
            't_p' => 'Do\'kon',
        ];
    }
}
