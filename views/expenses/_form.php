<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Expenses */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expenses-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row"> 
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'nomi')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'type_id')->label()->widget(\kartik\select2\Select2::classname(), [
                    'data' => $model->getTypes(),
                    'options' => [
                        'placeholder' => Yii::t('app','Tanlang...'),],
                    'pluginOptions' => [ 
                        'allowClear' => true
                    ],
                ]); ?> 
            </div>
            
        </div>
        <div class="row"> 
        <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'summa')->textInput(['maxlength' => true, 'type' => 'number']) ?>
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'date_cr')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'value' => date('d.m.Y')],
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose'=>true, 
                        'format' => 'dd.mm.yyyy',
                    ]
                ]);
                ?>  
            </div>
        </div>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
