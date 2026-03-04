<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <?php if($check == "check_1"){?>
                <div style="text-align:center"><b style="font-size:18px;color:red"><?= $model->client->fio ?> </b> <b style="font-size:16px;"> ning buyurtmasidagi o\'zgarishlarni tasdiqlaysizmi?</b></div>
                <div><b style="font-size:16px;">Qoldirilgan izoh:</b> <b style="font-size:16px;color:#f59c1a"> <?= $commnet ?></b></div>
            <?php }elseif($check == "check_2"){  ?>

            <?php }elseif($check == "check_3"){?>
            <?php }?>
            <?= $form->field($model, 'comment')->textInput(['value' => 1, 'style' => 'display:none;'])->label("") ?>

        </div>
    </div>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
