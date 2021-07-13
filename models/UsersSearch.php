<?php

namespace app\models;

use app\util\DatesUtil;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form of `app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'roleidfk', 'children', 'creation_date', 'status'], 'integer'],
            [['internet', 'name', 'surname', 'date_in', 'data_out', 'hour', 'note', 'photo', 'document', 'email'], 'safe'],
            [['salary'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->select([
            "users.*",
            "role_name"=>"roles.name"
        ]);
        $query->innerJoin("roles","users.roleidfk=roles.roleid");


        // grid filtering conditions
        $query->andFilterWhere([
            'userid' => $this->userid,
            'roleidfk' => $this->roleidfk,
            'date_in' => $this->date_in,
            'children' => $this->children,
            'hour' => $this->hour,
            'salary' => $this->salary,
            'creation_date' => $this->creation_date,
            'status' => $this->status,
        ]);



        $query->andFilterWhere(['like', 'internet', $this->internet])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'document', $this->document])
            ->andFilterWhere(['like', 'email', $this->email]);

        if(!empty($this->data_out)){
            $date=DatesUtil::convertDateToSql($this->data_out);

            $query->andWhere("users.data_out=:dateout",[":dateout"=>$date]);

        }


        $query->orderBy("users.name,users.surname");

        return $dataProvider;
    }
}
