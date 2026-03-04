<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\OrderAccountHistory;
use app\models\ProductAccountHistory;
use app\models\OrderAccount;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';
$orderAccount = OrderAccountHistory::find()->where(['id' => $order_id])->one();
$orderAccount2 = OrderAccount::find()->where(['client_id' => $orderAccount->client_id])->one();
$allMarkCount = 0;
$allMarkGivenCount = 0;
$allMarkSumm = 0;
$allMarkRealSumm = 0;
$allMarkProfitSumm = 0;
CrudAsset::register($this);
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/order-account-history/<?= $type?>" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="<?= Url::to(['/order-account-history/print2', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-warning"> <i class="fa fa-download"></i> Chop qilish Xodim uchun </a>
                    <?php if(Yii::$app->user->identity->permission == 1){?>
                        <a href="<?= Url::to(['/order-account-history/print-sklad', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-primary"> <i class="fa fa-download"></i> Chop qilish Sklad uchun</a>
                        
                    <?php }?>
                    <?php
                        $userPermission = Yii::$app->user->identity->permission;
                        $isPartner = $orderAccount->client->type;

                        if (($isPartner == 3 && $userPermission == 1) || ($isPartner != 3 && in_array($userPermission, [1, 2, 5, 6]))) {
                        ?>
                            <a href="<?= Url::to(['/order-account-history/print', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-warning"> <i class="fa fa-download"></i> Chop qilish Mijoz uchun</a>
                    <?php } ?>
                     <?php if(Yii::$app->user->identity->permission == 1){?>
                        <a href="<?= Url::to(['/order-account-history/print-profit', 'id' => $order_id]) ?>" target ="_blank" class="btn btn-xs  btn-danger"> <i class="fa fa-download"></i> Chop qilish Admin uchun</a>
                        
                    <?php }?>
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
                <h4 class="panel-title"><b style="color:#dfdfdf; font-size:16px"><?= $orderAccount->client->fio ?>ning <?= date('d.m.Y H:i', strtotime($orderAccount->cr_date_time));?> </b>sanadagi buyurmalar ro'yxati</h4>
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
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_transfers,2) ?>  $</b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; width: 150px;color:#f59c1a">To'langan summa ($):</th>
                                <td ><b style="color:#f59c1a" ><?= Yii::$app->formatter->asDecimal($orderAccount->all_summ_dollar, 2) ?> $ </b></td>
                                
                                <td style="width: 10px;"></td>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa so'mda:</th>
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($orderAccount->sum_som, 2) ?></b></td>
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
                                <th nowrap style="text-align:left;color:#474ba0">Qaytim:</th>
                                <td>
                                    <b style="color:#474ba0">
                                        <?= Yii::$app->formatter->asDecimal(($orderAccount->zdacha_sum?$orderAccount->zdacha_sum:''), 0) ?>
                                        (<?= Yii::$app->formatter->asDecimal(($orderAccount->zdacha_dollar?$orderAccount->zdacha_dollar:''), 2) ?> $)
                                    </b>
                                </td>
                            </tr>
                            <tr>                            
                        </table>
                    <?php }?>
                    <?php Pjax::begin([
                                'id' => 'crud-datatable-pjax',
                                'timeout' => 0,
                                'enablePushState' => false,
                            ]); ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th nowrap>Joyi</th>
                                <th nowrap>Model</th>
                                <th nowrap>Nomi</th>
                                <th nowrap>O'lchami</th>
                                <th nowrap>Tip</th>
                                <th nowrap>Soni</th>
                                <th nowrap>Berilgan soni</th>
                                <?php if(Yii::$app->user->identity->permission == 1){?>
                                <th nowrap>Narxi ($)</th>
                                <th nowrap>Asl Narxi ($)</th>
                                <th nowrap>Foyda ($)</th>
                                <?php }?>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0; $allGivenCount = 0; $allSumm = 0; $allRealSumm = 0;  $allProfitSumm = 0;?>
                                <tr>
                                    <?php if(Yii::$app->user->identity->permission == 1){?>
                                        <td colspan="10" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                        <td style="background-color:#a0d9ea;"></td>
                                    <?php }else{?>
                                        <td colspan="7" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                        <td style="background-color:#a0d9ea;"></td>
                                    <?php }?>
                                </tr>
                                <?php  foreach ($warehouses = ProductAccountHistory::find()
                                                ->andWhere(['brand_id' => $model->brand->id])
                                                ->andWhere(['order_account_history_id' => $order_id])->all() as $model1) { 
                                    $largePrice = $model1->price < $model1->real_price ?  '<i class="fa fa-exclamation-triangle" style="color: orange; font-size: 22px; cursor: pointer;" data-toggle="tooltip" title="Mahsulot narxi Asl narxidan kam"></i>':'';                
                                    ?>
                                    <tr >
                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td  > <?= $i ?> <?= ($model1->is_debtor == 1) ? '.' : '.'?></td>
                                        <?php }else{?>
                                            <td > <?= $i ?> <?= ($model1->is_debtor == 1) ? '.' : '.'?></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td ><b ><?= $model1->typeSklad->name ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->typeSklad->name ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td > <b ><?= $model1->brand->name ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->brand->name ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td > <b ><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td > <b ><?= $model1->size ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->size ?></b></td>
                                        <?php }?>

                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td ><b > <?= $model1->getTypeView($model1->type ) ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->getTypeView($model1->type) ?></b></td>
                                        <?php }?>
                                        
                                        <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td > <b ><?= $model1->count ?></b></td>
                                        <?php }else{?>
                                            <td> <b ><?= $model1->count ?></b></td>
                                        <?php }?>
                                        <?php if($model1->type_sklad_id ==1){ 
                                            if ($model1->count == $model1->given_count) {
                                                $colorCount = 'green';
                                            }else {
                                                $colorCount = 'red';
                                            }
                                            ?> 
                                            <td >
                                                    <?= Html::a(
                                                        '<b > '.$model1->given_count.'</b> <i class="fa fa-cubes"></i>',
                                                        ['/product-account-history/update-count', 'id' => $model1->id, 'order_id' => $model1->order_account_history_id],
                                                        [
                                                            'role' => 'modal-remote',              // AjaxCrud modal trigger
                                                            'data-toggle' => 'tooltip',
                                                            'title' => 'Tahrirlash',
                                                            'class' => 'price-link',               // butun katak bosiladigan bo‘ladi
                                                            'data-pjax' => 0,
                                                            'style' => 'color:'. $colorCount .'; font-weight:bold;font-size:18px'
                                                        ]
                                                    ) ?>
                                                </td>
                                        <?php }else{
                                             if ($model1->count == $model1->given_count) {
                                                $colorCount = 'green';
                                                }else {
                                                    $colorCount = 'red';
                                                }
                                            ?>
                                            
                                            <td >
                                                    <?= Html::a(
                                                        '<b > '.$model1->given_count.'</b> <i class="fa fa-cubes"></i>',
                                                        ['/product-account-history/update-count', 'id' => $model1->id],
                                                        [
                                                            'role' => 'modal-remote',              // AjaxCrud modal trigger
                                                            'data-toggle' => 'tooltip',
                                                            'title' => 'Tahrirlash',
                                                            'class' => 'price-link',               // butun katak bosiladigan bo‘ladi
                                                            'data-pjax' => 0,
                                                            'style' => 'color:'. $colorCount .'; font-weight:bold;font-size:18px'
                                                        ]
                                                    ) ?>
                                                </td>
                                        <?php }?>
                                        <?php if(Yii::$app->user->identity->permission == 1){?>
                                            <?php if($model1->type_sklad_id ==1){ ?> 
                                            <td style="color:<?= ($model1->price < $model1->real_price) ? '#e70f0fff;font-size: 15px;' : ''?>"> <b > <?= $model1->price ?> <?= $largePrice ?></b></td>
                                            <td > <b ><?= $model1->real_price ?></b></td>
                                            <td > <b style="color:<?= ($model1->profit >0) ? 'green' :'red' ?>;font-size: 16px"><?= $model1->profit ?></b></td>
                                            <?php }else{?>
                                                <td > <b style="color:<?= ($model1->price < $model1->real_price) ? '#e70f0fff;font-size: 15px' : ''?>">  <?= $model1->price ?> <?= $largePrice ?></b></td>
                                                <td> <b ><?= $model1->real_price ?></b></td>
                                                <td> <b style="color:<?= ($model1->profit >0) ? 'green' :'red' ?>;font-size: 16px"><?= $model1->profit ?></b></td>
                                            <?php }?>
                                        <?php }?>
                                        
                                        
                                    </tr>
                                <?php  $i = $i +1 ; 
                                    $allCount = $allCount + $model1->count; 
                                    $allGivenCount = $allGivenCount + $model1->given_count; 
                                    $allSumm = $allSumm + ($model1->count  * $model1->price); 
                                    $allRealSumm = $allRealSumm + ($model1->count  * $model1->real_price); 
                                    $allProfitSumm = $allProfitSumm + $model1->profit; 
                                    $allMarkCount = $allMarkCount + $model1->count; 
                                    $allMarkGivenCount = $allMarkGivenCount + $model1->given_count; 
                                    $allMarkSumm = $allMarkSumm + ($model1->count  * $model1->price); 
                                    $allMarkRealSumm = $allMarkRealSumm + ($model1->count  * $model1->real_price); 
                                    $allMarkProfitSumm = $allMarkProfitSumm + $model1->profit; 
                                    } ?>
                                <tr>
                                    <?php if(Yii::$app->user->identity->permission == 1){?>
                                        <td colspan="5"  ><b ></b></td>
                                        <td ><b style="color:#000;font-size: 15px" >Jami:</b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allCount ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allGivenCount ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allSumm ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allRealSumm ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allProfitSumm ?></b></td>
                                    <?php }else{?>
                                        <td colspan="4"  ><b ></b></td>
                                        <td ><b style="color:#000;font-size: 15px" >Jami:</b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allCount ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"><?= $allGivenCount ?></b></td>
                                        <td ><b style="color:#000;font-size: 15px"></b></td>
                                    <?php }?>

                                    
                                </tr>
                            <?php  } ?>
                            
                         
                            <tr>
                                <?php if(Yii::$app->user->identity->permission == 1){?>
                                    <td colspan="6" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkGivenCount ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkSumm ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkRealSumm ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkProfitSumm ?></b></td>
                                <?php }else{?>
                                    <td colspan="6" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkGivenCount ?></b></td>
                                <?php }?>
                                
                            </tr>
                        </tbody>
                    </table>
                    <?php Pjax::end(); ?>
                    
                </div>
                <!-- end table-responsive -->
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
