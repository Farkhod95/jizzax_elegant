<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\PriceProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="price-product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row"> 
        <div class="col-md-6 col-xs-6">

            <?= $form->field($model, 'brand_id')->widget(Select2::classname(), [
                        'data' => $model->getBrands(),
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'options' => ['placeholder' => Yii::t('app','Tanlang...'),],
                        'pluginOptions' => [
                            'allowClear' => true,
                            // 'minimumInputLength' => 10
                    ],
            ]);?>
        </div>
        <div class="col-md-6 col-xs-6">
            <?= $form->field($model, 'product_category_id')->label()->widget(\kartik\select2\Select2::classname(), [
                'data' => $model->getProductCategories(),
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
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
                    ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-4 col-xs-4">
            <?= $form->field($model, 'size')->textInput(['value' => 0])->label('O\'lchami <b style="color:red">(Misol uchun: 9.99 )</b>') ?>
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
