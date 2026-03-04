<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderProducts;

/**
 * OrderProductsSearch represents the model behind the search form about `app\models\OrderProducts`.
 */
class OrderProductsSearch extends OrderProducts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'product_category_id', 'count', 'order_id'], 'integer'],
            [['size'], 'number'],
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
        $query = OrderProducts::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'product_category_id' => $this->product_category_id,
            'size' => $this->size,
            'count' => $this->count,
            'cr_date' => $this->cr_date,
            'order_id' => $this->order_id,
        ]);

        return $dataProvider;
    }
}
