<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderAccountHistory;

/**
 * OrderAccountHistorySearch represents the model behind the search form about `app\models\OrderAccountHistory`.
 */
class OrderAccountHistorySearch extends OrderAccountHistory
{
    public $date_from;
    public $date_to;

    public $client_type;

    public function rules()
    {
        return [
            [['id', 'client_id', 'created_by', 'update_status', 'status_order_dukon', 'status_order_sklad', 'is_debt', 'is_delete', 'is_worker', 'large_price', 'fast_order', 'is_debtor', 'client_type'], 'integer'],
            [['order_account_status'], 'boolean'],
            [['date', 'date_last_debt_payment', 'last_order_date', 'cr_date', 'cr_date_time', 'order_commit', 'driver_info', 'date_from', 'date_to'], 'safe'],
            [['exchange_rate', 'all_product_sum', 'discount_amount', 'all_summ_dollar', 'all_profit_dollar', 'total_debt', 'total_debt_today', 'dollar_sumda', 'total_debt_old', 'number_of_orders', 'sum_som', 'sum_dollar', 'sum_cart', 'sum_transfers', 'zdacha_sum', 'zdacha_dollar'], 'number'],
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
        $query = OrderAccountHistory::find()
            ->alias('oah')
            ->joinWith(['client c'])
            ->select([
                'oah.*',
                new \yii\db\Expression("
                    (
                        COUNT(*) OVER (PARTITION BY oah.date)
                        - ROW_NUMBER() OVER (
                            PARTITION BY oah.date
                            ORDER BY
                                CASE WHEN oah.order_account_status = 0 THEN 0 ELSE 1 END ASC,
                                oah.id DESC
                        )
                        + 1
                    ) AS day_seq
                "),
            ])
            ->where([
                'or',
                ['oah.is_delete' => null],
                ['<>', 'oah.is_delete', 1],
            ])
            ->andWhere(['or', ['!=', 'oah.is_worker', 1], ['is', 'oah.is_worker', null]])
            ->orderBy([
                new \yii\db\Expression('CASE WHEN oah.order_account_status = 0 THEN 0 ELSE 1 END'),
                'oah.date' => SORT_DESC,
                'oah.id' => SORT_DESC,
            ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => [
                    'id' => [
                        'default' => SORT_DESC,
                    ],
                ],
            ],
        ]);

        $this->load($params);

        // --- Date range filter (date_from, date_to) ---
        if ($this->date_from) {
            $from = date('Y-m-d', strtotime($this->date_from));
            $query->andWhere(['>=', 'oah.date', $from]);
        }

        if ($this->date_to) {
            $to = date('Y-m-d', strtotime($this->date_to));
            $query->andWhere(['<=', 'oah.date', $to]);
        }

        // ?large_price=1 kabi tekis GET ham qo‘llab-quvvatlanadi
        if (isset($params['large_price']) && $params['large_price'] !== '') {
            $this->large_price = (int)$params['large_price'];
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'oah.id' => $this->id,
            'oah.is_debt' => $this->is_debt,
            'oah.is_debtor' => $this->is_debtor,
            'oah.large_price' => $this->large_price,
            'oah.fast_order' => $this->fast_order,
            'oah.is_delete' => $this->is_delete,
            'oah.is_worker' => $this->is_worker,
            'oah.client_id' => $this->client_id,
            'oah.order_account_status' => $this->order_account_status,
            'oah.date' => $this->date,
            'oah.cr_date_time' => $this->cr_date_time,
            'oah.all_product_sum' => $this->all_product_sum,
            'oah.exchange_rate' => $this->exchange_rate,
            'oah.discount_amount' => $this->discount_amount,
            'oah.all_summ_dollar' => $this->all_summ_dollar,
            'oah.all_profit_dollar' => $this->all_profit_dollar,
            'oah.total_debt' => $this->total_debt,
            'oah.total_debt_today' => $this->total_debt_today,
            'oah.total_debt_old' => $this->total_debt_old,
            'oah.date_last_debt_payment' => $this->date_last_debt_payment,
            'oah.number_of_orders' => $this->number_of_orders,
            'oah.last_order_date' => $this->last_order_date,
            'oah.dollar_sumda' => $this->dollar_sumda,
            'oah.sum_som' => $this->sum_som,
            'oah.sum_dollar' => $this->sum_dollar,
            'oah.sum_cart' => $this->sum_cart,
            'oah.sum_transfers' => $this->sum_transfers,
            'oah.created_by' => $this->created_by,
            'oah.cr_date' => $this->cr_date,
            'oah.update_status' => $this->update_status,
            'oah.status_order_dukon' => $this->status_order_dukon,
            'oah.status_order_sklad' => $this->status_order_sklad,
            'oah.zdacha_sum' => $this->zdacha_sum,
            'oah.zdacha_dollar' => $this->zdacha_dollar,
        ]);

        $query->andFilterWhere(['like', 'oah.order_commit', $this->order_commit]);
        $query->andFilterWhere(['like', 'oah.driver_info', $this->driver_info]);
        $query->andFilterWhere(['c.type' => $this->client_type]);

        return $dataProvider;
    }

