<?php

namespace Matodor\RegistryConstructor\models\search;

use Matodor\RegistryConstructor\models\Table as ParentModel;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class TableSearch extends ParentModel
{
    /**
     * @param $params
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function search($params)
    {
        $query = ParentModel::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);

        $this->scenario = static::SCENARIO_SEARCH;
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);
        return $dataProvider;
    }
}
