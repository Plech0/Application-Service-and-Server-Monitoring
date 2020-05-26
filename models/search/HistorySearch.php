<?php

namespace app\models\search;


use app\models\Dashboard;
use app\models\History;
use yii\data\ActiveDataProvider;

class HistorySearch extends Dashboard
{
    public function getServerHistoryDataProvider($server)
    {
        $query = History::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $query->andFilterWhere(["server_host" => $server]);
        $query->addOrderBy(["timestamp" => SORT_DESC]);

        return $dataProvider;
    }

    /**
     * @param $host
     * @param $resource
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getLastHistoryByHostAndResource($host, $resource)
    {
        $query = History::find();
        $query->andFilterWhere(['server_host' => $host, 'resource' => $resource]);
        $query->addOrderBy(['timestamp' => SORT_DESC]);
        return $query->one();
    }
}