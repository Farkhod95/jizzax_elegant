<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LossOfProfit;

/**
 * LossOfProfitSearch represents the model behind the search form about `app\models\LossOfProfit`.
 */
class LossOfProfitSearch extends LossOfProfit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by'], 'integer'],
            [['profit', 'loss'], 'number'],
            [['date_end', 'date_start'], 'safe'],
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
        $query = LossOfProfit::find();

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
            'profit' => $this->profit,
            'loss' => $this->loss,
            'date_end' => $this->date_end,
            'date_start' => $this->date_start,
            'created_by' => $this->created_by,
        ]);

        return $dataProvider;
    }
}
