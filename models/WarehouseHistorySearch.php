<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WarehouseHistory;

/**
 * WarehouseHistorySearch represents the model behind the search form about `app\models\WarehouseHistory`.
 */
class WarehouseHistorySearch extends WarehouseHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'sklad_id', 'product_category_id', 'count', 'created_by', 'update_by', 'type'], 'integer'],
            [['size', 'price'], 'number'],
            [['cr_date', 'cr_date_time'], 'safe'],
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
        $query = WarehouseHistory::find();

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
            'brand_id' => $this->brand_id,
            'sklad_id' => $this->sklad_id,
            'product_category_id' => $this->product_category_id,
            'size' => $this->size,
            'count' => $this->count,
            'cr_date' => $this->cr_date,
            'cr_date_time' => $this->cr_date_time,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
            'price' => $this->price,
            'type' => $this->type,
        ]);

        return $dataProvider;
    }
}
