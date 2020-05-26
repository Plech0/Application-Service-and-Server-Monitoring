<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\Dashboard */

/* @var $isUpdate boolean */


use app\assets\DashboardAsset;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

DashboardAsset::register($this);

$this->title = $isUpdate ? 'Update dashboard' : 'Create new dashboard';
$pageTitle = $isUpdate ? "Update dashboard {$model->name}" : "Create new dashboard";
$btnContent = $isUpdate ? "Update" : "Create";
$id_dashboard = $model->id;

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
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <?php $form = ActiveForm::begin([
                    'id' => 'dashboard-create-form',
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-12\">{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-12 control-label'],
                    ],
                ]); ?>


                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Dashboard name']) ?>

                <?= $form->field($model, 'aplication_host')->textInput(['placeholder' => 'app-host.cz']) ?>

                <?= $form->field($model, 'server_host')->textInput(['placeholder' => 'server-host.cz']) ?>

                <?= $form->field($model, 'elasticsearch_host')
                         ->textInput(['placeholder' => 'http://elasticsearch-host:9200']) ?>

                <div class="form-group has-success">
                    <label class="col-lg-12 control-label">Elasticsearch Name</label>
                    <div class="col-lg-6">
                        <input type="text" id="esName" class="form-control">
                    </div>
                    <div class="col-lg-12"><p class="help-block help-block-error"></p></div>
                </div>

                <div class="form-group has-success">
                    <label class="col-lg-12 control-label">Elasticsearch Password</label>
                    <div class="col-lg-6">
                        <input type="text" id="esPswd" class="form-control">
                    </div>
                    <div class="col-lg-12"><p class="help-block help-block-error"></p></div>
                </div>

                <div class="hidden">
                    <?= $form->field($model, 'elasticsearch_hash')->hiddenInput([
                        'id' => 'esHash',
                    ])->label("") ?>
                </div>

                <div id="radio-btns">
                    <?= $form->field($model, 'cpu')->widget(SwitchInput::class, [
                        'type' => SwitchInput::CHECKBOX,
                    ]); ?>

                    <?= $form->field($model, 'memory')->widget(SwitchInput::class, [
                        'type' => SwitchInput::CHECKBOX,
                    ]); ?>

                    <?= $form->field($model, 'os')->widget(SwitchInput::class, [
                        'type' => SwitchInput::CHECKBOX,
                    ]); ?>

                    <?= $form->field($model, 'browsers')->widget(SwitchInput::class, [
                        'type' => SwitchInput::CHECKBOX,
                    ]); ?>

                    <?= $form->field($model, 'unique_users')->widget(SwitchInput::class, [
                        'type' => SwitchInput::CHECKBOX,
                    ]); ?>
                </div>
                <?= Html::button($btnContent, [
                    'type' => 'submit',
                    'form' => 'dashboard-create-form',
                    'class' => 'btn btn-primary',
                ]) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</section>
