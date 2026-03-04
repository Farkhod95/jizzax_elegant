<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\OrderAccountHistory;
use app\models\ProductAccount;
use app\models\OrderAccount;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Top mijzolar ro\'yxati';
$allSumm = 0;
$sumOrder = 0;
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <!-- <a href="/order-account/index" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a> -->
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
                <h4 class="panel-title">Top mijzolar ro'yxati</h4>
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
                                <th nowrap>FIO</th>
                                <th nowrap>Buyurtma soni</th>
                                <th nowrap>Foyda ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  $i = 1; foreach ($orderAccount as $model) {?>
                                <tr>
                                    <td style="width:50px"> <?= $i ?></td>
                                    <td  ><b > <a href="<?=Url::toRoute(['order-account/products', 'id'=> $model->id])?>"><?= $model->client ? $model->client->fio : '—' ?></a></b></td>
                                    <td ><b style="color:#337ab7"><?= $model->number_of_orders ?></b></td>
                                    <td ><b style="color:green"><?= Yii::$app->formatter->asDecimal($model->all_profit_dollar, 2) ?></b></td>
                                </tr>
                            <?php  $i = $i +1; $allSumm = $allSumm + $model->total_debt; $sumOrder = $sumOrder + $model->number_of_orders;} ?>
                            <tr>
                                <td colspan="2" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $sumOrder ?></b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= Yii::$app->formatter->asDecimal($allSumm, 2) ?></b></td>
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
