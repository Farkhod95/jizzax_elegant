<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\ProductAccountHistory;
use app\models\Orders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0;

?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?= Url::to(['/order-account-history/client-history']) ?>" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
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
                <h4 class="panel-title"><b style="color:#e7cbcb"><?= $customer_fio ?>ning </b ><b >buyurmalar ro'yxati <?= $start_date ?> sanadan <?= $end_date ?> sanagacha bo'lgan vaqtda</b></h4>
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
                                <th nowrap>Model</th>
                                <th nowrap>Nomi</th>
                                <th nowrap>O'lchami</th>
                                <th nowrap>Soni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach ($array_Products as $model) { $i = 1; $allCount = 0;?>
                                <tr>
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b> <?= $model['cr_date'] ?> sanadagi</b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($warehouses = ProductAccountHistory::find()
                                                ->andWhere(['order_account_history_id' => $model['order_account_history_id']])->all() as $model1) { ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $model1->brand->name ?></td>
                                        <td><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></td>
                                        <td><?= $model1->size ?></td>
                                        <td><?= $model1->count ?></td>
                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1->count; $allMarkCount = $allMarkCount + + $model1->count; } ?>
                                <tr>
                                    <td colspan="3"  ><b ></b></td>
                                    <td ><b style="color:#000" >Jami:</b></td>
                                    <td ><b style="color:#000"><?= $allCount ?></b></td>
                                </tr>
                            <?php  } ?>
                            
                         
                            <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
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
