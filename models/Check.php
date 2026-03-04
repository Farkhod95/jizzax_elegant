<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "check".
 *
 * @property int $id
 * @property string $cr_date
 * @property string $cr_date_time
 * @property int $created_by create user
 * @property string $test Test
 * @property int $status status
 *
 * @property Users $createdBy
 * @property CheckWarehouse[] $checkWarehouses
 */
class Check extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cr_date', 'cr_date_time'], 'safe'],
            [['created_by', 'status'], 'integer'],
            [['test'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cr_date' => 'Sana',
            'cr_date_time' => 'Vaqt',
            'created_by' => 'Kim tomonidan',
            'test' => 'Izoh',
            'status' => 'Holati',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckWarehouses()
    {
        return $this->hasMany(CheckWarehouse::className(), ['check_id' => 'id']);
    }
}
