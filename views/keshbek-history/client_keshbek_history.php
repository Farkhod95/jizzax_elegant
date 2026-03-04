<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\OrderAccountHistory;

$this->title = 'Keshbeklar ro\'yxati';

// Calculate the total sum of keshbek_sum
$totalKeshbekSum = array_sum(array_map(function ($model) {
    return $model->keshbek_sum;
}, $keshbekHistorys));
?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/keshbek-history/client-keshbek" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= $customer_fio?>ning <?= $start_date ?> sanadan <?= $end_date?> sanagacha keshbeklar tarixi</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mijoz</th>
                                <th>Sana</th>
                                <th>Buyurtma summasi ($)</th>
                                <th>Keshbek</th>
                                <th>Keshbek Sum ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($keshbekHistorys as $index => $model): 
                                $orderAccountHistory = OrderAccountHistory::find()->where(['id' => $model->order_account_history_id])->one(); ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $customer_fio ?></td>
                                    <td><?= Yii::$app->formatter->asDate($model->cr_date, 'php:d.m.Y') ?></td>
                                    <td><?= Yii::$app->formatter->asDecimal($orderAccountHistory->all_product_sum, 2) ?></td>
                                    <td><b style="color:green"><?= $model->keshbek ?> %</b></td>
                                    <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($model->keshbek_sum, 2) ?> $</b></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;"><b style="color:#f59c1a;font-size:16px">Jami keshbek sum ($):</b></td>
                                <td ><b style="color:#f59c1a;font-size:16px"><?= Yii::$app->formatter->asDecimal($totalKeshbekSum, 2) ?> $</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
