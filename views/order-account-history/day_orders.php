<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;

$this->title = 'Mahsulotlar ro\'yxati';

// Agar sizda $searchModel yo‘q bo‘lsa, oddiy DynamicModel qilamiz:
if (!isset($searchModel)) {
    $searchModel = new \yii\base\DynamicModel(['date_from', 'date_to']);
    $searchModel->date_from = $start_date;
    $searchModel->date_to   = $end_date;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">
                        <i class="fa fa-expand"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse">
                        <i class="fa fa-minus"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <h4 class="panel-title">
                    Sotilgan tovarlar tarixi : <?= Html::encode($start_date) ?> dan <?= Html::encode($end_date) ?> gacha
                </h4>
            </div>

            <div class="panel-body">
                <div class="x_panel">
                    <div class="x_content">

                        <?php $form = ActiveForm::begin([
                            'action' => Url::to(['/order-account-history/day-orders-history']),
                            'method' => 'post',
                        ]); ?>

                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($searchModel, 'date_from')->widget(DatePicker::class, [
                                    'type' => DatePicker::TYPE_INPUT,
                                    'options' => [
                                        'placeholder' => 'Sanadan',
                                        'name' => 'start_date', // POST: start_date bo‘lib ketsin
                                    ],
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                    ],
                                ])->label(false); ?>
                            </div>

                            <div class="col-md-3">
                                <?= $form->field($searchModel, 'date_to')->widget(DatePicker::class, [
                                    'type' => DatePicker::TYPE_INPUT,
                                    'options' => [
                                        'placeholder' => 'Sanagacha',
                                        'name' => 'end_date', // POST: end_date bo‘lib ketsin
                                    ],
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                    ],
                                ])->label(false); ?>
                            </div>

                            <div class="col-md-2">
                                <?= Html::submitButton('Qidirish', ['class' => 'btn btn-primary btn-round', 'style' => '']) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>

                <div class="row row-space-10">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="background-color:#90e6e6;"><b>#</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Model</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Nomi</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>O'lchami</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Soni</b></th>
                        </tr>
                        </thead>
                        <tbody id="showRes"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
