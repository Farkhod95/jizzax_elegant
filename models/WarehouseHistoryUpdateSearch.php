<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WarehouseHistoryUpdate;

/**
 * WarehouseHistoryUpdateSearch represents the model behind the search form about `app\models\WarehouseHistoryUpdate`.
 */
class WarehouseHistoryUpdateSearch extends WarehouseHistoryUpdate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'brand_id', 'product_category_id', 'count', 'count_update', 'created_by', 'update_by', 'type'], 'integer'],
            [['size'], 'number'],
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
        $query = WarehouseHistoryUpdate::find();

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
            'brand_id' => $this->brand_id,
            'product_category_id' => $this->product_category_id,
            'size' => $this->size,
            'count' => $this->count,
            'count_update' => $this->count_update,
            'cr_date' => $this->cr_date,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
            'type' => $this->type,
        ]);

        return $dataProvider;
    }
}
