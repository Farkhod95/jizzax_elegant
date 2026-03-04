<?php

namespace app\models\searchs;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form about `app\models\Users`.
 */
class UsersSearch extends Users
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'permission', 'status', 'referal_id'], 'integer'],
            [['username', 'address', 'email', 'password', 'avatar', 'surname', 'name', 'middle_name', 'phone', 'last_seen', 'access_token', 'registry_date'], 'safe'],
            [['email_verified', 'phone_verified'], 'boolean'],
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
        $query = Users::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $dataProvider->sort->attributes['name'] = [
            'asc' => ['surname' => SORT_ASC,'name' => SORT_ASC ,'middle_name' => SORT_ASC],
            'desc' => ['surname' => SORT_DESC,'name' => SORT_DESC ,'middle_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'permission' => $this->permission,
            'status' => $this->status,
            'last_seen' => $this->last_seen,
            'registry_date' => $this->registry_date,
            'email_verified' => $this->email_verified,
            'phone_verified' => $this->phone_verified,
            'referal_id' => $this->referal_id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'access_token', $this->access_token]);

        return $dataProvider;
    }
}
