<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\OrderAccountHistory;
use app\models\ProductAccountHistory;
use app\models\OrderAccount;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') . 'sanadagi eksport tovarlar tarixi';
$orderAccount = OrderAccountHistory::find()->andWhere(['id' => $order_id])->andWhere(['cr_date' => $cr_date])->one();
$orderAccount2 = OrderAccount::find()->where(['client_id' => $orderAccount->client_id])->one();
$i = 1;
$sumCount = 0;
$allMarkCount = 0;

if ($link == "client-product-history") {
    $urlView = Url::toRoute(['order-account-history/'.$link]);
}else{
    $urlView = Url::toRoute(['order-account-history/'.$link, 'cr_date' => $cr_date]);
}

?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=Url::toRoute(['order-account-history/export-order', 'cr_date' => $cr_date])?>" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="<?= Url::to(['/order-account-history/print2', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-warning"> <i class="fa fa-download"></i> Chop qilish Xodim uchun </a>
                    <?php if(Yii::$app->user->identity->permission == 1 || Yii::$app->user->identity->permission == 5|| Yii::$app->user->identity->permission == 2|| Yii::$app->user->identity->permission == 5|| Yii::$app->user->identity->permission == 6){?>
                    <a href="<?= Url::to(['/order-account-history/print-date-client', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-warning"> <i class="fa fa-download"></i> Chop qilish Mijoz uchun</a>
                    <?php }?>
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a> -->
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y')?> sanadagi <?=  $customer_fio ?>ning  buyurtmalari</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
                <div class="table-responsive">
                <h5>Hisobot</h5>
                    <?php if(Yii::$app->user->identity->permission == 1){?>
                        <table class="table">   
                            <tr>
                                <th nowrap style="text-align: left; width: 300px; color:#f59c1a">Dollar kursi:</th>
                                <td ><b style="color:#f59c1a"><?= $orderAccount->exchange_rate ?></b></td>
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left; color:#474ba0">  </th>
                                <td ><b style="color:#474ba0"> </b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; width: 150px; color:#f59c1a">Jami to'lanadigan summa ($):</th>
                                <td ><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($orderAccount->all_product_sum, 2) ?> $</b></td>
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left; color:#474ba0">To'langan summa dollarda ($):</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_dollar,2) ?>  $</b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; width: 150px;color:#f59c1a">To'langan summa ($):</th>
                                <td ><b style="color:#f59c1a" ><?= Yii::$app->formatter->asDecimal($orderAccount->all_summ_dollar, 2) ?> $ </b></td>
                                
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa so'mda:</th>
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_som, 2) ?> </b><b style="color:#f59c1a"> (<?= Yii::$app->formatter->asDecimal($orderAccount->dollar_sumda,2) ?>$) </b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; width: 200px; color:#f59c1a">Chegirma ($):</th>
                                <td  ><b style="color:#f59c1a"><?= $orderAccount->discount_amount?>  $</b></td>
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left; color:#474ba0">To'langan summa kartada:</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_cart, 2)?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; width: 150px;color:#f59c1a">Qolgan qarz ($):</th>
                                <td ><b style="color:#f59c1a"><?=  Yii::$app->formatter->asDecimal($orderAccount->total_debt, 2) ?> $</b></td>
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa transferda:</th>
                                <td   ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_transfers,2)?></b></td>
                            </tr>
                            <tr>                            
                        </table>
                    <?php }?>
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
                            <?php  foreach ($orderProducts as $model) { $i = 1; $allCount = 0;?>
                                <tr>
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['brand_id' => $model->brand->id])->andWhere(['order_account_history_id' => $order_id])->andWhere(['cr_date' => $cr_date])->all() as $model1) { ?>
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