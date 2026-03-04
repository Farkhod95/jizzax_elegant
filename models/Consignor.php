<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "consignor".
 *
 * @property int $id
 * @property string $name Name
 * @property string $phone phone
 * @property string $address address
 *
 * @property Sklad[] $sklads
 */
class Consignor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consignor';
    }

    var $my_all_total_debt = null;
    var $total_debts = null;

    public function rules()
    {
        return [
            [['address'], 'string'],
            [['name', 'phone'], 'string', 'max' => 250],
            [['name'] ,'unique'],
            [['name', 'phone'],'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nomi',
            'phone' => 'Telefon nomeri',
            'address' => 'Manzili',
            'total_debts' => 'Mening qarzim ($)',
            'my_all_total_debt' => 'Mening qarzim ($)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSklads()
    {
        return $this->hasMany(Sklad::className(), ['consignor_id' => 'id']);
    }
}
