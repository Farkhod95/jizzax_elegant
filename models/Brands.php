<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "brands".
 *
 * @property int $id
 * @property string $name Name
 */
class Brands extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'brands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 150],
            [['sorting', 'sup_status'], 'integer'],
            [['name', 'sorting'],'required'],
            [['sorting', 'name'] ,'unique'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z0-9\s\-_]+$/',
            'message' => '(, ), [, ], @, #, $, %, +, = kabi belgilarni kiritib bo\'lmaydi!'],
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
            'sorting' => 'Tartibi',
            'sup_status' => "Status"
        ];
    }

        public static function getFreeSorting(int $limit = 10): array
    {
        $rows = self::find()
            ->select('sorting')
            ->orderBy(['sorting' => SORT_ASC])
            ->asArray()
            ->all();

        $used = array_map('intval', array_column($rows, 'sorting'));

        $free = [];
        $i = 1;

        foreach ($used as $s) {
            while ($i < $s) {
                $free[] = $i;              // gap
                if (count($free) >= $limit) return $free;
                $i++;
            }
            if ($i == $s) {
                $i++; // band raqamni o‘tkazib yuboramiz
            }
        }

        // Har doim navbatdagi bo‘sh raqamni ham qo‘shib qo‘yamiz (masalan, max+1)
        $free[] = $i;

        // Takrorlarni olib tashlab, limitlab qaytaramiz
        $free = array_values(array_unique($free));
        return array_slice($free, 0, $limit);
    }
    
    public function getBrands($id)
    {
        $brand = Brands::find()->where(['id' => $id])->one();
        return $brand->name;
    }

    public static function listActive(): array
    {
        return ArrayHelper::map(
            self::find()
                ->where(['sup_status' => 1])
                ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );
    }


    public function getBrandsSizes()
    {
        return $this->hasMany(BrandsSize::className(), ['brand_id' => 'id']);
    }
    
}
