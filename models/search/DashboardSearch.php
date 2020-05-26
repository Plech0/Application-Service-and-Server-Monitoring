<?php

namespace app\models\search;


use app\models\Dashboard;
use yii\data\ActiveDataProvider;

class DashboardSearch extends Dashboard
{
    /**
     * Get ActiveDataProvider for all dashboards
     *
     * @return ActiveDataProvider
     */
    public function getDashboardDataProvider()
    {
        return $dataProvider = new ActiveDataProvider([
            'query' => Dashboard::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }

    /**
     * Find dashboard model with given ID
     *
     * @param $id int id of searched dashboard
     *
     * @return \yii\db\ActiveRecord|null
     */
    public function getDashboardById($id)
    {
        return Dashboard::find()->andFilterWhere(['id' => $id])->one();
    }

    /**
     * Find all dashboard models
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllDashboards()
    {
        return Dashboard::find()->all();
    }
}