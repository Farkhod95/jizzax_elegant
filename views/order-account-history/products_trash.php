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

$this->title = 'Mahsulotlar ro\'yxati';
$orderAccount = OrderAccountHistory::find()->where(['id' => $order_id])->one();
$orderAccount2 = OrderAccount::find()->where(['client_id' => $orderAccount->client_id])->one();
$allMarkCount = 0;
$allMarkSumm = 0;
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/order-account-history/trash-o" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    
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
                <h4 class="panel-title"><?= $orderAccount->client->fio ?>ning <?= $orderAccount->date?> sanadagi buyurmalar ro'yxati</h4>
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
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_som, 2) ?></b><b style="color:#f59c1a"> (<?= Yii::$app->formatter->asDecimal($orderAccount->dollar_sumda,2)?> $)</b></td>
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
                                <td ><b style="color:#f59c1a"><?=  Yii::$app->formatter->asDecimal($orderAccount2->total_debt, 2) ?> $</b></td>
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
                                <th nowrap>Joyi</th>
                                <th nowrap>Tip</th>
                                <th nowrap>Model</th>
                                <th nowrap>Nomi</th>
                                <th nowrap>O'lchami</th>
                                <th nowrap>Soni</th>
                                <?php if(Yii::$app->user->identity->permission == 1){?>
                                <th nowrap>Narxi ($)</th>
                                <?php }?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0; $allSumm = 0;?>
                                <tr>
                                    <?php if(Yii::$app->user->identity->permission == 1){?>
                                        <td colspan="7" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                        <td style="background-color:#a0d9ea;"></td>
                                    <?php }else{?>
                                        <td colspan="6" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                        <td style="background-color:#a0d9ea;"></td>
                                    <?php }?>
                                </tr>
                                <?php  foreach ($warehouses = ProductAccountHistory::find()
                                                ->andWhere(['brand_id' => $model->brand->id])
                                                ->andWhere(['order_account_history_id' => $order_id])->all() as $model1) { ?>
                                    <tr>
                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <?= $i ?></td>
                                        <?php }else{?>
                                            <td> <?= $i ?></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"><b ><?= $model1->typeSklad->name ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->typeSklad->name ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"><b > <?= $model1->getTypeView($model1->type ) ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->getTypeView($model1->type) ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <b ><?= $model1->brand->name ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->brand->name ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <b ><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <b ><?= $model1->size ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->size ?></b></td>
                                        <?php }?>
                                        
                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <b ><?= $model1->count ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->count ?></b></td>
                                        <?php }?>
                                        <?php if(Yii::$app->user->identity->permission == 1){?>
                                            <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="background-color:#67a38569"> <b ><?= $model1->price ?></b></td>
                                            <?php }else{?>
                                                <td> <b ><?= $model1->price ?></b></td>
                                            <?php }?>
                                        <?php }?>
                                        
                                    </tr>
                                <?php  $i = $i +1 ; 
                                    $allCount = $allCount + $model1->count; 
                                    $allSumm = $allSumm + ($model1->count  * $model1->price); 
                                    $allMarkCount = $allMarkCount + $model1->count; 
                                    $allMarkSumm = $allMarkSumm + ($model1->count  * $model1->price); 
                                    } ?>
                                <tr>
                                    <?php if(Yii::$app->user->identity->permission == 1){?>
                                        <td colspan="5"  ><b ></b></td>
                                        <td ><b style="color:#000" >Jami:</b></td>
                                        <td ><b style="color:#000"><?= $allCount ?></b></td>
                                        <td ><b style="color:#000"><?= $allSumm ?></b></td>
                                    <?php }else{?>
                                        <td colspan="5"  ><b ></b></td>
                                        <td ><b style="color:#000" >Jami:</b></td>
                                        <td ><b style="color:#000"><?= $allCount ?></b></td>
                                    <?php }?>

                                    
                                </tr>
                            <?php  } ?>
                            
                         
                            <tr>
                                <?php if(Yii::$app->user->identity->permission == 1){?>
                                    <td colspan="6" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkSumm ?></b></td>
                                <?php }else{?>
                                    <td colspan="6" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                <?php }?>
                                
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
