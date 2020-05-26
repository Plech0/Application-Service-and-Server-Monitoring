<?php

namespace app\controllers;

use app\models\Dashboard;
use app\models\search\DashboardSearch;
use app\models\search\HistorySearch;
use yii\web\Controller;

class DashboardController extends Controller
{
    public function actionCreate()
    {
        $dashboard = new Dashboard();

        if (\Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post();
            $dashboard->load($data);

            $transaction = \Yii::$app->db->beginTransaction();

            if ($dashboard->save()) {
                $transaction->commit();
                \Yii::$app->session->setFlash('success', "Successfully created dashboard {$dashboard->name}.");
                return $this->redirect('/');
            }
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error',
                "Something went wrong while creating dashboard {$dashboard->name}!!");
        }
        return $this->render('create', [
            'model' => $dashboard,
            'isUpdate' => false,
        ]);
    }

    public function actionDelete($id)
    {
        $dashboardSearch = new DashboardSearch();
        $model = $dashboardSearch->getDashboardById($id);

        $transaction = \Yii::$app->db->beginTransaction();
        if (!$model) {
            \Yii::$app->session->setFlash('error', "Dashboard {$model->name} was not deleted!!");
            $transaction->rollBack();
            return $this->redirect('/');
        }

        if (!$model->delete()) {
            \Yii::$app->session->setFlash('error', "Dashboard {$model->name} was not deleted!!");
            $transaction->rollBack();
            return $this->redirect('/');
        }

        \Yii::$app->session->setFlash('success', "Dashboard {$model->name} successfully deleted.");
        $transaction->commit();
        return $this->redirect('/');
    }

    public function actionUpdate($id)
    {
        $dashboardSearch = new DashboardSearch();
        $dashboard = $dashboardSearch->getDashboardById($id);

        if (\Yii::$app->request->isPost) {

            $data = \Yii::$app->request->post();
            $dashboard->load($data);

            $transaction = \Yii::$app->db->beginTransaction();

            if ($dashboard->save()) {
                $transaction->commit();
                \Yii::$app->session->setFlash('success', "Successfully updated dashboard {$dashboard->name}.");
                return $this->redirect('/');
            }
            $transaction->rollBack();
            \Yii::$app->session->setFlash('error',
                "Something went wrong while updating dashboard {$dashboard->name}!!");
        }

        return $this->render('create', [
            'model' => $dashboard,
            'isUpdate' => true,
        ]);
    }

    public function actionView($id)
    {
        $dashboardSearchModel = new DashboardSearch();
        $dashboard = $dashboardSearchModel->getDashboardById($id);
        $historySearchModel = new HistorySearch();
        $historyDataProvider = $historySearchModel->getServerHistoryDataProvider($dashboard->server_host);

        return $this->render('detail', [
            'model' => $dashboard,
            'historyDataProvider' => $historyDataProvider,
        ]);
    }
}
