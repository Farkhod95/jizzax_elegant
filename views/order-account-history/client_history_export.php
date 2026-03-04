<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';

?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/order-account-history/client-history" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= $customer_fio?>ning <?= $start_date ?> sanadan <?= $end_date?> sanagacha buyurtmalar tarixi</h4>
            </div>
            <div class="panel-body">
                <div class="row row-space-10">
                    <?php foreach ($orders as $model) { ?>
                        <div class="col-md-3">
                            <div class="alert alert-warning  show m-b-10">
                            <i class="fa fa-calendar fa-2x"></i>
                                <a href="<?=Url::toRoute(['order-account-history/client-export-view', 'order_id'=> $model->id, 'cr_date'=> $model->cr_date, 'customer_fio'=> $customer_fio, 'client_id'=> $client_id, 'start_date'=> $start_date, 'end_date'=> $end_date])?>" class="alert-link" style="font-size: 18px; margin-left: 20px"> <?= Yii::$app->formatter->asDate($model->cr_date, 'php:d.m.Y') ?></a>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
                <h4><b>Qarz bergan mizojlar:</b></h4>
                <hr/>
                <div class="row row-space-10">
                    <?php foreach ($debtRepayments as $model) { ?>
                        <div class="col-md-3">
                            <div class="alert alert-danger  show m-b-10">
                            <i class="fa fa-calendar fa-2x"></i>
                                <a href="<?=Url::toRoute(['debt-repayment/print-debt', 'id'=> $model->id])?>" class="alert-link" style="font-size: 18px; margin-left: 20px" target ="_blank"> <?= Yii::$app->formatter->asDate($model->date, 'php:d.m.Y') ?></a><br/>
                                <b>To'lagan summa ($): <?=Yii::$app->formatter->asDecimal($model->all_summ_dollar??0, 2)?> $</b>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>