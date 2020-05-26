<?php

namespace app\controllers;

use app\models\Dashboard;
use app\models\search\DashboardSearch;
use app\models\search\GraphSearch;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dashboardSearch = new DashboardSearch();
        $dashboardDataProvider = $dashboardSearch->getDashboardDataProvider();
        return $this->render('index', [
            'dashboardDataProvider' => $dashboardDataProvider,
        ]);
    }
}
