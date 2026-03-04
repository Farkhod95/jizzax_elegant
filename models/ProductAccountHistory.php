<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_account_history".
 *
 * @property int $id
 * @property int $order_account_id order_account
 * @property int $order_account_history_id order_account_history
 * @property int $brand_id Brands
 * @property int $product_category_id Product category
 * @property double $size Size
 * @property int $count Count
 * @property int $type Karobka, dona
 * @property double $price price
 * @property double $real_price real_price
 * @property int $type_sklad_id type_sklad_id
 * @property double $profit profit
 * @property string $cr_date
 * @property int $created_by create user
 *
 * @property Brands $brand
 * @property Users $createdBy
 * @property OrderAccountHistory $orderAccountHistory
 * @property OrderAccount $orderAccount
 * @property ProductCategory $productCategory
 * @property TypeSklad $typeSklad
 */
class ProductAccountHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_account_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_account_id', 'order_account_history_id', 'brand_id', 'product_category_id', 'count', 'type', 'type_sklad_id', 'created_by', 'given_count', 'is_debtor', 'warehouse_id'], 'integer'],
            [['size', 'price', 'real_price', 'profit'], 'number'],
            [['cr_date'], 'safe'],
            // [['given_count'],'required'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['order_account_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderAccountHistory::className(), 'targetAttribute' => ['order_account_history_id' => 'id']],
            [['order_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderAccount::className(), 'targetAttribute' => ['order_account_id' => 'id']],
            [['product_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::className(), 'targetAttribute' => ['product_category_id' => 'id']],
            [['type_sklad_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypeSklad::className(), 'targetAttribute' => ['type_sklad_id' => 'id']],
            [['vozvrat_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => VozvratOrder::className(), 'targetAttribute' => ['vozvrat_order_id' => 'id']],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_account_id' => 'Order Account ID',
            'order_account_history_id' => 'Order Account History ID',
            'brand_id' => 'Brand ID',
            'product_category_id' => 'Product Category ID',
            'size' => 'Size',
            'count' => 'Count',
            'type' => 'Type',
            'price' => 'Price',
            'real_price' => 'Real Price',
            'type_sklad_id' => 'Type Sklad ID',
            'profit' => 'Profit',
            'cr_date' => 'Cr Date',
            'created_by' => 'Created By',
            'given_count' => 'Mijozga berilgan mahsulot soni',
            'is_debtor' => 'Narxda farq?',
            'vozvrat_order_id' => 'Vozvrat Order ID',
            'warehouse_id' => 'Ombor',
        ];
    }
    public function getType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Dona',],
            ['id' => '2', 'type' => 'Karobka',],
            ['id' => '3', 'type' => 'Komplekt',],
            ['id' => '4', 'type' => 'Pochka',],
        ],
        'id', 'type');
    }
    public function getTypeView($id)
    {
        if($id == 1) return 'Dona';
        if($id == 2) return 'Karobka';
        if($id == 3) return 'Komplekt';
        if($id == 4) return 'Pochka';
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
    public function getOrderAccountHistory()
    {
        return $this->hasOne(OrderAccountHistory::className(), ['id' => 'order_account_history_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderAccount()
    {
        return $this->hasOne(OrderAccount::className(), ['id' => 'order_account_id']);
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
    public function getTypeSklad()
    {
        return $this->hasOne(TypeSklad::className(), ['id' => 'type_sklad_id']);
    }

    public function getVozvratOrder()
    {
        return $this->hasOne(VozvratOrder::className(), ['id' => 'vozvrat_order_id']);
    }
}
