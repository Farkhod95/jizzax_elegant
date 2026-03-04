<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exchange_rate".
 *
 * @property int $id
 * @property int $dollar Dollar
 */
class ExchangeRate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exchange_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dollar'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dollar' => 'Dollar',
        ];
    }
}
