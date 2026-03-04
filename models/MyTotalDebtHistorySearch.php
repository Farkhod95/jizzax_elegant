<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MyTotalDebtHistory;

/**
 * MyTotalDebtHistorySearch represents the model behind the search form about `app\models\MyTotalDebtHistory`.
 */
class MyTotalDebtHistorySearch extends MyTotalDebtHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'my_total_debt_id', 'created_by', 'update_by'], 'integer'],
            [['cr_date'], 'safe'],
            [['total_debt', 'discount_amount', 'exchange_rate', 'all_summ_dollar'], 'number'],
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
        $query = MyTotalDebtHistory::find();

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
            'cr_date' => $this->cr_date,
            'total_debt' => $this->total_debt,
            'discount_amount' => $this->discount_amount,
            'my_total_debt_id' => $this->my_total_debt_id,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
            'exchange_rate' => $this->exchange_rate,
            'all_summ_dollar' => $this->all_summ_dollar,
        ]);

        return $dataProvider;
    }
}
