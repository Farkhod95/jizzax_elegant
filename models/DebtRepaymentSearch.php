<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DebtRepayment;

/**
 * DebtRepaymentSearch represents the model behind the search form about `app\models\DebtRepayment`.
 */
class DebtRepaymentSearch extends DebtRepayment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_account_id', 'client_id', 'created_by', 'is_delete', 'is_worker'], 'integer'],
            [['summa', 'total_debt_old', 'sum_som', 'summ_dollar', 'summ_cart', 'sum_transfers', 'total_debt', 'exchange_rate', 'discount_amount', 'all_summ_dollar', 'zdacha_sum', 'zdacha_dollar'], 'number'],
            [['text', 'date', 'cr_date_time'], 'safe'],
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
        $query = DebtRepayment::find()
            ->andWhere([
                'or',
                ['is_delete' => null],
                ['<>', 'is_delete', 1]
            ])
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
            'summa' => $this->summa,
            'is_delete' => $this->is_delete,
            'is_worker' => $this->is_worker,
            'date' => $this->date,
            'order_account_id' => $this->order_account_id,
            'client_id' => $this->client_id,
            'sum_som' => $this->sum_som,
            'summ_dollar' => $this->summ_dollar,
            'summ_cart' => $this->summ_cart,
            'sum_transfers' => $this->sum_transfers,
            'total_debt' => $this->total_debt,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'total_debt_old' => $this->total_debt_old,
            'all_summ_dollar' => $this->all_summ_dollar,
            'created_by' => $this->created_by,
            'zdacha_sum' => $this->zdacha_sum,
            'zdacha_dollar' => $this->zdacha_dollar,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }

    public function searchTrash($params)
    {
        $query = DebtRepayment::find()
            ->where(['is_delete' => 1])
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
            'summa' => $this->summa,
            'is_delete' => $this->is_delete,
            'date' => $this->date,
            'order_account_id' => $this->order_account_id,
            'client_id' => $this->client_id,
            'sum_som' => $this->sum_som,
            'summ_dollar' => $this->summ_dollar,
            'summ_cart' => $this->summ_cart,
            'sum_transfers' => $this->sum_transfers,
            'total_debt' => $this->total_debt,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'total_debt_old' => $this->total_debt_old,
            'all_summ_dollar' => $this->all_summ_dollar,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}