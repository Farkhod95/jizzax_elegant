<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\ProductAccountHistory;
use app\models\DebtRepayment;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0;
$allDebtSum = 0;
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?= Url::to(['/order-account/index']) ?>" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <?php /* if(Yii::$app->user->identity->permission == 1 || Yii::$app->user->identity->permission == 2){?>
                        <a class="btn btn-xs  btn-danger" href="<?= Url::toRoute(['/orders/check', 'id' => $order_id])?>"><i class="fa fa-trash-o"> </i> Buyurtmani bekor qilish</a>
                    <?php }*/?>
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a> -->
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a> -->
                </div>
                <h4 class="panel-title"><?= $customer_fio ?>ning to'langan qarzlar tarixi</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th nowrap>Jami qarz ($)</th>
                                <th nowrap>Dollar kursi</th>
                                <th nowrap>Jami to'langan summa ($)</th>
                                <th nowrap>Qolgan qarz ($)</th>
                                <!-- <th nowrap>Summa dollarda ($)</th>
                                <th nowrap>Summa so'mda</th>
                                <th nowrap>Summa kartada</th> -->
                                <!-- <th nowrap>Izoh</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach ($array_Products as $model) { $i = 1; $allCount = 0;$alltotal_debt = 0;?>
                                <tr>
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b> <?= $model['date'] ?> sanadagi</b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($warehouses = DebtRepayment::find()
                                                    ->where(['client_id' => $model['client_id']])
                                                    ->andWhere(['date' => $model['date']])
                                                    ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
                                                    ->all() as $model1) { ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $model1->total_debt ?></td>
                                        <td><?= $model1->exchange_rate ?></td>
                                        <td><?= $model1->all_summ_dollar ?></td>
                                        <td><?= $model1->total_debt - $model1->all_summ_dollar ?></td>
                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1->all_summ_dollar; $alltotal_debt = $alltotal_debt + ($model1->total_debt - $model1->all_summ_dollar); $allMarkCount = $allMarkCount + $model1->all_summ_dollar; $allDebtSum = $allDebtSum + ($model1->total_debt - $model1->all_summ_dollar);} ?>
                                <tr>
                                    <td colspan="2"  ><b ></b></td>
                                    <td ><b style="color:#000" >Jami:</b></td>
                                    <td ><b style="color:#000"><?= $allCount ?></b></td>
                                    <td  ><b ><?= $alltotal_debt ?></b></td>
                                </tr>
                            <?php  } ?>
                            <tr>
                                <td  style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"></td>
                                <td  style="background-color:#2d353c;"></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allDebtSum ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>
