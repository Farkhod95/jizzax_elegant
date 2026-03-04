<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use unclead\multipleinput\MultipleInput;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';

?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">Savdo jarayonida hisob-kitoblar uchun kalkulyator</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                        <div class="col-md-6">
                            <?php echo $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                                'id' => 'my_id',
                                'columns' => [
                                    [
                                        'name'  => 'count',
                                        'title' => 'Soni',
                                        'enableError' => true,
                                        'options' => [
                                            'type' =>'number',
                                            'class' => 'input-priority target',
                                            'headerOptions' => [
                                                'style' => 'font-size: 40px',
                                            ] ,
                                        ]
                                    ],
                                    [
                                        'name'  => 'price',
                                        'title' => 'Narxi',
                                        'enableError' => true,
                                        'options' => [ 
                                            // 'type' =>'number',
                                            'class' => 'input-priority',
                                            'options' => [
                                                'id' => 'price',
                                            ], 
                                            'id' => 'price',
                                            'headerOptions' => [
                                                'id' => 'price',
                                            ] ,
                                            'pluginOptions' => [
                                                'id' => 'price',
                                            ],  
                                            // 'onchange'=>'
                                            //     price = $(this).val();
                                            //     // alert(price);
                                            // '
                                        ]
                                    ], 
                                    [
                                        'name'  => 'summ',
                                        'title' => 'Summa',
                                        'enableError' => true,
                                        'options' => [
                                            'options' => [
                                            ], 
                                            'headerOptions' => [
                                                'style' => 'width: 370px;',
                                            ] ,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                            ],  
                                            'class' => 'input-priority summ',
                                            'disabled' => true
                                        ]
                                    ],            
                                ]
                            ])->label('');?>
                            <?= Html::submitButton('Natijani hisoblash', ['class' => 'btn btn-info calc_res', 'style' => 'width:100%']) ?>
                        </div>
                </div>
                <?php ActiveForm::end(); ?>
                
                <!-- end table-responsive -->
            </div>
            <div class="modal fade" id="modal-dialog-error">
                <div class="modal-dialog">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <h4 class="modal-title">Natija</h4>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                        </div>
                        <div class="modal-body">
                                <div class="form-group row m-b-15 align-items-center">
                                    <label class="col-sm-12 col-form-label"><h3><b >Umumiy summa: <label ><h3><b style="color:red" id="count_porduct"></b ></h3></label> </b></h3></label>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Yopish</a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$(document).on("change", ".input-priority", function(){
    const attr_name = $(this).attr('name');

    let id = attr_name.match(/\d/g)
    id = id.join("");

    const price =  parseFloat($("input[name='Warehouse[allValue][" + id + "][price]']").val());
    const count =  parseInt($("input[name='Warehouse[allValue][" + id + "][count]']").val());

    if(price  && count){
        $("input[name='Warehouse[allValue][" + id + "][summ]']").val(price * count);
    }

});

$(".calc_res").on("click", function(event){
    event.preventDefault()
    let count = 0;

    $(".summ").each(function(input){
        summ = parseFloat($(this).val());

        if(summ){
            count += summ;
        }
    });
    // alert('Natija:' + count);
    $('#count_porduct').text(count);
    $("#modal-dialog-error").modal("toggle");
});

JS) ?>