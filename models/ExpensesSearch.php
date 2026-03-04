<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Expenses;

/**
 * ExpensesSearch represents the model behind the search form about `app\models\Expenses`.
 */
class ExpensesSearch extends Expenses
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'loss_of_profit_id'], 'integer'],
            [['nomi', 'date_cr'], 'safe'],
            [['summa'], 'number'],
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
        $query = Expenses::find();

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
            'date_cr' => $this->date_cr,
            'type_id' => $this->type_id,
            'loss_of_profit_id' => $this->loss_of_profit_id,
        ]);

        $query->andFilterWhere(['like', 'nomi', $this->nomi]);

        return $dataProvider;
    }

    public function search2($params, $start_date, $end_date)
    {
        $query = Expenses::find()->where(['between', 'date_cr', $start_date, $end_date]);

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
            'date_cr' => $this->date_cr,
            'type_id' => $this->type_id,
            'loss_of_profit_id' => $this->loss_of_profit_id,
        ]);

        $query->andFilterWhere(['like', 'nomi', $this->nomi]);

        return $dataProvider;
    }
}
