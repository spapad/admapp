<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeaveSearch represents the model behind the search form about `app\models\Leave`.
 */
class LeaveSearch extends Leave
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'employee', 'type', 'decision_protocol', 'application_protocol', 'duration'], 'integer'],
            [['decision_protocol_date', 'application_protocol_date', 'application_date', 'accompanying_document', 'start_date', 'end_date', 'reason', 'comment', 'create_ts', 'update_ts', 'type'], 'safe'],
            [['extra_reason1', 'extra_reason2', 'extra_reason3', 'extra_reason4', 'extra_reason5', 'extra_reason6', 'extra_reason7', 'extra_reason8', 'extra_reason9', 'extra_reason10'], 'safe'],
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
        $query = Leave::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['start_date' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'employee' => $this->employee,
            'type' => $this->type,
            'decision_protocol' => $this->decision_protocol,
            'decision_protocol_date' => $this->decision_protocol_date,
            'application_protocol' => $this->application_protocol,
            'application_protocol_date' => $this->application_protocol_date,
            'application_date' => $this->application_date,
            'duration' => $this->duration,
            'deleted' => $this->deleted,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'create_ts' => $this->create_ts,
            'update_ts' => $this->update_ts,
        ]);

        $query->andFilterWhere(['like', 'accompanying_document', $this->accompanying_document])
                ->andFilterWhere(['like', 'reason', $this->reason])
                ->andFilterWhere(['like', 'comment', $this->comment]);
        for ($extrareason = 1; $extrareason <= 10; $extrareason++) {
            $field = "extra_reason{$extrareason}";
            $query = $query->andFilterWhere(['like', $field, $this->$field]);
        }

        return $dataProvider;
    }
}
