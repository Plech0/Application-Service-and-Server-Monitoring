<?php
/* @var $this \yii\web\View */

use app\models\search\DashboardSearch;
use yii\bootstrap\Html;

$dashboards = DashboardSearch::getAllDashboards();

?>

<header>
    <section class="section header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar">
                        <div class="logo-wrapper">
                            <a href="/" class="logo"><?= Html::img('img/monitoring-logo.svg') ?></a></div>
                        <a href="/">Home</a>
                        <div class="dropdown">
                            <button class="dropbtn">Dashboards
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">
                                <?php foreach ($dashboards as $dashboard):
                                    $id = $dashboard->id ?>
                                    <a href=<?= "?r=dashboard/view&id=${id}" ?>><?= $dashboard->name ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</header>

