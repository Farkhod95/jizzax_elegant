<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
use app\models\PriceProduct;
use app\models\ExchangeRate;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Url;
use app\models\Brands;
use app\models\Client;
use app\models\TypeSklad;
use app\models\Consignor;
use kartik\select2\Select2;

$brands = Brands::find()->where(['sup_status' => 1])->orderBy(['sorting' => SORT_ASC])->all();
$clients = Consignor::find()->orderBy(['id' => SORT_ASC])->all();
$typeSklads = TypeSklad::find()->orderBy(['id' => SORT_ASC])->all();

$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0;

$exchangeRate = ExchangeRate::find()->where(['id' => 1])->one();
?>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

#productTableWrapper {
    pointer-events: none; /* Jadvalni nofaol qilish */
    opacity: 0.5; /* Yaqin ko'rinish */
}
</style>
<div class="row">
	<div class="col-md-5">
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
                <h4 class="panel-title">Import qilinadigan mahsulotlar ro'yxati</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive" >
                        <label style="font-size: 14px;" for="cars">Modelni tanlang:</label>
                        <?= Select2::widget([
                            'name' => 'cars',
                            'id' => 'cars',
                            'data' => \yii\helpers\ArrayHelper::map($brands, 'name', 'name'),
                            'options' => ['placeholder' => 'Modelni tanlang'],
                            'pluginOptions' => [
                                    'allowClear' => true, // This option allows the user to clear the selection
                                    'width' => '400px',
                                ],
                        ]); ?>
                        <br/>
                        <!-- <div  style="height: 700px; overflow-y: auto; display: block; width: 100%;" onscroll="loadMoreContent()"> -->
                        <div style="height: 700px; overflow-y: auto; display: block; width: 100%; pointer-events: none;" id="productTableWrapper">
                            <table class="table">
                                <thead >
                                    <tr >
                                        <th style="background-color:#a4ddc3;"><b>#</b></th>
                                        <th nowrap style="background-color:#a4ddc3;"><b>Model</b></th>
                                        <th nowrap style="background-color:#a4ddc3;"><b>Nomi</b></th>
                                        <th nowrap style="background-color:#a4ddc3;"><b>O'lchami</b></th>
                                        <th nowrap style="background-color:#a4ddc3;"><b>Tip</b></th>
                                    </tr>
                                </thead>
                                <tbody id="showRes"  >
                                    <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0;?>
                                        <tr class="handle-header">
                                            <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                            <td style="background-color:#a0d9ea;"></td>
                                        </tr>
                                        <?php  foreach ($warehouses = Warehouse::find()
                                                ->alias('p')
                                                ->select(["p.*", "pc.sorting"])
                                                // ->where(['p.brand_id' => $model->brand->id])
                                                ->leftJoin("product_category pc", "p.product_category_id = pc.id")
                                                ->leftJoin("brands b", "p.brand_id = b.id")
                                                ->andWhere(['b.sup_status' => 1])
                                                // ->andWhere(['p.type' => [2, 3]])
                                                ->andWhere(['p.brand_id' => $model->brand->id])
                                                ->orderBy(['pc.sorting' => SORT_ASC])->all() as $model1) 
                                        { ?>
                                            <tr class="handle" id="<?= $model->brand->name . "_" . $i?>">
                                                <td style="background-color:#edefdf;width: 15%;"><b><?= $i ?></b></td>
                                                <td style="background-color:#edefdf;width: 15%;"><b><?= $model1->brand->name ?></b></td>
                                                <td style="background-color:#edefdf;width: 15%;"><b><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                                <td style="background-color:#edefdf;width: 15%;"><b><?= $model1->size ?></b></td>
                                                <td style="display:none;"><b><?= $model1->count ?></b></td>
                                                <td style="background-color:#edefdf;width: 15%;"><b><?= $model1->getTypeView($model1->type) ?></b></td>
                                                <td style="display:none;"> <?= $model1->id ?> </b></td>
                                            </tr>
                                        <?php  $i = $i +1 ; $allCount = $allCount + $model1->count; $allMarkCount = $allMarkCount + $model1->count; } ?>
                                        <tr class="<?= $model->brand->name ?>-amount">
                                            <!-- <td style="background-color:#fff;" colspan="2"  ><b ></b></td>
                                            <td style="background-color:#fff;"><b style="color:#000" >Jami:</b></td>
                                            <td style="background-color:#fff;"><b style="color:#000"> $allCount </b></td> -->
                                        </tr>
                                    <?php  } ?>
                                    
                                
                                    <!-- <tr>
                                        <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                        <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                        
                    <!-- <a href="#modal-dialog" class="btn btn-sm btn-danger" style="width:100%" data-toggle="modal">Sotish</a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7" style="position: sticky; top: 60px;">
        <div class="panel panel-inverse" >
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">Import qilinayotgan mahsulotlar ro'yxati</h4>
            </div>
            <div class="panel-body" >
                <div class="table-responsive" >
                    <div class="form-group row m-b-15">            
                        <div class="col-sm-5">
                            <!-- <label class="col-form-label"><h5><b>Xaridor</b></h5></label> -->
                            <!-- <input type="text" class="form-control" required placeholder="" name="customer_name"/> -->
                            <select action="<?=Url::toRoute(['warehouse/qarz'])?>" class="form-control" name="customer_name" id="myselect" required >
                                    <option ></option>
                                <?php  foreach ($clients as $client) {?>
                                    <option value=<?= $client->id?>><?= $client->name?></option>
                                <?php  } ?>
                            </select>
                            <?= Select2::widget([
                                'name' => 'customer_name',
                                'id' => 'myselect',
                                'data' => \yii\helpers\ArrayHelper::map($clients, 'name', 'name'),
                                'options' => ['placeholder' => 'Yuk jo\'natuvchini tanlang','style' => 'text-align:right;',],
                                'pluginOptions' => [
                                    'allowClear' => true, // This option allows the user to clear the selection
                                ],
                            ]); ?>
                        </div>
                        <?php if(\Yii::$app->user->identity->permission == 1 ||\Yii::$app->user->identity->permission == 6 || \Yii::$app->user->identity->permission == 5){?>
                            <div class="col-sm-4">
                                <label class="col-form-label"><h5><b>Mening Qarzim ($): </b></h5></label>
                                <label ><h5><b style="color:red" id="qarz_client_summ"> </b></h5></label>
                                <!-- <input  id="qarz_client_summ" type="number"  class="form-control" placeholder="" name="jami_qarzi" value="0"/> -->
                            </div>
                            <div class="col-sm-3" >
                                <label  class="col-form-label switch">   
                                    <input type="checkbox" id="toggleSwitch">
                                    <span class="slider round"></span>
                                </label>
                                <!-- <label><h5><b style="color:red" id="qarz_client_summ_label"></b></h5></label>
                                <input id="qarz_client_summ" type="number" class="form-control" placeholder="" name="jami_qarzi" value="0"/> -->
                            </div>
                        <?php }else{?>
                            <div class="col-sm-4">
                                <label style="display:none;" class="col-form-label"><h5><b>Mening Qarzim ($): </b></h5></label>
                                <label ><h5><b style="color:red;display:none;" id="qarz_client_summ" > </b></h5></label>
                                <!-- <input  id="qarz_client_summ" type="number"  class="form-control" placeholder="" name="jami_qarzi" value="0"/> -->
                            </div>
                        <?php }?>
                        <!-- <div class="col-sm-4">
                            <label class="col-sm-8 col-form-label"><h5><b>Ko'rinishi:</b></h5></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="show" value="show">
                                <label class="form-check-label" for="show">Ko'rsat</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="hide" value="hide" checked>
                                <label class="form-check-label" for="hide">Yashir</label>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-body hidden" id="toggleContent">
                        <form id="qarztul" action="<?=Url::toRoute(['warehouse/qarztul'])?>" method="post">
                            <input type="hidden" class="form-control" placeholder="" name="customer_name"/>
                            <input type="hidden" class="form-control" placeholder="" name="qarz_client_summ"/>
                            <div class="col-sm-3">
                                <label class="col-sm-8 col-form-label"><h5><b>Sana</b></h5></label>
                                <input class="form-control" style= "font-size: 14px;padding:3px;" required type="date" name="qarz_tul_date" value=<?= date('Y-m-d')?> >
                            </div>   
                            <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>Dollar kurs: </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_dollar_kurs_label"></b></h5></label>
                                <input type="number" class="form-control" placeholder="" name="tul_qarz_dollar_kurs"/>
                            </div>
                            <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>Summa ($): </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_sum_dollar_label"></b></h5></label>
                                <input type="text" class="form-control" placeholder="" name="tul_qarz_sum_dollar" value="0"/>
                            </div>
                            
                            <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>chegirma ($): </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_skidka_label"></b></h5></label>
                                <input type="number" class="form-control" placeholder="" name="tul_qarz_sikidka" value="0"/>
                            </div>
                            <!-- <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>Summa so'm: </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_sum_som_label"></b></h5></label>
                                <input type="number" class="form-control" placeholder="" name="tul_qarz_sum_som" value="0"/>
                            </div>
                            <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>Summa karta: </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_summ_cart_label"></b></h5></label>
                                <input type="number" class="form-control" placeholder="" name="tul_qarz_summ_cart" value="0"/>
                            </div>
                            <div class="col-sm-3">
                                <label class="col-form-label"><h5><b>Summa transfer: </b></h5></label>
                                <label><h5><b style="color:red" id="qarz_client_sum_transfer_label"></b></h5></label>
                                <input type="number" class="form-control" placeholder="" name="tul_qarz_sum_transfer" value="0"/>
                            </div> -->
                            <div class="modal-footer">
                                <button id="payButton" type="submit" style="margin-top:2%"  class="btn btn-danger" disabled>Qarzni to'lash</button>
                            </div>
                        </form>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="background-color:#a4ddc3;width: 5%;"><b>#</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 12%;"><b>Joy</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 18%;"><b>Model</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 15%;"><b>Nomi</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 10%;"><b>O'lcham</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 15%;"><b>Tip</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 10%;"><b>Narxi ($)</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 8%;"><b>Soni</b></th>
                                <th nowrap style="background-color:#a4ddc3;width: 10%;"><b>Umum.narxi ($)</b></th>
                                <th style="background-color:#a4ddc3;"><b></b></th>
                            </tr>
                        </thead>
                        <tbody id="backet" data-count="0" data-increment="0" data-count-sum="0">
                            <tr id="add">
                                <td colspan="7" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white" id="all">0</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white" id="all_sum">0</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white" ></b></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <!-- <a id="sellButton"  href="#" class="btn btn-sm btn-danger buy_product"  style="width:100%">Sotish</a> -->
                    <button id="sellButton" class="btn btn-sm btn-danger buy_product" style="width:100%" disabled>Import qilish</button>
                        <!-- <button type="button" class="btn btn-danger" style="width:100%">Sotish</button> -->
                    
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-dialog2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import mahsulotlarga qo'shish</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
            </div>
            
            <div class="modal-body">
                <form id="add_to_backet">
                    <input type="hidden" name="product_id">
                    <input type="hidden" name="size">
                    <input type="hidden" name="key">
                    <input type="hidden" name="maxsulot_tipi">
                    <div class="form-group row m-b-15">
                        <label class="col-sm-4 col-form-label"><h5><b>Model:</b></h5></label>
                        <div class="col-sm-8">
                            <label ><h5><b style="color:red" id="marka">FD-2</b ></h5></label>
                        </div>
                    </div>
                    <div class="form-group row m-b-15 align-items-center">
                        <label class="col-sm-4 col-form-label"><h5><b>Nomi:</b></h5></label>
                        <div class="col-sm-8">
                            <label ><h5><b style="color:red" id="name">Kosa</b></h5></label>
                        </div>
                    </div>
                    <div class="form-group row m-b-15 align-items-center">
                        <label class="col-sm-4 col-form-label"><h5><b>O'lchami:</b></h5></label>
                        <div class="col-sm-8">
                            <label ><h5><b style="color:red" id="size">12</b></h5></label>
                        </div>
                    </div>
                    <div class="form-group row m-b-15 align-items-center">
                        <label class="col-sm-4 col-form-label"><h5><b>Tip:</b></h5></label>
                        <div class="col-sm-8">
                            <label ><h5><b style="color:red" id="maxsulot_tipi">12</b></h5></label>
                        </div>
                    </div>
                    <div class="form-group row m-b-15">
                        <label class="col-sm-4 col-form-label"><h4><b>Joy</b></h4></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="maxsulot_joyi" required >
                                <!-- <option ></option> -->
                                <?php  foreach ($typeSklads as $sklad) {?>
                                    <option value=<?= $sklad->name?>><?= $sklad->name?></option>
                                <?php  } ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="soni" placeholder="" />
                            <span class="error_message text-danger"></span> -->
                        </div>
                        
                    </div>
                    <!-- <div class="form-group row m-b-15">
                        <label class="col-sm-4 col-form-label"><h4><b>Tip</b></h4></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="maxsulot_tipi" required >
                                <option ></option>
                                <option value="Karobka">Karobka</option>
                                <option value="Komplekt">Komplekt</option>
                                <option value="Pochka">Pochka</option>
                                <option value="Dona">Dona</option>
                            </select>
                        </div>
                        
                    </div> -->
                    <div class="form-group row m-b-15">
                        <label class="col-sm-4 col-form-label"><h4><b>Soni</b></h4></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" name="soni" placeholder="" />
                            <span class="error_message text-danger"></span>
                        </div>
                        
                    </div>
                    <div class="form-group row m-b-15">
                        <label class="col-sm-4 col-form-label"><h4><b>Narxi ($)</b></h4></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" required name="maxsulot_narxi" placeholder="" />
                            <span class="error_message_narx text-danger"></span>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                <a  class="btn btn-danger submit" >Import qo'shish</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import qilish</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
            </div>
            <div class="modal-body">
                <form id="buy" action="<?=Url::toRoute(['warehouse/accept'])?>" method="post">
                    <!-- <div class="form-group row m-b-15">
                        <label class="col-sm-3 col-form-label"><h5><b>Mahsulot turi soni:</b></h5></label>
                        <div class="col-sm-8">
                            <label ><h5><b style="color:red" id="count_porduct"></b ></h5></label>
                        </div>
                    </div> -->
                    <div class="form-group row m-b-15 align-items-center">
                        <label class="col-sm-3 col-form-label"><h5><b>Umumiy soni:</b></h5></label>
                        <div class="col-sm-1">
                            <label ><h5><b style="color:red" id="total_product"></b></h5></label>
                        </div>
                        <label class="col-sm-3 col-form-label"><h5><b>Umumiy narxi ($):</b></h5></label>
                        <div class="col-sm-1">
                            <label ><h5><b style="color:red" id="total_product_sum"></b></h5></label>
                        </div>
                        <!-- <label class="col-sm-2 col-form-label"><h5><b>Tip:</b></h5></label>
                        <div class="col-sm-2">
                            <label ><h5><b style="color:red" id="maxsulot_tipi"> </b></h5></label>
                        </div> -->
                        
                    </div>
                    <hr/>
                    <input type="hidden" class="form-control" placeholder="" name="product_details"/>
                    
                    <div class="form-group row m-b-15">
                        <div class="col-sm-6">
                            <label class="col-sm-8 col-form-label"><h5><b>Sana</b></h5></label>
                            <input class="form-control" style= "font-size: 14px;padding:3px;" required type="date" name="order_date" value=<?= date('Y-m-d')?> >
                        </div>           
                        <div class="col-sm-6">
                            <label class="col-sm-8 col-form-label"><h5><b>Dollar kursi</b></h5></label>
                            <input  class="form-control" placeholder="" name="dollar_kurs" value=<?= $exchangeRate->dollar ?>  />
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group row m-b-15">
                        <div class="col-sm-8">
                            <label class="col-sm-8 col-form-label"><h5><b>To'langan summa ($)</b></h5></label>
                            <input class="form-control"  placeholder="" name="summa_dollor" id="dollarToSum" value="0"/>
                        </div>
                        <div class="col-sm-4">
                            <label class="col-sm-8 col-form-label"><h5><b>Chegirma ($)</b></h5></label>
                            <input type="number" class="form-control"  placeholder="" name="chegirma_summa" value="0"/>
                        </div>
                    </div>
                    <!-- <div class="form-group row m-b-15">
                        
                        <div class="col-sm-4">
                            <label class="col-sm-8 col-form-label"><h5><b>Dollar ($)</b></h5></label>
                            <input class="form-control"  placeholder="" name="summa_dollor" id="dollarToSum" value="0"/>
                        </div>
                        <div class="col-sm-5">
                            <label class="col-sm-8 col-form-label"><h5><b>Summa so'mda</b></h5></label>
                            <input type="number" class="form-control"  placeholder="" name="summa_som" value="0"/>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-sm-8 col-form-label"><h5><b>($) </b></h5></label>
                            <input type="number" class="form-control"  placeholder="" name="dollar_sumda" value="0"/>
                        </div>
                        
                    </div>
                    <div class="form-group row m-b-15">
                        
                        <div class="col-sm-6">
                            <label class="col-sm-8 col-form-label"><h5><b>Summa kartada</b></h5></label>
                            <input type="number" class="form-control"  placeholder="" name="summa_karta" value="0"/>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-sm-8 col-form-label"><h5><b>Summa transferda</b></h5></label>
                            <input type="number" class="form-control"  placeholder="" name="summa_transfer" value="0"/>
                        </div>
                    </div> -->
                    <div class="form-group row m-b-15">
                        <div class="col-sm-12">
                            <label class="col-sm-8 col-form-label"><h5><b>Izoh</b></h5></label>
                            <textarea class="form-control" placeholder="" name="comment" rows="4"></textarea>
                        </div>
                    </div>
                    <!-- <div class="form-group row m-b-15">
                        
                        <div class="col-sm-6">
                            <label class="col-sm-8 col-form-label"><h5><b>Tasdiqlash:</b></h5></label>
                            <input type="checkbox" id="tasdiqCheckbox" style="margin-top:14px" name="tasdiq_check" value="1" checked />
                        </div>
                    </div> -->
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                        <button type="submit" id="sellSubmitButton"  class="btn btn-danger">Importni tasdiqlash</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="modal-dialog-error">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xatolik!</h4>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
            </div>
            <div class="modal-body">
                    <div class="form-group row m-b-15 align-items-center">
                        <label class="col-sm-12 col-form-label"><h5><b style="color:red">Mijozda hech qanday buyurtma yo'q. </b></h5></label>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Yopish</a>
                    </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS


