<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row"> 
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => "+\9\98##-###-##-##",'options' => ['placeholder' => '+99800-000-00-00','class'=>'form-control',]]) ?> 
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'region_id')->label()->widget(\kartik\select2\Select2::classname(), [
                    'data' => $model->getRegions(),
                    'options' => [
                        'placeholder' => Yii::t('app','Tanlang...'),
                        'onchange'=>'
                            $.post( "/client/districts?id='.'"+$(this).val(), function( data ){
                                $( "select#districts_id" ).html( data);
                            });' 
                        ],
                    'pluginOptions' => [ 
                        'allowClear' => true
                    ],
                ]); ?> 
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'district_id')->label()->widget(\kartik\select2\Select2::classname(), [
                    'data' => $model->getDistricts($model->region_id),
                    'options' => [
                        'placeholder' => Yii::t('app','Tanlang...'),
                        'id' => 'districts_id',
                        ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-6 col-xs-6">
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
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'keshbek')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-12 col-xs-12">
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <?php if(\Yii::$app->user->identity->permission == 1){?>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'is_profit_loss')->checkbox([
                    // 'label' => Yii::t('app', 'Hamkor hisoblanadimi?'),
                    'uncheck' => 0,
                    'checked' => $model->is_profit_loss == 1
                ]) ?>
            </div>
            <?php }?>
        </div>
    

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
