<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Warehouse;

/**
 * WarehouseSearch represents the model behind the search form about `app\models\Warehouse`.
 */
class WarehouseSearch extends Warehouse
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'product_category_id', 'count', 'created_by', 'update_by', 'type'], 'integer'],
            [['status_count'], 'boolean'],
            [['size', 'price', 'all_sum_dollar', 'all_discount_amount', 'all_my_total_debt', 'worker_price'], 'number'],
            [['cr_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Warehouse::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // MUHIM: shu grid uchun alohida page/sort param nomlari
            'pagination' => [
                'pageSize'      => 20,              // xohlagancha o'zgartirishingiz mumkin
                'pageParam'     => 'warehouse-page',
                'pageSizeParam' => 'warehouse-per-page',
            ],
            'sort' => [
                'defaultOrder' => [
                    'brand_id'            => SORT_ASC,
                    'product_category_id' => SORT_ASC,
                    'id'                  => SORT_ASC, // barqaror tartib bo‘lsin
                ],
                'sortParam' => 'warehouse-sort',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'                  => $this->id,
            'brand_id'            => $this->brand_id,
            'price'               => $this->price,
            'product_category_id' => $this->product_category_id,
            'size'                => $this->size,
            'count'               => $this->count,
            'cr_date'             => $this->cr_date,
            'created_by'          => $this->created_by,
            'update_by'           => $this->update_by,
            'type'                => $this->type,
            'all_sum_dollar'      => $this->all_sum_dollar,
            'all_discount_amount' => $this->all_discount_amount,
            'all_my_total_debt'   => $this->all_my_total_debt,
            'worker_price'        => $this->worker_price,
            'status_count'        => $this->status_count,
        ]);

        return $dataProvider;
    }
}
