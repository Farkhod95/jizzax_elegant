<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "keshbek_history".
 *
 * @property int $id
 * @property double $keshbek
 * @property double $keshbek_sum
 * @property string $cr_date
 * @property int $client_id client
 * @property int $order_account_history_id order_account_history
 *
 * @property Client $client
 * @property OrderAccountHistory $orderAccountHistory
 */
class KeshbekHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'keshbek_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keshbek', 'keshbek_sum'], 'number'],
            [['cr_date'], 'safe'],
            [['client_id', 'order_account_history_id'], 'integer'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['order_account_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderAccountHistory::className(), 'targetAttribute' => ['order_account_history_id' => 'id']],
            [['keshbek_sum', 'cr_date'],'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keshbek' => 'Keshbek',
            'keshbek_sum' => 'Keshbek Sum ($)',
            'cr_date' => 'Sana',
            'client_id' => 'Mijoz',
            'order_account_history_id' => 'Buyurtma summasi ($)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderAccountHistory()
    {
        return $this->hasOne(OrderAccountHistory::className(), ['id' => 'order_account_history_id']);
    }
}
