<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$this->title = 'Buyurtmalar va qarzlar';
$rows = $provider->getModels();


?>
<style>
/* Jadval fonini to‘liq ko‘k qilish */
.table {
    background-color: #d0e7ff !important; /* Asosiy jadval foni */
    color: #000 !important;
    border-color: #a7c6e6 !important;
}

/* Sarlavhalar (thead) */
.table thead {
    background-color:rgb(167, 201, 238) !important;
    color: #000 !important;
}

.table thead th {
    background-color:rgb(169, 198, 228) !important;
    color: #000 !important;
    font-weight: bold !important;
}

/* Oddiy satrlar */
.table tbody tr {
    background-color: #d0e7ff !important;
}

/* Har ikkinchi satr (zebra) */
.table tbody tr:nth-child(even) {
    background-color: #c0dcff !important;
}

/* Sana bloklari (colspan=9) */
.table tbody tr td[colspan="9"] {
    background-color:rgb(185, 217, 231) !important;
    color: #000 !important;
    font-weight: bold !important;
}

/* Kataklar */
.table td, .table th {
    font-size: 14px !important;
    vertical-align: middle !important;
    padding: 8px !important;
    border-color:rgb(165, 179, 179) !important;
}

/* Holat uchun ranglar */
.table tbody tr td b.red {
    color: #d9534f !important;
}

.table tbody tr td b.green {
    color: #5cb85c !important;
}
</style>




