<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductAccountHistory;

/**
 * ProductAccountHistorySearch represents the model behind the search form about `app\models\ProductAccountHistory`.
 */
class ProductAccountHistorySearch extends ProductAccountHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_account_id', 'order_account_history_id', 'brand_id', 'product_category_id', 'count', 'type', 'type_sklad_id', 'created_by', 'given_count', 'is_debtor', 'vozvrat_order_id', 'warehouse_id'], 'integer'],
            [['size', 'price', 'real_price', 'profit'], 'number'],
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
        $query = ProductAccountHistory::find();

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
            'order_account_id' => $this->order_account_id,
            'order_account_history_id' => $this->order_account_history_id,
            'brand_id' => $this->brand_id,
            'product_category_id' => $this->product_category_id,
            'size' => $this->size,
            'count' => $this->count,
            'type' => $this->type,
            'price' => $this->price,
            'real_price' => $this->real_price,
            'type_sklad_id' => $this->type_sklad_id,
            'profit' => $this->profit,
            'cr_date' => $this->cr_date,
            'created_by' => $this->created_by,
            'given_count' => $this->given_count,
            'is_debtor' => $this->is_debtor,
            'vozvrat_order_id' => $this->vozvrat_order_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        return $dataProvider;
    }
}
