<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

class Client extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'client';
    }

    public function rules()
    {
        return [
            [['region_id', 'district_id', 'is_worker', 'created_by', 'is_partner', 'type', 'is_profit_loss'], 'integer'],
            [['keshbek', 'total_debt'], 'number'],
            [['fio', 'phone', 'address'], 'string', 'max' => 250],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => Districts::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['fio', 'phone'], 'required'],
            ['phone', 'unique', 'message' => 'Bu mijoz tizimda mavjud'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'FIO',
            'phone' => 'Telefon nomer',
            'region_id' => 'Viloyat',
            'district_id' => 'Tuman',
            'address' => 'Manzil',
            'total_debt' => 'Qarzi ($)',
            'keshbek' => 'Keshbek (%)',
            'is_worker' => 'Is Worker',
            'created_by' => 'Kim qo\'shgan',
            'is_partner' => 'Hamkor',
            'type' => 'Mijoz turi',
            'is_profit_loss' => 'Foyda/Zarar hisoblanmasinmi?',
        ];
    }

    public function beforeValidate()
    {
        if (empty($this->type)) {
            $this->type = 1;
        }

        if ($this->total_debt === null || $this->total_debt === '') {
            $this->total_debt = 0;
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->is_worker = 0;
            $this->created_by = Yii::$app->user->identity->id;

            if (empty($this->type)) {
                $this->type = 1;
            }

            if ($this->total_debt === null || $this->total_debt === '') {
                $this->total_debt = 0;
            }
        }

        return parent::beforeSave($insert);
    }

    public function getType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Oddiy mijoz'],
            ['id' => '2', 'type' => 'Bozordagi mijoz'],
            ['id' => '3', 'type' => 'Filial'],
        ], 'id', 'type');
    }

    public function getTypeView($id)
    {
        if ($id == 1) return 'Oddiy mijoz';
        if ($id == 2) return 'Bozordagi mijoz';
        if ($id == 3) return 'Filial';
    }

    public function getTypeNameView($name)
    {
        if ($name == 'Oddiy mijoz') return 1;
        if ($name == 'Bozordagi mijoz') return 2;
        if ($name == 'Filial') return 3;
    }

    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    public function getDistrict()
    {
        return $this->hasOne(Districts::className(), ['id' => 'district_id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    public function getRegions()
    {
        return ArrayHelper::map(Regions::find()->all(), 'id', 'name');
    }

    public function getDistricts($id)
    {
        return ArrayHelper::map(Districts::find()->where(['region_id' => $id])->all(), 'id', 'name');
    }

    public function getOrderAccounts()
    {
        return $this->hasMany(OrderAccount::className(), ['client_id' => 'id']);
    }

    public function getOrderAccountHistories()
    {
        return $this->hasMany(OrderAccountHistory::className(), ['client_id' => 'id']);
    }

    public function getVozvratOrders()
    {
        return $this->hasMany(VozvratOrder::className(), ['client_id' => 'id']);
    }
}