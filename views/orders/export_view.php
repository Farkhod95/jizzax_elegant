<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\OrderProducts;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') . 'sanadagi eksport tovarlar tarixi';
$i = 1;
$sumCount = 0;
$allMarkCount = 0
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?=Url::toRoute(['orders/export-order', 'cr_date' => $cr_date])?>" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y')?> sanadagi <?=  $customer_fio ?>ning  buyurtmalari</h4>
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
                            <?php  foreach ($orderProducts as $model) { $i = 1; $allCount = 0;?>
                                <tr>
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($orderProduct = OrderProducts::find()->andWhere(['brand_id' => $model->brand->id])->andWhere(['order_id' => $order_id])->andWhere(['cr_date' => $cr_date])->all() as $model1) { ?>
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