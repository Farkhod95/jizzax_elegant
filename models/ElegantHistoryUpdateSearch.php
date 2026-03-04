<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ElegantHistoryUpdate;

/**
 * ElegantHistoryUpdateSearch represents the model behind the search form about `app\models\ElegantHistoryUpdate`.
 */
class ElegantHistoryUpdateSearch extends ElegantHistoryUpdate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'status', 'order_account_history_id', 'type'], 'integer'],
            [['comment', 'cr_date', 'title'], 'safe'],
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
        $query = ElegantHistoryUpdate::find()->orderBy(['id' => SORT_DESC]);

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
            'created_by' => $this->created_by,
            'status' => $this->status,
            'type' => $this->type,
            'order_account_history_id' => $this->order_account_history_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);
        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
