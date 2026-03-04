<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderAccount;

/**
 * OrderAccountSearch represents the model behind the search form about `app\models\OrderAccount`.
 */
class OrderAccountSearch extends OrderAccount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'created_by', 'is_worker'], 'integer'],
            [['date', 'date_last_debt_payment', 'last_order_date', 'cr_date', 'cr_date_time'], 'safe'],
            [['exchange_rate', 'all_product_sum', 'discount_amount', 'all_summ_dollar', 'all_profit_dollar', 'total_debt', 'number_of_orders', 'dollar_sumda', 'sum_som', 'sum_dollar', 'sum_cart', 'sum_transfers'], 'number'],
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
        $query = OrderAccount::find()->where(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ]);

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
            'is_worker' => $this->is_worker,
            'date' => $this->date,
            'cr_date_time' => $this->cr_date_time,
            'exchange_rate' => $this->exchange_rate,
            'all_product_sum' => $this->all_product_sum,
            'discount_amount' => $this->discount_amount,
            'all_summ_dollar' => $this->all_summ_dollar,
            'all_profit_dollar' => $this->all_profit_dollar,
            'total_debt' => $this->total_debt,
            'date_last_debt_payment' => $this->date_last_debt_payment,
            'number_of_orders' => $this->number_of_orders,
            'last_order_date' => $this->last_order_date,
            'sum_som' => $this->sum_som,
            'dollar_sumda' => $this->dollar_sumda,
            'sum_dollar' => $this->sum_dollar,
            'sum_cart' => $this->sum_cart,
            'sum_transfers' => $this->sum_transfers,
            'created_by' => $this->created_by,
            'cr_date' => $this->cr_date,
        ]);

        return $dataProvider;
    }
}