// function adjustTableClass() {
//     // Ekran kengligini o'lchash
//     var screenWidth = window.innerWidth;
    
//     // Jadval elementini olish
//     var table = document.querySelector('.table');

//     if (screenWidth <= 1300) {
//       // Agar ekran kengligi 1024px yoki undan kichik bo'lsa, classni olib tashlash
//       table.classList.remove('table');
//     } else {
//       // Agar ekran kengligi 1025px yoki undan katta bo'lsa, classni qo'shish
//       table.classList.add('table');
//     }
//   }
  function adjustTableClass() {
    // Ekran kengligini o'lchash
    var screenWidth = window.innerWidth;
    
    // Jadval elementini olish
    var table = document.querySelector('.table');

    if (screenWidth <= 1300) {
        // Ekran kengligi 1400px yoki undan kichik bo'lsa, stilni o'zgartirish
        table.classList.remove('table');
        table.style.padding = '10px 0';
        table.style.lineHeight = '3';  // Satrlar orasidagi kenglik
    } else {
        // Ekran kengligi 1400px dan katta bo'lsa, stilni asl holatiga qaytarish
        table.classList.add('table');
    }
}

  // Sahifa yuklanganida classni tekshirish
  window.onload = adjustTableClass;

  // Ekran o'lchami o'zgarganda classni tekshirish
  window.onresize = adjustTableClass;
  
