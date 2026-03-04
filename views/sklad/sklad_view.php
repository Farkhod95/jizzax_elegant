<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\WarehouseHistory;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') .' sanadagi import tovarlar tarixi';
$i = 1;
$sumCount = 0;
$allMarkCount = 0;
$allMarkPrice = 0;
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/sklad/index" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="<?= Url::to(['/sklad/print', 'id' => $id]) ?>" target ="_blank" class="btn btn-xs  btn-warning"> <i class="fa fa-download"></i> Chop qilish Mijoz uchun</a>
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') ?> sanadagi <b style="font-size:14px"><?= $model->consignor0->name?></b>dan import qilingan tovarlar tarixi</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
                <h5>Hisobot</h5>
                <?php if(Yii::$app->user->identity->permission == 1){?>
                    <table class="table">   
                        <tr>
                            <th nowrap style="text-align: left; width: 40%; color:#f59c1a">Dollar kursi:</th>
                            <td ><b style="color:#f59c1a"><?= $model->exchange_rate ?></b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align: left; color:#f59c1a">Jami to'lanadigan summa ($):</th>
                            <td ><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($model->given_sum_dollar, 2) ?> $</b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align: left;color:#f59c1a">To'langan summa ($):</th>
                            <td ><b style="color:#f59c1a" ><?= Yii::$app->formatter->asDecimal($model->sum_dollar, 2) ?> $ </b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align: left; color:#f59c1a">Chegirma ($):</th>
                            <td  ><b style="color:#f59c1a"><?=  Yii::$app->formatter->asDecimal($model->discount_amount, 2) ?>  $</b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align: left;color:red">Qolgan umumiy qarzim ($):</th>
                            <td ><b style="color:red"><?=  Yii::$app->formatter->asDecimal($myTotalDebt->total_debt, 2) ?> $</b></td>
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
                                <th nowrap>Narxi ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0; $allPrice = 0;?>
                                <tr>
                                    <td colspan="5" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($warehouses = WarehouseHistory::find()->andWhere(['brand_id' => $model->brand->id])->andWhere(['sklad_id' => $id])->all() as $model1) { ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $model1->brand->name ?></td>
                                        <td><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></td>
                                        <td><?= $model1->size ?></td>
                                        <td><?= $model1->count ?></td>
                                        <td><?= $model1->price ?></td>
                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1->count; $allPrice = $allPrice + $model1->price; $allMarkCount = $allMarkCount + $model1->count; $allMarkPrice = $allMarkPrice + $model1->price;  } ?>
                                <tr>
                                    <td colspan="3"  ><b ></b></td>
                                    <td ><b style="color:#000" >Jami:</b></td>
                                    <td ><b style="color:#000"><?= $allCount ?></b></td>
                                    <td ><b style="color:#000"><?= $allPrice ?></b></td>
                                </tr>
                            <?php  } ?>
                            
                         
                            <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkPrice ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                <!-- end table-responsive -->
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>
