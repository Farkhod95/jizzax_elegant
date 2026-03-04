<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PriceProduct;

/**
 * PriceProductSearch represents the model behind the search form about `app\models\PriceProduct`.
 */
class PriceProductSearch extends PriceProduct
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'product_category_id', 'type'], 'integer'],
            [['real_price', 'size', 'count'], 'number'],
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
        $query = PriceProduct::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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
            'size' => $this->size,
            'count' => $this->count,
            'type' => $this->type,
            'product_category_id' => $this->product_category_id,
            'real_price' => $this->real_price,
            'cr_date' => $this->cr_date,
        ]);

        return $dataProvider;
    }
}
