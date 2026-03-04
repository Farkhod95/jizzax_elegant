<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
use app\models\ExchangeRate;
/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Tovar qo\'shish';
$this->params['breadcrumbs'][] = $this->title;
$exchangeRate = ExchangeRate::findOne(1);
?>

<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/sklad/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Tovar qo'shish</h4>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'consignor_id')->label()->widget(\kartik\select2\Select2::classname(), [
                        'data' => $model->getConsignor(),
                        'options' => [
                            'placeholder' => Yii::t('app','Tanlang...'),
                            'required' => true,
                        ],
                        'pluginOptions' => [
                            'tags' => true,
                            'allowClear' => true,],
                    ])->label('Yuk jo\'natuvchi <b style="color:red">(Kiritilish majburiy)</b>'); ?> 
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'cr_date')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose'=>true, 
                            'format' => 'dd.mm.yyyy',
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'exchange_rate')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar])->label("Dollar kursi") ?>
                </div>
                <div class="col-md-12">
                    <?php echo $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                        'id' => 'my_id',
                        'columns' => [
                            [
                                'name' => 'brand_id',
                                'title' => 'Model',
                                'type' => \kartik\select2\Select2::className(),                                
                                'options' => [
                                    'data'  => $model->getBrands(),
                                    'options' => [
                                        'placeholder' => 'Tanlang...',
                                        'class' => 'input-priority',
                                        'required' => true
                                    ], 
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                                                     
                                ],
                                'headerOptions' => [
                                    'style' => 'width: 370px;',
                                ] 
                            ],
                            [
                                'name' => 'product_category_id',
                                'title' => 'Nomi',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getProductCategories(),
                                    'options' => [
                                        'placeholder' => 'Tanlang...',
                                        'required' => true
                                    ],   
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],     
                                    'class' => 'input-priority',
                                    ],
                                    'headerOptions' => [
                                        'style' => 'width: 370px;',
                                        
                                    ] 
                            ],
                            [
                                'name'  => 'size',
                                'title' => 'O\'lchami <b style="color:red">(Butun sonni nuqta bilan kiriting. Misol uchun: 9.99 )</b>',
                                'enableError' => true,
                                'defaultValue' => 0,
                                'options' => [
                                    'class' => 'input-priority',
                                    // 'type' =>'number',
                                    'required' => true,
                                 ]
                            ],
                            [
                                'name' => 'type',
                                'title' => 'Tip',
                                'type' => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data'  => $model->getProductType(),
                                    'options' => [
                                        'placeholder' => 'Tanlang...',
                                        'required' => true
                                    ],   
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],     
                                    'class' => 'input-priority',
                                    ],
                                    'headerOptions' => [
                                        'style' => 'width: 180px;',
                                        
                                    ] 
                            ],
                            [
                                'name'  => 'count',
                                'title' => 'Soni',
                                'enableError' => true,
                                'options' => [
                                    'class' => 'input-priority',
                                    'type' =>'number',
                                    'required' => true
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
                        ]
                    ])->label('');?>
                </div>
                <div class="row">
                <div class="col-md-8">
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'sum_dollar')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'discount_amount')->textInput(['type' => 'number', 'value' => 0])->label("<b style='color:#f59c1a'>Jami chegirma ($) </b>") ?>
                </div>
                
                <!-- <div class="col-md-2">
                    ?= $form->field($model, 'sum_soms')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-1">
                    ?= $form->field($model, 'dollar_sumdas')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    ?= $form->field($model, 'sum_carts')->textInput(['type' => 'number', 'value' => 0]) ?> 
                </div>
                <div class="col-md-2">
                    ?= $form->field($model, 'sum_transferss')->textInput(['type' => 'number', 'value' => 0]) ?>
                </div> -->
                <!-- <div class="col-md-2">
                    ?= $form->field($model, 'order_account_statuses')->checkbox(['checked' => true])->label("<b style='font-size:16px;color:red'>Tasdiqlash: </b>") ?>
                </div> -->
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'import_product_status')->checkbox(['checked' => true])->label("<b style='font-size:16px;color:red'>Tasdiqlash: </b>") ?>
                </div>
            </div>
            </div>
            <?php if (!Yii::$app->request->isAjax){ ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Qo\'shish' : 'O\'zgartirish', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'style' => 'width:100%']) ?>
                </div>
            <?php } ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>