    public function searchDeptor($params)
    {
        $query = OrderAccountHistory::find()
            ->where([
                'or',
                ['is_delete' => null],
                ['<>', 'is_delete', 1]
            ])->andWhere(['or',['!=', 'is_worker', 1],['is', 'is_worker', null]])
            ->andWhere(['is_debtor' => 1])
            ->orderBy([
                new \yii\db\Expression('CASE WHEN order_account_status = 0 THEN 0 ELSE 1 END'),
                'date' => SORT_DESC,
                'id' => SORT_DESC,
            ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => [
                    'id' => [
                        'default' => SORT_DESC,
                    ],
                ],
            ],
        ]);

        $this->load($params);

        // ?large_price=1 kabi tekis GET ham qo‘llab-quvvatlanadi
        if (isset($params['large_price']) && $params['large_price'] !== '') {
            $this->large_price = (int)$params['large_price'];
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_debt' => $this->is_debt,
            'is_debtor' => $this->is_debtor,
            'large_price' => $this->large_price,
            'fast_order' => $this->fast_order,
            'is_delete' => $this->is_delete,
            'is_worker' => $this->is_worker,
            'client_id' => $this->client_id,
            'order_account_status' => $this->order_account_status,
            'date' => $this->date,
            'cr_date_time' => $this->cr_date_time,
            'all_product_sum' => $this->all_product_sum,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'all_summ_dollar' => $this->all_summ_dollar,
            'all_profit_dollar' => $this->all_profit_dollar,
            'total_debt' => $this->total_debt,
            'total_debt_today' => $this->total_debt_today,
            'total_debt_old' => $this->total_debt_old,
            'date_last_debt_payment' => $this->date_last_debt_payment,
            'number_of_orders' => $this->number_of_orders,
            'last_order_date' => $this->last_order_date,
            'dollar_sumda' => $this->dollar_sumda,
            'sum_som' => $this->sum_som,
            'sum_dollar' => $this->sum_dollar,
            'sum_cart' => $this->sum_cart,
            'sum_transfers' => $this->sum_transfers,
            'created_by' => $this->created_by,
            'cr_date' => $this->cr_date,
            'update_status' => $this->update_status,
            'status_order_dukon' => $this->status_order_dukon,
            'status_order_sklad' => $this->status_order_sklad,
            'zdacha_sum' => $this->zdacha_sum,
            'zdacha_dollar' => $this->zdacha_dollar,
        ]);

        $query->andFilterWhere(['like', 'order_commit', $this->order_commit]);
        $query->andFilterWhere(['like', 'driver_info', $this->driver_info]);

        return $dataProvider;
    }

     public function searchTrash($params)
    {
        $query = OrderAccountHistory::find()
            ->where(['is_delete' => 1])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->orderBy([
                new \yii\db\Expression('CASE WHEN order_account_status = 0 THEN 0 ELSE 1 END'),
                'date' => SORT_DESC,
                'id' => SORT_DESC,
            ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => [
                    'id' => [
                        'default' => SORT_DESC,
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_debt' => $this->is_debt,
            'is_delete' => $this->is_delete,
            'is_worker' => $this->is_worker,
            'client_id' => $this->client_id,
            'order_account_status' => $this->order_account_status,
            'date' => $this->date,
            'cr_date_time' => $this->cr_date_time,
            'all_product_sum' => $this->all_product_sum,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'all_summ_dollar' => $this->all_summ_dollar,
            'all_profit_dollar' => $this->all_profit_dollar,
            'total_debt' => $this->total_debt,
            'total_debt_today' => $this->total_debt_today,
            'total_debt_old' => $this->total_debt_old,
            'date_last_debt_payment' => $this->date_last_debt_payment,
            'number_of_orders' => $this->number_of_orders,
            'last_order_date' => $this->last_order_date,
            'dollar_sumda' => $this->dollar_sumda,
            'sum_som' => $this->sum_som,
            'sum_dollar' => $this->sum_dollar,
            'sum_cart' => $this->sum_cart,
            'sum_transfers' => $this->sum_transfers,
            'created_by' => $this->created_by,
            'cr_date' => $this->cr_date,
            'update_status' => $this->update_status,
            'status_order_dukon' => $this->status_order_dukon,
            'status_order_sklad' => $this->status_order_sklad,
        ]);

        $query->andFilterWhere(['like', 'order_commit', $this->order_commit]);

        return $dataProvider;
    }

    public function search2($params, $start_date, $end_date)
    {
        $query = OrderAccountHistory::find()
            ->where(['between', 'date', $start_date, $end_date])
            ->andWhere(['or',
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
            'all_product_sum' => $this->all_product_sum,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'all_summ_dollar' => $this->all_summ_dollar,
            'all_profit_dollar' => $this->all_profit_dollar,
            'total_debt' => $this->total_debt,
            'date_last_debt_payment' => $this->date_last_debt_payment,
            'number_of_orders' => $this->number_of_orders,
            'last_order_date' => $this->last_order_date,
            'sum_som' => $this->sum_som,
            'sum_dollar' => $this->sum_dollar,
            'sum_cart' => $this->sum_cart,
            'sum_transfers' => $this->sum_transfers,
            'created_by' => $this->created_by,
            'cr_date' => $this->cr_date,
        ]);

        return $dataProvider;
    }
}
