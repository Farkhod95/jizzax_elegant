<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\WarehouseHistory;
use yii\helpers\Url;
/*echo "<pre>";
print_r($post);
echo "</pre>";
die;*/
$identity = Yii::$app->user->identity;

?>
<div class="client-form">
    <div class="box-body">
        <form action="<?=Url::toRoute(['warehouse_history/client'])?>" method="post">
            <div class="form-group row m-b-15">
                <label class="col-sm-4 col-form-label"><h4><b>Xaridor</b></h4></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" required placeholder="" name="customer_name"/>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                <button type="submit"  class="btn btn-danger">Sotishni tasdiqlash</button>
            </div>
        </form>
    </div>
</div>
