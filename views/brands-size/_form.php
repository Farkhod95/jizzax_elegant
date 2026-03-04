<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
/* @var $this yii\web\View */
/* @var $model app\models\BrandsSize */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-category-form">

    <?php $form = ActiveForm::begin(); ?>
		<div class="row">
		    <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'brand_id')->label()->widget(\kartik\select2\Select2::classname(), [
                    'data' => $model->getBrands(),
                    'options' => [
                        'placeholder' => Yii::t('app','Tanlang...'),
                        'onchange'=>'
                            $.post( "/brands-size/products-category?id='.'"+$(this).val(), function( data ){
                                $( "select#products_category_id" ).html( data);
                            });' 
                        ],
                    'pluginOptions' => [ 
                        'allowClear' => true
                    ],
                ]); ?> 
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'product_category_id')->label()->widget(\kartik\select2\Select2::classname(), [
                    'data' => $model->getProductCategoryId($model->brand_id),
                    'options' => [
                        'placeholder' => Yii::t('app','Tanlang...'),
                        'id' => 'products_category_id',
                        ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-6">
				<?= $form->field($model, 'size')->textInput(['maxlength' => true]) ?>
			</div>	
			<div class="col-md-6">
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
		</div>			
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>