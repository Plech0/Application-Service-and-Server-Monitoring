<?php

/* @var $this yii\web\View */

/* @var $dashboardDataProvider \yii\data\ActiveDataProvider */

use app\assets\HomepageAsset;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

HomepageAsset::register($this);


$this->title = 'Servers dashboard';
?>


<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="add-dashboard-btn">
                    <a href="?r=dashboard/create" class="btn-new-dashboard btn btn-primary">New
                        Dashboard<?= Html::img('img/ic-add.svg'); ?></a>
                </div>
            </div>
            <div class="col-md-12">
                <?= GridView::widget([
                    'dataProvider' => $dashboardDataProvider,
                    'columns' => [
                        'name',
                        [
                            'attribute' => 'memory',
                            'format'=>'html',
                            'value' => function ($model)
                            {
                                if ($model->memory){
                                    return Html::img('img/toggle_on-24px.svg');
                                }
                                return Html::img('img/toggle_off-24px.svg');
                            },
                        ],
                        [
                            'attribute' => 'cpu',
                            'format'=>'html',
                            'value' => function ($model)
                            {
                                if ($model->cpu){
                                    return Html::img('img/toggle_on-24px.svg');
                                }
                                return Html::img('img/toggle_off-24px.svg');
                            },
                        ],
                        [
                            'attribute' => 'os',
                            'format'=>'html',
                            'value' => function ($model)
                            {
                                if ($model->os){
                                    return Html::img('img/toggle_on-24px.svg');
                                }
                                return Html::img('img/toggle_off-24px.svg');
                            },
                        ],
                        [
                            'attribute' => 'browsers',
                            'format'=>'html',
                            'value' => function ($model)
                            {
                                if ($model->browsers){
                                    return Html::img('img/toggle_on-24px.svg');
                                }
                                return Html::img('img/toggle_off-24px.svg');
                            },
                        ],
                        [
                            'attribute' => 'unique_users',
                            'format'=>'html',
                            'value' => function ($model)
                            {
                                if ($model->unique_users){
                                    return Html::img('img/toggle_on-24px.svg');
                                }
                                return Html::img('img/toggle_off-24px.svg');
                            },
                        ],
                        [
                            'class' => ActionColumn::class,
                            'template' => '{view} {update} {delete}',
                            'urlCreator' => function ($action, $model)
                            {
                                if ($action === 'view') {
                                    $url = '?r=dashboard/view&id=' . $model->id;
                                    return $url;
                                }

                                if ($action === 'update') {
                                    $url = '?r=dashboard/update&id=' . $model->id;
                                    return $url;
                                }
                                if ($action === 'delete') {
                                    $url = '?r=dashboard/delete&id=' . $model->id;
                                    return $url;
                                }
                                return "";
                            },
                        ],
                    ],
                    'layout' => "{items}\n<div class='grid-pagination'>{pager}{summary}</div>",
                ]) ?>
            </div>
        </div>
    </div>
</section>




