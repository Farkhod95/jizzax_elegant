<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CheckWarehouse;

/**
 * CheckWarehouseSearch represents the model behind the search form about `app\models\CheckWarehouse`.
 */
class CheckWarehouseSearch extends CheckWarehouse
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'check_id', 'brand_id', 'product_category_id', 'count', 'created_by', 'update_by'], 'integer'],
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
        $query = CheckWarehouse::find();

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
            'check_id' => $this->check_id,
            'brand_id' => $this->brand_id,
            'product_category_id' => $this->product_category_id,
            'size' => $this->size,
            'count' => $this->count,
            'cr_date' => $this->cr_date,
            'created_by' => $this->created_by,
            'update_by' => $this->update_by,
        ]);

        return $dataProvider;
    }
}
