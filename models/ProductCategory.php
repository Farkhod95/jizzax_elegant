<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "product_category".
 *
 * @property int $id
 * @property string $name Name
 */
class ProductCategory extends \yii\db\ActiveRecord
{
    public $allValue = [];
    public static function tableName()
    {
        return 'product_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['brand_id', 'sorting', 'sup_status'], 'integer'],

            // brand_id ham majburiy bo‘lsin:
            [['brand_id', 'name', 'sorting'], 'required'],

            // Har bir brand ichida NAME unikal
            [['name', 'brand_id'], 'unique',
                'targetAttribute' => ['name', 'brand_id'],
                'message' => 'Bu nom ushbu brand uchun allaqachon mavjud.'],

            // Har bir brand ichida SORTING unikal
            [['sorting', 'brand_id'], 'unique',
                'targetAttribute' => ['sorting', 'brand_id'],
                'message' => 'Bu tartib raqami ushbu brand uchun allaqachon mavjud.'],

            // Tegishli brend mavjudligini tekshirish
            [['brand_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Brands::className(),
                'targetAttribute' => ['brand_id' => 'id']],

            // Ruxsat etilgan belgilar
            [['name'], 'match',
                'pattern' => '/^[a-zA-Z0-9\s\-_]+$/',
                'message' => '(, ), [, ], @, #, $, %, +, = kabi belgilarni kiritib bo\'lmaydi!'],
            ['allValue', 'required', 'message' => 'Hech bo‘lmaganda bitta o‘lcham kiriting.'],
            ['allValue', 'validateAllValue'],
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
            'brand_id' => 'Model',
            'sorting' => 'Tartibi',
            'sup_status' => 'Holati'
        ];
    }

    public function getBrand()
    {
        return $this->hasOne(Brands::className(), ['id' => 'brand_id']);
    }

    public function validateAllValue($attribute)
    {
        $list = $this->$attribute;
        if (empty($list) || !is_array($list)) {
            $this->addError($attribute, "Hech bo‘lmaganda bitta o‘lcham kiriting.");
            return;
        }

        // Har xil formatlarni normalize qilamiz:
        // A) ['size' => [2,4,5]]  B) [['size'=>2],['size'=>4]]  C) [2,4,5]
        if (isset($list['size']) && is_array($list['size'])) {
            $list = array_map(function ($v) {
                return ['size' => $v];
            }, $list['size']);
        }

        $ok = 0; $rowNum = 0;
        foreach ($list as $row) {
            $rowNum++;
            $val = is_array($row) ? ($row['size'] ?? null) : $row;
            $val = is_string($val) ? str_replace(',', '.', trim($val)) : $val;

            if ($val === null || $val === '') {
                $this->addError($attribute, "№{$rowNum} qatorda o‘lcham kiritilmagan.");
                continue;
            }
            if (!is_numeric($val)) {
                $this->addError($attribute, "№{$rowNum} qatorda o‘lcham noto‘g‘ri formatda (faqat raqam).");
                continue;
            }
            $ok++;
        }

        if ($ok === 0) {
            $this->addError($attribute, "Kamida bitta to‘g‘ri o‘lcham kiriting.");
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouses()
    {
        return $this->hasMany(Warehouse::className(), ['product_category_id' => 'id']);
    }

    public function getBrands()
    {
        return ArrayHelper::map(Brands::find()->all(), 'id', 'name');
    }

    public function getProductCategorys()
    {
        return ArrayHelper::map(ProductCategory::find()->all(), 'id', 'name');
    }

    public function getProductCategory($id)
    {
        $brand = ProductCategory::find()->where(['id' => $id])->one();
        return $brand->name;
    }

    public function getBrandsSizes()
    {
        return $this->hasMany(BrandsSize::className(), ['product_category_id' => 'id']);
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

    public static function listActiveByBrand(int $brandId): array
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()
                ->where(['brand_id' => $brandId, 'sup_status' => 1])
                ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );
    }
}
