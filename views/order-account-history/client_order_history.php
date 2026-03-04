<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\WarehouseHistory;
use kartik\select2\Select2;
use app\models\Client;

$warehouseHistory = WarehouseHistory::find()->all();
$clients = Client::find()->orderBy(['id' => SORT_ASC])->all();
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
                <h4 class="panel-title">Klientlar tarixi</h4>
            </div>
            <div class="panel-body">
                <div class="x_panel">
                    <div class="x_content">
                        <?php $form = ActiveForm::begin(['action' => '/order-account-history/client-product-history',]);?>
                            <div class="form-group">
                                <div class="row"> 
                                    <div class="col-sm-5">
                                        <?= Select2::widget([
                                            'name' => 'client_id',
                                            'id' => 'client_id',
                                            'data' => \yii\helpers\ArrayHelper::map($clients, 'id', 'fio'),
                                            'options' => ['placeholder' => 'Mijoz tanlang','style' => 'text-align:right;',],
                                            'pluginOptions' => [
                                                'allowClear' => true, // This option allows the user to clear the selection
                                            ],
                                        ]); ?>
                                    </div>
                                    <div class="col-md-2 ">
                                        <input style= "font-size: 14px;padding:3px;" type="date" name="start_date">
                                        <label style= "font-size: 14px;padding:4px;">dan</label>
                                    </div>
                                    <div class="col-md-2 ">
                                        <input style= "font-size: 14px;padding:3px;" type="date" name="end_date">
                                        <label style= "font-size: 14px;padding:4px;">gacha</label>
                                    </div>
                                    <div class="col-md-1 ">
                                        <?php echo Html::submitButton(Yii::t('app', 'Qidirish'), ['class' => 'btn btn-primary btn-round']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php ActiveForm::end();?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$("#buy").submit(function(event){
    event.preventDefault();

    let action = $(this).attr("action");

    let payload = {
        customer_name: $('input[name="customer_name"]').val(),
    };

    // console.log(payload.product_details);

    $.ajax({
        url: action,
        data: payload,
        method: "POST", 
    }).done(function(data) {
        $( this ).addClass( "done" );
        alert(data);
    });

    onClick="this.disabled=true; this.value='Sending…';"

});

JS
) ?>