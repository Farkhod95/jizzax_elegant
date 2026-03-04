<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sklad;
use yii\db\Expression;

/**
 * SkladSearch represents the model behind the search form about `app\models\Sklad`.
 */
class SkladSearch extends Sklad
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'status', 'consignor_id', 'exchange_rate', 'actived'], 'integer'],
            [['import_product_status'], 'boolean'],
            [['cr_date', 'cr_date_time', 'consignor', 'comment'], 'safe'],
            [['my_total_debt', 'sum_dollar', 'discount_amount', 'given_sum_dollar', 'old_my_total_debt'], 'number'],
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
        $query = Sklad::find()
            ->where(['!=', 'actived', 0])
            ->orderBy([
                // Birinchi status bo'yicha custom tartiblash
                new Expression("CASE 
                    WHEN status = 1 THEN 0 
                    WHEN status = 3 THEN 1 
                    ELSE 2 
                END"),
                'cr_date' => SORT_DESC,      // cr_date bo'yicha yangi sanalar yuqorida
                'cr_date_time' => SORT_DESC,  // cr_date_time bo'yicha yuqoridan pastga
            ]);

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
            'consignor_id' => $this->consignor_id,
            'cr_date' => $this->cr_date,
            'cr_date_time' => $this->cr_date_time,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'consignor' => $this->consignor,
            'my_total_debt' => $this->my_total_debt,
            'old_my_total_debt' => $this->old_my_total_debt,
            'sum_dollar' => $this->sum_dollar,
            'given_sum_dollar' => $this->given_sum_dollar,
            'exchange_rate' => $this->exchange_rate,
            'discount_amount' => $this->discount_amount,
            'import_product_status' => $this->import_product_status,
            'actived' => $this->actived,
        ]);
        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
    public function search2($params)
    {
        $query = Sklad::find()->where(['!=', 'actived', 0]);

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
            'consignor_id' => $this->consignor_id,
            'cr_date' => $this->cr_date,
            'cr_date_time' => $this->cr_date_time,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'consignor' => $this->consignor,
        ]);

        return $dataProvider;
    }
}