<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Buyurtmalar va qarzlar</h4>
            </div>
            <div class="panel-body" style="background-color:rgb(210, 226, 245);">

                <div class="row row-space-12">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => Url::to(['order-account-history/order-and-debt']),
                    'options' => ['class' => 'form-inline', 'style' => 'margin-bottom: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end;']
                ]); ?>

                    <div class="form-group" style="margin-right:10px;">
                        <?= Select2::widget([
                            'name' => 'customer_name',
                            'id' => 'myselect',
                            'data' => ArrayHelper::map($clients, 'fio', 'fio'),
                            'value' => $selectedFio,
                            'options' => ['placeholder' => 'Mijoz tanlang'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'width' => '250px',
                            ],
                        ]); ?>
                    </div>

                    <div class="form-group" style="margin-right:10px;">
                        <?= Html::input('date', 'start_date', $startDate, ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <?= Html::input('date', 'end_date', $endDate, ['class' => 'form-control']) ?>
                    </div>

                    <?= Html::submitButton('Qidirish', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Tozalash', ['order-account-history/order-and-debt'], ['class' => 'btn btn-default']) ?>

                    <div class="form-group" style="margin-left:auto;">
                        <?= Html::a('<i class="fa fa-file-pdf-o"></i> PDF', [
                            'order-account-history/export-pdf',
                            'customer_name' => $selectedFio,
                            'start_date' => $startDate,
                            'end_date' => $endDate
                        ], ['class' => 'btn btn-danger', 'target' => '_blank']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" >
                            <thead>
                                <tr>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">#</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Mijoz</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Hodim</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">To‘lanadigan summa ($)</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">To‘langan summa ($)</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">To‘langan qarz ($)</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Foyda ($)</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Umumiy qolgan qarz ($)</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Vaqti</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px">Holati</th>
                                    <th style="color:rgb(187, 25, 25) !important;font-weight: bold;font-size:14px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grouped = [];
                                foreach ($rows as $row) {
                                    $dateKey = date('Y-m-d', strtotime($row['datetime']));
                                    $grouped[$dateKey][] = $row;
                                }

                                foreach ($grouped as $date => $groupRows):
                                    $counter = 1;
                                    $sum_all_product = 0;
                                    $sum_paid = 0;
                                    $sum_paid_debt = 0;
                                    $sum_total_debt = 0;
                                    
                                    $sum_paid_som = 0;
                                    $sum_paid_cart = 0;
                                    $sum_paid_transfers = 0;

                                    $sum_paid_debt_som = 0;
                                    $sum_paid_debt_cart = 0;
                                    $sum_paid_debt_transfers = 0;
                                    $sum_profit = 0;
                                ?>
                                    <tr>
                                        <td colspan="11" style="color:rgb(187, 25, 25) !important; font-weight: bold;font-size:14px">
                                            <?= date('d.m.Y', strtotime($date)) ?>
                                        </td>
                                    </tr>

                                    <?php foreach ($groupRows as $row): ?>
                                        <tr>
                                            <td style="font-weight: bold;font-size:14px"><?= $counter++ ?></td>
                                            <td style="font-weight: bold;font-size:14px"><b><?= Html::encode($row['client_name']) ?></b></td>
                                            <td style="font-weight: bold;font-size:14px">
                                                <?= Html::encode($row['created_by_name'] ?? '-') ?>
                                            </td>
                                            <td style="font-weight: bold;font-size:14px"><?= number_format($row['all_product_sum'], 2) ?></td>
                                            <!-- <td style="font-weight: bold;font-size:14px">< ?= number_format($row['all_summ_dollar'], 2) ?></td> -->
                                            <td style="font-weight: bold; font-size:14px; min-width:200px;">
                                                <?php if($row['action'] === 'Buyurtma qilgan'){?>
                                                    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                                                        <span ><?= number_format($row['all_summ_dollar'] ?? 0, 2) ?></span>
                                                        <!-- (
                                                            <span style="color:rgb(184, 27, 22);"><i class="fa fa-usd"></i> <?= number_format($row['sum_transfers'] ?? 0, 2) ?></span>
                                                            <span style="color: #333;"><i class="fa fa-money"></i> <?= number_format($row['sum_som'] ?? 0, 2) ?></span>
                                                            <span style="color: #007bff;"><i class="fa fa-credit-card"></i> <?= number_format($row['sum_cart'] ?? 0, 2) ?></span>
                                                        ) -->
                                                    </div>
                                                <?php }?>
                                            </td>

                                            <!-- <td style="font-weight: bold;font-size:14px">< ?= number_format($row['paid_debt'], 2) ?></td> -->
                                            <td style="font-weight: bold; font-size:14px; min-width:200px;">
                                                <?php if($row['action'] === 'Qarz to‘lagan'){?>
                                                    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                                                        <span ><?= number_format($row['paid_debt'] ?? 0, 2) ?></span>
                                                        <!-- (<span style="color:rgb(184, 27, 22);"><i class="fa fa-usd"></i> <?= number_format($row['debt_sum_transfers'] ?? 0, 2) ?></span>
                                                        <span style="color: #333;"><i class="fa fa-money"></i> <?= number_format($row['debt_sum_som'] ?? 0, 2) ?> </span>
                                                        <span style="color: #007bff;"><i class="fa fa-credit-card"> </i> <?= number_format($row['debt_summ_cart'] ?? 0, 2) ?></span>) -->
                                                        
                                                    </div>
                                                <?php }?>
                                            </td>
                                            <td style="font-weight: bold;font-size:14px">
                                                <?= number_format($row['all_profit_dollar'] ?? 0, 2) ?>
                                            </td>
                                            <td style="font-weight: bold;font-size:14px"><?= number_format($row['total_debt'], 2) ?></td>
                                            <td style="font-weight: bold;font-size:14px"><?= date('d.m.Y H:i', strtotime($row['datetime'])) ?></td>
                                            <!-- <td style="font-weight: bold;font-size:14px"><?= date('Y-m-d', strtotime($row['datetime'])) ?></td> -->
                                            <td style="color: <?= $row['action'] === 'Qarz to‘lagan' ? 'red' : 'green' ?>">
                                                <b><?= Html::encode($row['action']) ?></b>
                                            </td>
                                            <td>
                                                <?= Html::a('<span class="glyphicon glyphicon-print"></span>', [
                                                    $row['action'] === 'Buyurtma qilgan' ? '/order-account-history/print' : '/debt-repayment/print-debt',
                                                    'id' => $row['id']
                                                ], [
                                                    'class' => 'btn btn-warning btn-xs',
                                                    'role' => 'modal-remote',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Chop qilish',
                                                    'target' => '_blank'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <?php
                                            $sum_all_product += $row['all_product_sum'];
                                            
                                            $sum_paid += $row['all_summ_dollar'];
                                            $sum_paid_som += $row['sum_som'];
                                            $sum_paid_cart += $row['sum_cart'];
                                            $sum_paid_transfers += $row['sum_transfers'];
                                            
                                            $sum_paid_debt += $row['paid_debt'];
                                            $sum_paid_debt_som += $row['debt_sum_som'];
                                            $sum_paid_debt_cart += $row['debt_summ_cart'];
                                            $sum_paid_debt_transfers += $row['debt_sum_transfers'];
                                            $sum_profit += ($row['all_profit_dollar'] ?? 0);

                                            $sum_total_debt += $row['total_debt'];
                                        ?>
                                    <?php endforeach; ?>

                                    <tr style="color: #348fe2; font-weight: bold;font-size:14px">
                                        <td colspan="3" class="text-right">Umumiy summa:</td>
                                        <td><?= number_format($sum_all_product, 2) ?></td>
                                        <!-- <td>< ?= number_format($sum_paid, 2) ?></td> -->
                                        <td style="font-weight: bold; font-size:14px; min-width:200px;">
                                            <div style="display:flex; flex-direction:column; gap:6px;">
                                                <span style="color: green; font-weight:600;">
                                                    Jami ($): <?= number_format($sum_paid ?? 0, 2) ?>
                                                </span>

                                                <div style="font-size:12px; color:#666; display:flex; flex-direction:column; gap:4px; padding-left:12px;">
                                                    <span style="color: rgb(184, 27, 22);">
                                                    Dollar ($): <?= number_format($sum_paid_transfers ?? 0, 2) ?>
                                                    </span>

                                                    <span style="color: #333;">
                                                    Naqt:  <?= number_format($sum_paid_som ?? 0, 2) ?>
                                                    </span>

                                                    <span style="color: #007bff;">
                                                    Karta: <?= number_format($sum_paid_cart ?? 0, 2) ?>
                                                    </span>
                                                </div>
                                            </div>

                                        </td>
                                        <!-- <td>< ?= number_format($sum_paid_debt, 2) ?></td> -->
                                         <td style="font-weight: bold; font-size:15px; min-width:200px;">
                                            <div style="display:flex; flex-direction:column; gap:6px;">
                                                <span style="color: green; font-weight:600;">
                                                    Jami ($): <?= number_format($sum_paid_debt ?? 0, 2) ?>
                                                </span>

                                                <div style="font-size:12px; color:#666; display:flex; flex-direction:column; gap:4px; padding-left:12px; ">
                                                    <span style="color: rgb(184, 27, 22);"> Dollar ($): <?= number_format($sum_paid_debt_transfers ?? 0, 2) ?> </span>

                                                    <span style="color: #333;"> Naqt: <?= number_format($sum_paid_debt_som ?? 0, 2) ?></span>

                                                    <span style="color: #007bff;"> Karta: <?= number_format($sum_paid_debt_cart ?? 0, 2) ?></span>
                                                </div>
                                                </div>
                                        </td>
                                        <td><?= number_format($sum_profit, 2) ?></td>
                                        <td><?= number_format($sum_total_debt, 2) ?></td>
                                        <td colspan="4"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="text-center">
                        <?= LinkPager::widget([
                            'pagination' => $provider->getPagination(),
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