// document.getElementById('dollarToSum').addEventListener('change', function() {
//     let summa_dollor = $('input[name="summa_dollor"]').val();
//     let dollar_kurs = $('input[name="dollar_kurs"]').val();
//     const dollar_sumda = summa_dollor*dollar_kurs;
//     $('input[name="dollar_sumda"]').val(dollar_sumda);
    
// });

$("#myselect").change(function () {
    var clientId = $(this).val();

    if (clientId) {
        // Agar mijoz tanlangan bo'lsa
        $("#productTableWrapper").css("pointer-events", "auto"); // Jadvalni faollashtirish
        $("#productTableWrapper").css("opacity", "1");
    } else {
        // Agar mijoz tanlanmagan bo'lsa
        $("#productTableWrapper").css("pointer-events", "none"); // Jadvalni nofaol qilish
        $("#productTableWrapper").css("opacity", "0.5");
        // $("#errorModal").modal("show"); // Modalni ko'rsatish
    }
});

$("select#myselect").change(function(){
    var client_id =$('select[name="customer_name"]').val();
    let action = $(this).attr("action");
    let payload = {
        client_id: $('select[name="customer_name"]').val(),
    };

    $.ajax({
        url: action,
        data: payload,
        method: "GET", 
    }).done(function(data) {
        $( this ).addClass( "done" );
        // alert(data);
        $("#qarz_client_summ").text(data);
        var sellButton = document.getElementById('payButton');
        if (data == 0) {
            sellButton.disabled = true; // Enable button if a client is selected
        } else {
            sellButton.disabled = false; // Disable button if no client is selected
        }
    });

    var sellButton = document.getElementById('sellButton');
    if (this.value) {
        sellButton.disabled = false; // Enable button if a client is selected
    } else {
        sellButton.disabled = true; // Disable button if no client is selected
    }

    var sellButton = document.getElementById('payButton');
    if (this.value) {
        sellButton.disabled = false; // Enable button if a client is selected
    } else {
        sellButton.disabled = true; // Disable button if no client is selected
    }
    
});


