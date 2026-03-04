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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'string'],
            [['name', 'phone'], 'string', 'max' => 250],
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
