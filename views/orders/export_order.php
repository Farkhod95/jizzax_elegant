<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
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
                    <a href="/orders/export" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') ?> sanadagi buyurtmachilar</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="row row-space-10">
                    <?php foreach ($orders as $model) { ?>
                        <div class="col-md-3">
                            <?php if($model->status == 2){?>
                                <div class="alert alert-danger show m-b-10">
                            <?php }else{?>
                                <div class="alert alert-warning show m-b-10">
                            <?php }?>
                            
                            <i class="fa fa-user fa-2x" style="font-size: 16px; "></i>
                                <a href="<?=Url::toRoute(['orders/export-view', 'order_id'=> $model->id, 'cr_date' => $cr_date, 'customer_fio' => $model->customer_fio])?>" class="alert-link" style="font-size: 14px; margin-left: 10px"> 
                                    <?= $model->customer_fio ?>, <?= date("H:i", strtotime($model->cr_date_time)) ?>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>