<?php


namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "brands_size".
 *
 * @property int $id
 * @property double $size Size
 * @property int $brand_id Product category
 * @property int $product_category_id Product category
 *
 * @property Brands $brand
 * @property ProductCategory $productCategory
 */
class BrandsSize extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brands_size';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['size'], 'number'],
            [['brand_id', 'product_category_id', 'type'], 'integer'],
            [['brand_id', 'product_category_id', 'size'], 'required'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brands::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['product_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductCategory::className(), 'targetAttribute' => ['product_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'size' => 'O\'lcham',
            'brand_id' => 'Model',
            'product_category_id' => 'Nomi',
            'type' => 'Tip',
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
    public function getProductCategory()
    {
        return $this->hasOne(ProductCategory::className(), ['id' => 'product_category_id']);
    }

    public function getProductCategorys()
    {
        return ArrayHelper::map(ProductCategory::find()->all(), 'id', 'name');
    }


    public function getBrands()
    {
        return ArrayHelper::map(Brands::find()->all(), 'id', 'name');
    }

    public function getProductCategoryId($id)
    {
        return ArrayHelper::map(ProductCategory::find()->where(['brand_id' => $id])->all(), 'id', 'name');
    }
    public static function getType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Dona',],
            ['id' => '2', 'type' => 'Karobka',],
            ['id' => '3', 'type' => 'Komplekt',],
            ['id' => '4', 'type' => 'Pochka',],
        ],
        'id', 'type');
    }

    public function getTypeNameView($name)
    {
        if($name == 'Dona') return 1;
        if($name == 'Karobka') return 2;
        if($name == 'Komplekt') return 3;
        if($name == 'Pochka') return 4;
    }
    
    public static function getTypeView($id)
    {
        $id = (int)$id;
        if ($id === 1) return 'Dona';
        if ($id === 2) return 'Karobka';
        if ($id === 3) return 'Komplekt';
        if ($id === 4) return 'Pochka';
        return '—';
    }
}
