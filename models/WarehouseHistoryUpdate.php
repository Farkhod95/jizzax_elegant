<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "warehouse_history_update".
 *
 * @property int $id
 * @property int $brand_id Brands
 * @property int $product_category_id Product category
 * @property double $size Size
 * @property int $count Count
 * @property int $count_update Count update
 * @property string $cr_date
 * @property int $created_by create user
 * @property int $update_by update user
 *
 * @property Brands $brand
 * @property Users $createdBy
 * @property ProductCategory $productCategory
 * @property Users $updateBy
 */
class WarehouseHistoryUpdate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse_history_update';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['brand_id', 'product_category_id', 'count', 'count_update', 'created_by', 'update_by', 'type'], 'integer'],
            [['size'], 'number'],
            [['cr_date'], 'safe'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'id']],
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
            'brand_id' => 'Model',
            'product_category_id' => 'Nomi',
            'size' => 'O\'lchami',
            'count' => 'Eski soni',
            'count_update' => 'O\'zgargan soni',
            'cr_date' => 'Sana',
            'created_by' => 'Kim yaratdi',
            'update_by' => 'Kim o\'zgartirdi',
            'type' => 'Tip',
        ];
    }

    public function getProductType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Dona',],
            ['id' => '2', 'type' => 'Karobka',],
            ['id' => '3', 'type' => 'Komplekt',],
            ['id' => '4', 'type' => 'Pochka',],
        ],
        'id', 'type');
    }
    
    public function getProductTypeView($id)
    {
        if($id == 1) return 'Dona';
        if($id == 2) return 'Karobka';
        if($id == 3) return 'Komplekt';
        if($id == 4) return 'Pochka';
    }

    public function getTypeNameView($name)
    {
        if($name == 'Dona') return 1;
        if($name == 'Karobka') return 2;
        if($name == 'Komplekt') return 3;
        if($name == 'Pochka') return 4;
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
