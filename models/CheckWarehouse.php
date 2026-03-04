<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "check_warehouse".
 *
 * @property int $id
 * @property int $check_id Check
 * @property int $brand_id Brands
 * @property int $product_category_id Product category
 * @property double $size Size
 * @property int $count Count
 * @property string $cr_date
 * @property int $created_by create user
 * @property int $update_by update user
 *
 * @property Brands $brand
 * @property Check $check
 * @property Users $createdBy
 * @property ProductCategory $productCategory
 * @property Users $updateBy
 */
class CheckWarehouse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check_warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_id', 'brand_id', 'product_category_id', 'count', 'created_by', 'update_by'], 'integer'],
            [['size'], 'number'],
            [['cr_date'], 'safe'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['check_id'], 'exist', 'skipOnError' => true, 'targetClass' => Check::className(), 'targetAttribute' => ['check_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['product_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::className(), 'targetAttribute' => ['product_category_id' => 'id']],
            [['update_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['update_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_id' => 'Check ID',
            'brand_id' => 'Model',
            'product_category_id' => 'Nomi',
            'size' => 'O\'lchami',
            'count' => 'Soni',
            'cr_date' => 'Sana',
            'created_by' => 'Kim yaratdi',
            'update_by' => 'Kim o\'zgartirdi',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brands::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheck()
    {
        return $this->hasOne(Check::className(), ['id' => 'check_id']);
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
    public function getProductCategory()
    {
        return $this->hasOne(ProductCategory::className(), ['id' => 'product_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'update_by']);
    }
}
