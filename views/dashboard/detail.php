<?php
/* @var $this yii\web\View */

use app\assets\DashboardAsset;
use app\assets\ReChartAsset;

/* @var $model app\models\Dashboard */
/* @var $historyDataProvider \yii\data\ActiveDataProvider */


DashboardAsset::register($this);
ReChartAsset::register($this);

$this->title = $model->name;
$pageTitle = $model->name;

?>

<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= $pageTitle ?></h1>
            </div>
        </div>
    </div>
</section>

<section class="section" id="graphs">
    <div class="container">
        <div class="row">
            <?php if ($model->cpu): ?>
                <div class="col-md-6">
                    <h3>CPU usage</h3>
                    <div
                            class="line-graph"
                            data-type="pct"
                            data-fieldName="system.cpu.total.norm.pct"
                            data-host="<?= $model->server_host ?>"
                            data-es="<?= $model->elasticsearch_host ?>"
                            data-token="<?= $model->elasticsearch_hash ?>"
                            data-monitoringUnit="CPU"
                    >
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($model->memory): ?>
                <div class="col-md-6">
                    <h3>Memory usage</h3>
                    <div
                            class="line-graph"
                            data-type="pct"
                            data-fieldName="system.memory.actual.used.pct"
                            data-host="<?= $model->server_host ?>"
                            data-es="<?= $model->elasticsearch_host ?>"
                            data-token="<?= $model->elasticsearch_hash ?>"
                            data-monitoringUnit="Memory"
                    >
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($historyDataProvider->totalCount > 0): ?>
                <?php \yii\widgets\Pjax::begin() ?>
                <div class="col-md-12">
                    <table class="table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Time</th>
                            <th scope="col">Resource</th>
                            <th scope="col">Value</th>
                        </tr>
                        </thead>
                        <?= \yii\widgets\ListView::widget([
                            'dataProvider' => $historyDataProvider,
                            'itemView' => function ($model)
                            {
                                $time = new DateTime();
                                $time = $time->setTimestamp($model->timestamp);
                                $time = $time->format("Y-m-d H:i:s");
                                $resource = $model->resource;
                                $value = $model->value;
                                return "<tr><td>${time}</td><td>${resource}</td><td>${value}%</td></tr>";
                            },
                            'layout' => "<tbody>{items}</tbody></table>{pager}",
                        ]) ?>
                </div>
                <?php \yii\widgets\Pjax::end() ?>
            <?php endif; ?>

            <?php if ($model->browsers): ?>
                <div class="col-md-6">
                    <h3>Access from browsers</h3>
                    <div
                            class="pie-graph"
                            data-name="name.keyword"
                            data-host="<?= $model->server_host ?>"
                            data-es="<?= $model->elasticsearch_host ?>"
                            data-vhost="<?= $model->aplication_host ? $model->aplication_host : "" ?>"
                            data-token="<?= $model->elasticsearch_hash ?>"
                    >
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($model->os): ?>
                <div class="col-md-6">
                    <h3>Access from OS</h3>
                    <div
                            class="pie-graph"
                            data-name="os_name.keyword"
                            data-host="<?= $model->server_host ?>"
                            data-es="<?= $model->elasticsearch_host ?>"
                            data-vhost="<?= $model->aplication_host ? $model->aplication_host : "" ?>"
                            data-token="<?= $model->elasticsearch_hash ?>"
                    >
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($model->unique_users): ?>
                <div class="col-md-6">
                    <h3>Unique users count</h3>
                    <div
                            class="unique-graph"
                            data-name="clientip.keyword"
                            data-host="<?= $model->server_host ?>"
                            data-es="<?= $model->elasticsearch_host ?>"
                            data-vhost="<?= $model->aplication_host ? $model->aplication_host : "" ?>"
                            data-token="<?= $model->elasticsearch_hash ?>"
                    >
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" id="messages">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div
                        class="apache-error error"
                        data-host="<?= $model->server_host ?>"
                        data-es="<?= $model->elasticsearch_host ?>"
                        data-vhost="<?= $model->aplication_host ? $model->aplication_host : "" ?>"
                        data-token="<?= $model->elasticsearch_hash ?>"
                >

                </div>

                <div class="aplication-error error">

                </div>
            </div>
        </div>
    </div>
</section>
