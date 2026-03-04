<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PriceProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="price-product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row"> 
        <div class="col-md-6 col-xs-6">
            <?= $form->field($model, 'brand_id')->label()->widget(\kartik\select2\Select2::classname(), [
                'data' => $model->getUpdateBrands(),
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
                    'disabled' => true
                    ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-6 col-xs-6">
            <?= $form->field($model, 'product_category_id')->label()->widget(\kartik\select2\Select2::classname(), [
                'data' => $model->getProductCategories(),
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
                    'disabled' => true
                    ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>
    <div class="row"> 
    <div class="col-md-4 col-xs-4">
            <?= $form->field($model, 'type')->label()->widget(\kartik\select2\Select2::classname(), [
                'data' => $model->getType(),
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
                    'disabled' => true
                    ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-4 col-xs-4">
            <?= $form->field($model, 'size')->textInput(['disabled' => true])->label('O\'lchami <b style="color:red">(Misol uchun: 9.99 )</b>') ?>
        </div>
        <div class="col-md-4 col-xs-4">
            <?= $form->field($model, 'real_price')->textInput(['type' => 'number']) ?>
        </div>
    </div>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
