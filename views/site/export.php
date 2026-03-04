<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

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
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">Eksport tovarlar tarixi</h4>
            </div>
            <div class="panel-body">
                <div class="row row-space-10">
                    <?php foreach ($warehouse as $model) { ?>
                        <div class="col-md-3">
                            <div class="alert alert-warning  show m-b-10">
                            <i class="fa fa-calendar fa-2x"></i>
                                <a href="#" class="alert-link" style="font-size: 18px; margin-left: 20px"> 20.03.2022</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>