function loadMoreContent() {
  var table = document.getElementById("myTable");
  var wrapper = document.querySelector(".table-wrapper");
  if (wrapper.scrollTop + wrapper.clientHeight >= table.scrollHeight) {
    // Load more content when scrolled to the bottom
    // Example: Append more rows to the table
    var tbody = table.querySelector("tbody");
    for (var i = 0; i < 10; i++) {
      var newRow = document.createElement("tr");
      newRow.innerHTML = "<td>New Data</td><td>New Data</td>";
      tbody.appendChild(newRow);
    }
  }
}


$('#cars').on('change', function(e){
    e.preventDefault();
    const value = $(this).val().toUpperCase();;
    if (value) {
        $("#showRes .handle").filter(function() {
        const td = $(this).children('td').eq(1).text().toUpperCase();
        $(this).toggle(td === value)
        });

        $("#showRes .handle-header").filter(function() {
            const td = $(this).children('td').eq(0).text().toUpperCase();
            $(this).toggle(td === value)
        });

        $("#showRes tr").filter(function() {
            const className = $(this).attr('class').toUpperCase();
            if(className.indexOf('AMOUNT') > -1){
                $(this).toggle(className == value + '-AMOUNT')
            }
        });
    }else{
        $("#showRes tr").filter(function() {
        $(this).show()
        });
    }
    


    
});

