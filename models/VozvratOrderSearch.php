<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\VozvratOrder;

/**
 * VozvratOrderSearch represents the model behind the search form about `app\models\VozvratOrder`.
 */
class VozvratOrderSearch extends VozvratOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'created_by', 'update_status'], 'integer'],
            [['date', 'confirmation', 'cr_date_time', 'comment'], 'safe'],
            [['exchange_rate', 'discount_amount', 'sum_dollar', 'sum_som', 'sum_cart', 'all_summ_dollar', 'total_debt', 'old_total_debt', 'large_price', 'is_delete', 'product_summ_dollar'], 'number'],
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
        $query = VozvratOrder::find();

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
            'client_id' => $this->client_id,
            'date' => $this->date,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'sum_dollar' => $this->sum_dollar,
            'sum_som' => $this->sum_som,
            'sum_cart' => $this->sum_cart,
            'all_summ_dollar' => $this->all_summ_dollar,
            'total_debt' => $this->total_debt,
            'old_total_debt' => $this->old_total_debt,
            'product_summ_dollar' => $this->product_summ_dollar,
            'cr_date_time' => $this->cr_date_time,
            'created_by' => $this->created_by,
            'large_price' => $this->large_price,
            'is_delete' => $this->is_delete,
            'update_status' => $this->update_status,
        ]);

        $query->andFilterWhere(['like', 'confirmation', $this->confirmation])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
