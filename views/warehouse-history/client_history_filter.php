<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\Orders;
/*echo "<pre>";
print_r($post);
echo "</pre>";
die;*/
$identity = Yii::$app->user->identity;

?>
<div class="client-form">
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-4">
                    <label>Davr</label>
                    <?= Html::dropDownList('year', ['class' => 'form-control'] ) ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <?= Html::submitButton('Saralash', ['class' => 'btn btn-info', 'style' => 'margin-top:25px;']) ?>
                    </div>  
                </div>                
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