$("#qarztul").submit(function(event){
    event.preventDefault();
    var payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.innerHTML = 'Jarayonda…';

    let action = $(this).attr("action");

    let payload = {
        customer_name: $('select[name="customer_name"]').val(),
        qarz_client_summ: $("#qarz_client_summ").text(),
        qarz_tul_date: $('input[name="qarz_tul_date"]').val(),
        tul_qarz_sum_dollar: $('input[name="tul_qarz_sum_dollar"]').val(),
        tul_qarz_dollar_kurs: $('input[name="tul_qarz_dollar_kurs"]').val(),

        tul_qarz_sikidka: $('input[name="tul_qarz_sikidka"]').val(),
        tul_qarz_sum_som: $('input[name="tul_qarz_sum_som"]').val(),
        tul_qarz_summ_cart: $('input[name="tul_qarz_summ_cart"]').val(),
        tul_qarz_sum_transfer: $('input[name="tul_qarz_sum_transfer"]').val(),
    };

    // console.log(payload);

    $.ajax({
        url: action,
        data: payload,
        method: "POST", 
    }).done(function(data) {
        alert(data);
        // Optionally, you can enable the button again after the process is completed
        payButton.disabled = false;
        payButton.innerHTML = 'Qarzni to\'lash';
    }).fail(function() {
        // In case of failure, re-enable the button so the user can try again
        payButton.disabled = false;
        payButton.innerHTML = 'Qarzni to\'lash';
    });
});

