<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;

/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id', 'district_id', 'total_debt', 'is_worker', 'created_by', 'is_partner', 'type', 'is_profit_loss'], 'integer'],
            [['keshbek'], 'number'],
            [['fio', 'phone', 'address'], 'safe'],
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
        $query = Client::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
            'defaultOrder' => ['id' => SORT_DESC], // Qo'shilgan tartibda ko'rsatish uchun
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
            'is_worker' => $this->is_worker,
            'is_partner' => $this->is_partner,
            'type' => $this->type,
            'is_profit_loss' => $this->is_profit_loss,
            'created_by' => $this->created_by,
            'region_id' => $this->region_id,
            'district_id' => $this->district_id,
            'total_debt' => $this->total_debt,
            'keshbek' => $this->keshbek,
        ]);

        $query->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
