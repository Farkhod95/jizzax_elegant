<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MyTotalDebt;

/**
 * MyTotalDebtSearch represents the model behind the search form about `app\models\MyTotalDebt`.
 */
class MyTotalDebtSearch extends MyTotalDebt
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'consignor_id', 'created_by', 'update_by'], 'integer'],
            [['cr_date'], 'safe'],
            [['total_debt'], 'number'],
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
        $query = MyTotalDebt::find();

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
            'cr_date' => $this->cr_date,
            'total_debt' => $this->total_debt,
            'consignor_id' => $this->consignor_id,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
        ]);

        return $dataProvider;
    }
}