$("#buy").submit(function(event){
    event.preventDefault();
    // Disable the submit button and change its text to 'Loading...'
    let action = $(this).attr("action");

    let payload = {
        customer_name: $('select[name="customer_name"]').val(),
        // customer_name: $('#customer_name').find(":selected").text(),
        // jami_qarzi: $('input[name="jami_qarzi"]').val(),
        jami_qarzi: $("#qarz_client_summ").text(),
        order_date: $('input[name="order_date"]').val(),
        dollar_kurs: $('input[name="dollar_kurs"]').val(),
        chegirma_summa: $('input[name="chegirma_summa"]').val(),
        summa_dollor: $('input[name="summa_dollor"]').val(),
        dollar_sumda: $('input[name="dollar_sumda"]').val(),
        summa_som: $('input[name="summa_som"]').val(),
        summa_karta: $('input[name="summa_karta"]').val(),
        summa_transfer: $('input[name="summa_transfer"]').val(),
        comment: $('textarea[name="comment"]').val(),
        total: $("#count_porduct").text(),
        count: $("#total_product").text(),
        all_sum: $("#total_product_sum").text(),
        product_details: $('input[name="product_details"]').val(),
        tasdiq_check: $('input[name="tasdiq_check"]').val(),
    };

    // JavaScript: Modified AJAX request with loading indicator
    $.ajax({
        url: action,
        data: payload,
        method: "POST",
        beforeSend: function() {
            $("#sellSubmitButton").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Jarayonda...');
        }
    }).done(function(data) {
        // Handle success response
        alert(data);
        $("#modal-dialog2").modal("hide");
        // Optionally add some indication of success to the triggering element
        $(this).addClass("done");
    }).fail(function(jqXHR, textStatus, errorThrown) {
        // Handle error response
        console.error("Request failed: " + textStatus + ", " + errorThrown);
        $("#modal-dialog2").modal("hide");
    }).always(function() {
        $("#sellSubmitButton").prop("disabled", false).html('Importni tasdiqlash');
        $("#modal-dialog2").modal("hide");
    });


    // onClick="this.disabled=true; this.value='Sending…';"

});

