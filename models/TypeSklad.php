<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "type_sklad".
 *
 * @property int $id
 * @property string $name fioname
 *
 * @property ProductAccount[] $productAccounts
 */
class TypeSklad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_sklad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 250],
            [['name'],'required'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductAccounts()
    {
        return $this->hasMany(ProductAccount::className(), ['type_sklad_id' => 'id']);
    }
}
