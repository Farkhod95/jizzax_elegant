<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\KeshbekHistory;

/**
 * KeshbekHistorySearch represents the model behind the search form about `app\models\KeshbekHistory`.
 */
class KeshbekHistorySearch extends KeshbekHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'order_account_history_id'], 'integer'],
            [['keshbek', 'keshbek_sum'], 'number'],
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
        $query = KeshbekHistory::find();

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
            'keshbek' => $this->keshbek,
            'keshbek_sum' => $this->keshbek_sum,
            'cr_date' => $this->cr_date,
            'client_id' => $this->client_id,
            'order_account_history_id' => $this->order_account_history_id,
        ]);

        return $dataProvider;
    }
}