$('#tasdiqCheckbox').on('change', function() {
        // Checkbox tanlangan bo'lsa, qiymat 1, aks holda 0 bo'ladi
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

$(".buy_product").on('click', function(){

    let all_total = $("#backet").attr("data-count");
    let all_total_sum = $("#backet").attr("data-count-sum");
    let products_count = $("#backet").attr("data-increment");

    let product_details = [];
    
    $("#backet").children().each(function(){
        if($(this).attr("id") !== "add"){

            product_details.push({
                joy: $(this).children().eq(1).text(),
                marka: $(this).children().eq(2).text(),
                name: $(this).children().eq(3).text(),
                size: $(this).children().eq(4).text(),
                tip: $(this).children().eq(5).text(),
                price: $(this).children().eq(6).text(),
                count: $(this).children().eq(7).text(),
                all_sum: $(this).children().eq(8).text(),
                product_id: $(this).children().eq(9).text(),
            });
        }
    });
    console.log("product_details:", product_details);
    // console.log(product_details);

    $('input[name="product_details"]').val(JSON.stringify(product_details));
    $('#count_porduct').text(products_count);
    $("#total_product").text(all_total);
    $("#total_product_sum").text(all_total_sum);
    $("#maxsulot_tipi").text(12312);
    
    if(all_total == 0){
        // alert("Mijozda hech qanday buyurtma yo'q");
        $("#modal-dialog-error").modal("toggle");
        return false;
    }

    $("#modal-dialog").modal("toggle");

});

$('.handle').on("click", function(){
    $(".error_message").text("");
    $('input[name="maxsulot_narxi"]').val("");
    $('input[name="soni"]').val("");
    $('input[name="maxsulot_tipi"]').val("");

    let maxsulot_tipi = $(this).children().eq(5).eq(0).text();
    let count = $(this).children().eq(4).text();
    let mark = $(this).children().eq(1).text();
    let name = $(this).children().eq(2).text();
    let size = $(this).children().eq(3).text();
    let product_id = $(this).children().eq(5).text();

    $("#marka").text(mark);
    $("#name").text(name);
    $("#size").text(size);
    $("#maxsulot_tipi").text(maxsulot_tipi);

    if (count < 0) {
        count = 0;
    }

    $('input[name="product_id"]').val(product_id);
    // $('input[name="soni"]').val(count);
    $('input[name="soni"]').val(0);
    $('input[name="size"]').val(size);
    $('input[name="maxsulot_tipi"]').val(maxsulot_tipi);
    $('input[name="key"]').val($(this).attr('id'));

    $("#modal-dialog2").modal();
});

$(".submit").on("click", function(event){
    event.preventDefault();
    $(".error_message").text("");
    let count_product = $('input[name="soni"]').val();
    let maxsulot_narxi = $('input[name="maxsulot_narxi"]').val();
    // let maxsulot_tipi = $('select[name="maxsulot_tipi"]').val();
    let maxsulot_joyi = $('select[name="maxsulot_joyi"]').val();
    let name = $('#name').text();
    let marka = $('#marka').text();
    let product_id = $('input[name="product_id"]').val();
    let size = $('input[name="size"]').val();
    let maxsulot_tipi = $('input[name="maxsulot_tipi"]').val();

    let key = $('input[name="key"]').val();
    let count = parseInt($("#backet").attr("data-count"));
    let all_sum = parseInt($("#backet").attr("data-count-sum"));
    let increment = parseInt($("#backet").attr("data-increment")) + 1;
    count = count + parseInt(count_product);
    all_sum = all_sum +  Math.round(maxsulot_narxi * count_product * 100)/100;
    // Validate data
    let count_old = parseInt($("#" + key).children().eq(4).text());

    if(count_product == 0 ){
        $(".error_message").text("0 ta buyurtma berib bo'lmaydi");
        return false;
    }

    // if(count_product == 0 || count_product > count_old){
    //    $(".error_message").text("Maximal qiymat "+ count_old +" ta bo'lishi kerak");
    //    return false;
    // }
    
    if(maxsulot_narxi.length == 0){
        $(".error_message_narx").text("Mahsulot narxini kiriting.");
        return false;
    }
    count_old = count_old - count_product;

    let id = key.split("_");
    let ammount_value = $("." + id[0] + "-amount").children().eq(2).text();
    ammount_value = ammount_value - count_product;
    $("." + id[0] + "-amount").children().eq(2).text(ammount_value)
    
    // $("#" + key).children().eq(4).text(count_old);
    // console.log("maxsulot_tipi:", maxsulot_tipi)
    $("#add").before("<tr><td style='background-color:#edefdf;'><b>" + increment + "</b></td><td style='background-color:#edefdf;'>" + maxsulot_joyi + "</td><td style='background-color:#edefdf;'><b>" + marka + "</b></td><td style='background-color:#edefdf;'>" + name + "</td><td style='background-color:#edefdf;'><b>" + size + "</b></td><td style='background-color:#edefdf;'>" + maxsulot_tipi + "</td><td style='background-color:#edefdf;'><b>" + maxsulot_narxi + "</b></td><td style='background-color:#edefdf;'>" + count_product + "</td><td style='background-color:#edefdf;'><b>" + Math.round(maxsulot_narxi * count_product* 100)/100 + "</b></td><td style='display:none;'>" + product_id + "</td><td style='background-color:#edefdf;'><button class='btn btn-sm btn-danger delete-product'><i class='glyphicon glyphicon-remove'></i></button></td></tr>");
    
    $("#backet").attr("data-increment", increment)

    // Update total count and sum
    // let currentCount = parseInt($("#backet").attr("data-count"));
    // let currentSum = parseInt($("#backet").attr("data-count-sum"));

    // currentCount += parseInt(count_product);
    // currentSum += parseInt(maxsulot_narxi) * parseInt(count_product);
    // console.log("currentCount:", currentCount);
    // console.log("currentSum:", currentSum);
    // console.log("currentCount1:", currentCount);
    // console.log("currentSum1:", currentSum);
    $("#backet").attr("data-count", count)
    $("#backet").attr("data-count-sum", all_sum)
    $("#all").text(count);
    $("#all_sum").text(all_sum);

    $("#modal-dialog2").modal('toggle');

});

$(document).on("click", ".delete-product", function() {
    // Get the row to be deleted and update counts
    let row = $(this).closest("tr");
    let decrementCount = parseInt(row.find("td:eq(7)").text()); // Assuming count is in the 8th cell
    let increment = parseInt($("#backet").attr("data-increment"));

    let currentCount = parseInt($("#backet").attr("data-count"));
    let currentSum = parseInt($("#backet").attr("data-count-sum"));

    currentCount -= decrementCount;
    currentSum -= parseInt(row.find("td:eq(6)").text()) * decrementCount; // Assuming price is in the 7th cell

    $("#backet").attr("data-count", currentCount);
    $("#backet").attr("data-count-sum", currentSum);
    $("#all").text(currentCount);
    $("#all_sum").text(currentSum);

    row.remove();
    increment--;
    $("#backet").attr("data-increment", increment);


    let i = 1;

    $('#backet').find('tr').each(function() {
        console.log($(this).attr('id'));
        if($(this).attr('id') !== 'add'){
            $(this).find("td:eq(0)").html(i);
            if (i === increment) {
                return false;
            }
            i++;
        }
    });

});

document.getElementById('toggleSwitch').addEventListener('change', function() {
            var toggleContent = document.getElementById('toggleContent');
            
            if (this.checked) {
                toggleContent.classList.remove('hidden');
                sellButton.disabled = true; 
                $('input[name="tul_qarz_dollar_kurs"]').val($exchangeRate->dollar);
            } else {
                toggleContent.classList.add('hidden');
                sellButton.disabled = false; 
            }
        });

JS
) ?>