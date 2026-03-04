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
use kartik\select2\Select2;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

$brands = Brands::find()->where(['sup_status' => 1])->orderBy(['sorting' => SORT_ASC])->all();
$clients = Client::find()->orderBy(['id' => SORT_ASC])->all();
$typeSklads = TypeSklad::find()->orderBy(['id' => SORT_ASC])->all();

$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0;

$exchangeRate = ExchangeRate::find()->where(['id' => 1])->one();

/** Brand id=>name xaritasi JS ga chiqsin */
$this->registerJsVar('BRAND_MAP', ArrayHelper::map($brands, 'id', 'name'));
$catAjaxUrl = Url::to(['product-category/by-brand']); // AJAX endpoint (absolyut/relative muhim emas)
$isRole1 = !Yii::$app->user->isGuest && (int)Yii::$app->user->identity->permission === 1;
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
  opacity: 0.5;        /* Yaqin ko'rinish */
}

.err-space{
  display:block;
  min-height:18px;   /* xatolik chiqsa ham layout buzilmaydi */
  line-height:18px;
}
</style>

<div class="row">
  <div class="col-md-5">
    <div class="panel panel-inverse">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Ombordagi mahsulotlar</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          
          <!-- ====== FILTER QISMI (faqat shu joy o'zgartirildi) ====== -->
          <div class="row form-inline">
            <div class="col-md-5">
                <label style="font-size:14px;margin-right:10px" for="brandFilter">Modelni tanlang:</label>
                <?= Select2::widget([
                'name' => 'brand_id',
                'id'   => 'brandFilter',
                'data' => ArrayHelper::map($brands, 'id', 'name'),
                'options' => [
                    'placeholder' => 'Modelni tanlang',
                    'data-url'    => $catAjaxUrl, // AJAX endpoint
                ],
                'pluginOptions' => ['allowClear'=>true],
                ]); ?>
            </div>

            <div class="col-md-4">
                <label style="font-size:14px;margin-right:10px" for="categoryFilter">Kategoriya:</label>
                <?= Select2::widget([
                'name' => 'product_category_id',
                'id'   => 'categoryFilter',
                'data' => [],
                'options' => [
                    'placeholder' => 'Kategoriyani tanlang',
                    'disabled'    => true,
                ],
                'pluginOptions' => ['allowClear'=>true],
                ]); ?>
            </div>

            <!-- Yangi: Tozalash tugmasi -->
            <div class="col-md-3" style="padding-top:22px;">
                <button type="button" id="bcClearFilters" class="btn btn-default" title="Filtirlarni tozalash">
                <i class="fa fa-eraser"></i> Tozalash
                </button>
            </div>
            </div>

          <!-- ====== /FILTER QISMI ====== -->

          <br/>

          <div style="height: 700px; overflow-y: auto; display: block; width: 100%; pointer-events: none;" id="productTableWrapper">
            <table class="table">
              <thead>
                <tr>
                  <th style="background-color:#90e6e6;"><b>#</b></th>
                  <th nowrap style="background-color:#90e6e6;"><b>Model</b></th>
                  <th nowrap style="background-color:#90e6e6;"><b>Nomi</b></th>
                  <th nowrap style="background-color:#90e6e6;"><b>O'lchami</b></th>
                  <th nowrap style="background-color:#90e6e6;"><b>Sklad soni</b></th>
                  <th nowrap style="background-color:#90e6e6;"><b>Tip</b></th>
                </tr>
              </thead>
              <tbody id="showRes">
                <?php foreach ($warehouse as $model) { $i = 1; $allCount = 0; ?>
                  <tr class="handle-header">
                    <td colspan="5" style="background-color:#a0d9ea;"><b style="color:red"><?= $model->brand->name ?></b></td>
                    <td style="background-color:#a0d9ea;"></td>
                  </tr>

                  <?php
                  $warehouses = Warehouse::find()
                    ->alias('p')
                    ->select(["p.*", "pc.sorting"])
                    ->leftJoin("product_category pc", "p.product_category_id = pc.id")
                    ->leftJoin("brands b", "p.brand_id = b.id")
                    ->andWhere(['b.sup_status' => 1])
                    ->andWhere(['p.brand_id' => $model->brand->id])
                    ->orderBy(['pc.sorting' => SORT_ASC])
                    ->all();

                  foreach ($warehouses as $model1) {
                  ?>
                    <tr class="handle"
                      id="<?= $model->brand->name . "_" . $i ?>"
                      data-name="<?= strtolower($model1->product_category_id ? $model1->productCategory->name : '') ?>"
                      data-brand-id="<?= $model1->brand_id ?>"
                      data-category-id="<?= $model1->product_category_id ?>">
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%;"><b><?= $i ?></b></td>
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%;"><b><?= $model1->brand->name ?></b></td>
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%;"><b><?= $model1->product_category_id ? $model1->productCategory->name : '' ?></b></td>
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%;"><b><?= $model1->size ?></b></td>
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%; color:#8f302d;font-size:14px"><b><?= $model1->count ?></b></td>
                      <td style="border-top:1px solid #bebabaff; border-bottom:1px solid #bebabaff; border-left:none; border-right:none;background-color:#efdfdf;width: 15%;"><b><?= $model1->getTypeClientView($model1->type) ?></b></td>
                      <td style="display:none;"><?= $model1->id ?></td>
                    </tr>
                  <?php $i++; $allCount += $model1->count; $allMarkCount += $model1->count; } ?>

                  <tr class="<?= $model->brand->name ?>-amount"></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- O'ng panel — mijoz buyurtmasi (o'zgartirmadim) -->
  <div class="col-md-7" style="position: sticky; top: 60px;">
    <div class="panel panel-inverse">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Mijoz buyurtmasi</h4>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <div class="form-group row m-b-15">
            <div class="col-sm-5">
              <select action="<?= Url::toRoute(['order-account/qarz'])?>" class="form-control" name="customer_name" id="myselect" required>
                <option></option>
                <?php foreach ($clients as $client) { ?>
                  <option value="<?= $client->id ?>"><?= $client->fio ?></option>
                <?php } ?>
              </select>
              <?= Select2::widget([
                'name' => 'customer_name',
                'id' => 'myselect',
                'data' => ArrayHelper::map($clients, 'fio', 'fio'),
                'options' => ['placeholder' => 'Mijoz tanlang','style' => 'text-align:right;'],
                'pluginOptions' => ['allowClear' => true],
              ]); ?>
            </div>
            <?php if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 6 || \Yii::$app->user->identity->permission == 5){ ?>
              <div class="col-sm-4">
                <label class="col-form-label"><h5><b>Qarzi ($): </b></h5></label>
                <label><h5><b style="color:red" id="qarz_client_summ"></b></h5></label>
              </div>
              <div class="col-sm-3">
                <label class="col-form-label switch">
                  <input type="checkbox" id="toggleSwitch">
                  <span class="slider round"></span>
                </label>
              </div>
            <?php } else { ?>
              <div class="col-sm-4">
                <label style="display:none;" class="col-form-label"><h5><b>Qarzi ($): </b></h5></label>
                <label><h5><b style="color:red;display:none;" id="qarz_client_summ"></b></h5></label>
              </div>
            <?php } ?>
          </div>

          <div class="modal-body hidden" id="toggleContent">
            <form id="qarztul" action="<?= Url::toRoute(['order-account/qarztul'])?>" method="post">
              <div class="row">
                <div class="col-sm-4">
                  <label><h5><b>Sana</b></h5></label>
                  <input class="form-control" required type="date" name="qarz_tul_date"
                        value="<?= date('Y-m-d') ?>" <?= $isRole1 ? '' : 'disabled' ?>>
                  <span class="text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <label><h5><b>Jami Summa ($):</b></h5></label>
                  <input type="text" class="form-control" name="tul_qarz_sum_dollar"/>
                  <span class="error_tul_qarz_sum_dollar text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <label><h5><b>Chegirma ($):</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_sikidka" value="0"/>
                  <span class="text-danger err-space"></span>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <label><h5><b>Summa ($):</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_sum_transfer" step="0.01" min="0" inputmode="decimal">
                  <span class="error_tul_qarz_sum_transfer text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <label><h5><b>Summa so'm:</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_sum_som"/>
                  <span class="error_tul_qarz_sum_som text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <label><h5><b>Summa karta:</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_summ_cart"/>
                  <span class="error_tul_qarz_summ_cart text-danger err-space"></span>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <label><h5><b>Qaytim ($)</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_zdacha_dollar" step="0.01" min="0" inputmode="decimal">
                  <span class="error_tul_qarz_zdacha_dollar text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <label><h5><b>Qaytim so'm</b></h5></label>
                  <input type="number" class="form-control" name="tul_qarz_zdacha_sum"/>
                  <span class="error_tul_qarz_zdacha_sum text-danger err-space"></span>
                </div>

                <div class="col-sm-4">
                  <!-- bo‘sh joy (3 ta ustun to‘lsin) -->
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="modal-footer">
                <button id="payButton" type="submit" class="btn btn-danger" disabled>Qarzni to'lash</button>
              </div>

            </form>
          </div>


          <table class="table">
            <thead>
            <tr>
              <th style="background-color:#90e6e6;width: 5%;"><b>#</b></th>
              <th nowrap style="background-color:#90e6e6;width: 12%;"><b>Joy</b></th>
              <th nowrap style="background-color:#90e6e6;width: 18%;"><b>Model</b></th>
              <th nowrap style="background-color:#90e6e6;width: 15%;"><b>Nomi</b></th>
              <th nowrap style="background-color:#90e6e6;width: 10%;"><b>O'lcham</b></th>
              <th nowrap style="background-color:#90e6e6;width: 15%;"><b>Tip</b></th>
              <th nowrap style="background-color:#90e6e6;width: 10%;"><b>Narxi ($)</b></th>
              <th nowrap style="background-color:#90e6e6;width: 8%;"><b>Soni</b></th>
              <th nowrap style="background-color:#90e6e6;width: 10%;"><b>Umum.narxi ($)</b></th>
              <th style="background-color:#90e6e6;"><b></b></th>
            </tr>
            </thead>
            <tbody id="backet" data-count="0" data-increment="0" data-count-sum="0">
            <tr id="add">
              <td colspan="7" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
              <td style="background-color:#2d353c;"><b style="color:white" id="all">0</b></td>
              <td style="background-color:#2d353c;"><b style="color:white" id="all_sum">0</b></td>
              <td style="background-color:#2d353c;"><b style="color:white"></b></td>
            </tr>
            </tbody>
          </table>

          <button id="sellButton" class="btn btn-sm btn-danger buy_product" style="width:100%" disabled>Sotish</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal #2 -->
<div class="modal fade" id="modal-dialog2">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title">Mijoz buyurtmasiga qo'shish</h4></div>
      <div class="modal-body">
        <form id="add_to_backet">
          <input type="hidden" name="product_id">
          <input type="hidden" name="size">
          <input type="hidden" name="key">
          <input type="hidden" name="maxsulot_tipi">
          <input type="hidden" name="brand_id">
          <input type="hidden" name="product_category_id">

          <div class="form-group row m-b-15">
            <label class="col-sm-4 col-form-label"><h5><b>Model:</b></h5></label>
            <div class="col-sm-8"><label><h5><b style="color:red" id="marka">FD-2</b></h5></label></div>
          </div>
          <div class="form-group row m-b-15 align-items-center">
            <label class="col-sm-4 col-form-label"><h5><b>Nomi:</b></h5></label>
            <div class="col-sm-8"><label><h5><b style="color:red" id="name">Kosa</b></h5></label></div>
          </div>
          <div class="form-group row m-b-15 align-items-center">
            <label class="col-sm-4 col-form-label"><h5><b>O'lchami:</b></h5></label>
            <div class="col-sm-8"><label><h5><b style="color:red" id="size">12</b></h5></label></div>
          </div>
          <div class="form-group row m-b-15 align-items-center">
            <label class="col-sm-4 col-form-label"><h5><b>Tip:</b></h5></label>
            <div class="col-sm-8"><label><h5><b style="color:red" id="maxsulot_tipi">12</b></h5></label></div>
          </div>

          <div class="form-group row m-b-15">
            <label class="col-sm-4 col-form-label"><h4><b>Joy</b></h4></label>
            <div class="col-sm-8">
              <select class="form-control" name="maxsulot_joyi" required>
                <?php foreach ($typeSklads as $sklad) { ?>
                  <option value="<?= $sklad->name ?>"><?= $sklad->name ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group row m-b-15">
            <label class="col-sm-4 col-form-label"><h4><b>Soni</b></h4></label>
            <div class="col-sm-8">
              <input type="number" class="form-control" name="soni" placeholder=""/>
              <span class="error_message text-danger"></span>
            </div>
          </div>

          <div class="form-group row m-b-15">
            <label class="col-sm-4 col-form-label"><h4><b>Narxi ($)</b></h4></label>
            <div class="col-sm-8">
              <input type="number" class="form-control" required name="maxsulot_narxi" placeholder=""/>
              <span class="error_message_narx text-danger"></span>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
        <a class="btn btn-danger submit">Buyurtmaga qo'shish</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Sotish -->
<div class="modal fade" id="modal-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title">Sotish</h4></div>
      <div class="modal-body">
        <form id="buy" action="<?= Url::toRoute(['order-account/accept'])?>" method="post">
          <div class="form-group row m-b-15 align-items-center">
            <label class="col-sm-3 col-form-label"><h5><b>Umumiy soni:</b></h5></label>
            <div class="col-sm-1"><label><h5><b style="color:red" id="total_product"></b></h5></label></div>
            <label class="col-sm-3 col-form-label"><h5><b>Umumiy narxi ($):</b></h5></label>
            <div class="col-sm-1"><label><h5><b style="color:red" id="total_product_sum"></b></h5></label></div>
          </div>
          <hr/>
          <input type="hidden" class="form-control" name="product_details"/>

          <div class="form-group row m-b-15">
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Sana</b></h5></label>
              <!-- <input class="form-control" style="font-size: 14px;padding:3px;" required type="date" name="order_date" value="<?= date('Y-m-d') ?>"> -->
              <input
                  class="form-control"
                  style="font-size: 14px; padding:3px;"
                  required
                  type="date"
                  name="order_date"
                  value="<?= date('Y-m-d') ?>"
                  <?= $isRole1 ? '' : 'disabled' ?>
              >
            </div>
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Dollar kursi</b></h5></label>
              <input class="form-control" name="dollar_kurs" value="<?= $exchangeRate->dollar ?>"/>
            </div>
          </div>
          <hr/>

          <div class="form-group row m-b-15">
            <div class="col-sm-12">
              <label class="col-sm-8 col-form-label"><h5><b>Jami summa ($)</b></h5></label>
              <input class="form-control" name="summa_dollor" id="dollarToSum"/>
              <span class="error_summa_dollor text-danger"></span>
            </div>
          </div>

          <div class="form-group row m-b-15">
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Chegirma ($)</b></h5></label>
              <input type="number" class="form-control" name="chegirma_summa" value="0"/>
            </div>
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>To'langan summa ($)</b></h5></label>
              <input class="form-control" name="summa_transfer"/>
              <span class="error_summa_transfer text-danger"></span>
            </div>
          </div>

          <div class="form-group row m-b-15">
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Summa kartada</b></h5></label>
              <input type="number" class="form-control" name="summa_karta"/>
              <span class="error_summa_karta text-danger"></span>
            </div>
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Summa so'mda</b></h5></label>
              <input type="number" class="form-control" name="summa_som"/>
              <span class="error_summa_som text-danger"></span>
            </div>
          </div>
          <div class="form-group row m-b-15">
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Qaytim ($)</b></h5></label>
              <input type="number" class="form-control" name="zdacha_dollar"/>
              <span class="error_zdacha_dollar text-danger"></span>
            </div>
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Qaytim so'm</b></h5></label>
              <input type="number" class="form-control" name="zdacha_sum"/>
              <span class="error_zdacha_sum text-danger"></span>
            </div>
          </div>
          <div class="form-group row m-b-15">
            <div class="col-sm-12">
              <label class="col-sm-8 col-form-label"><h5><b>Haydovchi ma'lumotlari</b></h5></label>
              <input type="text" class="form-control" name="driver_info">
            </div>
          </div>

          <div class="form-group row m-b-15">
            <div class="col-sm-12">
              <label class="col-sm-8 col-form-label"><h5><b>Izoh</b></h5></label>
              <textarea class="form-control" name="comment" rows="4"></textarea>
            </div>
          </div>

          <div class="form-group row m-b-15">
            <div class="col-sm-6">
              <label class="col-sm-8 col-form-label"><h5><b>Tasdiqlash:</b></h5></label>
              <input type="checkbox" id="tasdiqCheckbox" style="margin-top:14px" name="tasdiq_check" value="1" checked/>
            </div>
            <div class="col-sm-6">
                <label class="col-sm-8 col-form-label"><h5><b>Tezda tayyorlash:</b></h5></label>
                <input type="checkbox" id="fastOrderCheckbox" name="fast_order">
            </div>
          </div>

          <div class="modal-footer">
            <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
            <button type="submit" id="sellSubmitButton" class="btn btn-danger">Sotishni tasdiqlash</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-dialog-error">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h4 class="modal-title">Xatolik!</h4></div>
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

/** Qolgan JS (sizniki) — o‘zgartirmadim. Faqat brand→category filtri bo‘limini pastda qo‘shdim. */
$this->registerJs(<<<'JS'
function adjustTableClass() {
  var screenWidth = window.innerWidth;
  var table = document.querySelector('.table');
  if (screenWidth <= 1300) {
    table.classList.remove('table');
    table.style.padding = '10px 0';
    table.style.lineHeight = '3';
  } else {
    table.classList.add('table');
  }
}
window.onload = adjustTableClass;
window.onresize = adjustTableClass;

$("#myselect").change(function () {
  var clientId = $(this).val();
  if (clientId) {
    $("#productTableWrapper").css("pointer-events", "auto");
    $("#productTableWrapper").css("opacity", "1");
  } else {
    $("#productTableWrapper").css("pointer-events", "none");
    $("#productTableWrapper").css("opacity", "0.5");
  }
});

$("select#myselect").change(function(){
  var client_id =$('select[name="customer_name"]').val();
  let action = $(this).attr("action");
  let payload = { client_id: $('select[name="customer_name"]').val() };

  $.ajax({ url: action, data: payload, method: "GET" })
    .done(function(data) {
      $("#qarz_client_summ").text(data);
      var payBtn = document.getElementById('payButton');
      if (payBtn) payBtn.disabled = (data == 0);
    });

  var sellButton = document.getElementById('sellButton');
  if (sellButton) sellButton.disabled = !this.value;

  var payButton = document.getElementById('payButton');
  if (payButton) payButton.disabled = !this.value;
});

function loadMoreContent() {
  var table = document.getElementById("myTable");
  var wrapper = document.querySelector(".table-wrapper");
  if (!table || !wrapper) return;
  if (wrapper.scrollTop + wrapper.clientHeight >= table.scrollHeight) {
    var tbody = table.querySelector("tbody");
    for (var i = 0; i < 10; i++) {
      var newRow = document.createElement("tr");
      newRow.innerHTML = "<td>New Data</td><td>New Data</td>";
      tbody.appendChild(newRow);
    }
  }
}

/* Sizning eski #cars filtr kodingiz — element yo'q bo'lsa ham zarar qilmaydi */
$('#cars').on('change', function(e){
  e.preventDefault();
  const value = ($(this).val() || '').toUpperCase();
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
      const className = ( $(this).attr('class') || '' ).toUpperCase();
      if(className.indexOf('AMOUNT') > -1){
        $(this).toggle(className == value + '-AMOUNT')
      }
    });
  } else {
    $("#showRes tr").show();
  }
});


function toFloat(val) {
  if (val === null || val === undefined) return NaN;
  val = String(val).trim();
  // vergulni nuqtaga o‘tkazamiz
  val = val.replace(',', '.');
  return parseFloat(val);
}

$("#qarztul").submit(function(event){
  event.preventDefault();
  var payButton = document.getElementById('payButton');
  if (payButton){ payButton.disabled = true; payButton.innerHTML = 'Jarayonda…'; }

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
    tul_qarz_zdacha_dollar: $('input[name="tul_qarz_zdacha_dollar"]').val(),
    tul_qarz_zdacha_sum: $('input[name="tul_qarz_zdacha_sum"]').val(),
  };

  let hasError = false;
  let v1 = parseFloat($('input[name="tul_qarz_sum_dollar"]').val());
  if (isNaN(v1) || v1 < 0) { $(".error_tul_qarz_sum_dollar").text("Jami Summa ($) ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_sum_dollar").text(""); }

  let v2 = parseFloat($('input[name="tul_qarz_sum_transfer"]').val());
  if (isNaN(v2) || v2 < 0) { $(".error_tul_qarz_sum_transfer").text("Summa ($) ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_sum_transfer").text(""); }

  let v3 = parseFloat($('input[name="tul_qarz_sum_som"]').val());
  if (isNaN(v3) || v3 < 0) { $(".error_tul_qarz_sum_som").text("Summa so'm ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_sum_som").text(""); }

  let v4 = parseFloat($('input[name="tul_qarz_summ_cart"]').val());
  if (isNaN(v4) || v4 < 0) { $(".error_tul_qarz_summ_cart").text("Summa karta ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_summ_cart").text(""); }

  let v5 = parseFloat($('input[name="tul_qarz_zdacha_dollar"]').val());
  if (isNaN(v5) || v5 < 0) { $(".error_tul_qarz_zdacha_dollar").text("Qaytim ($) ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_zdacha_dollar").text(""); }

  let v6 = parseFloat($('input[name="tul_qarz_zdacha_sum"]').val());
  if (isNaN(v6) || v6 < 0) { $(".error_tul_qarz_zdacha_sum").text("Qaytim so'm ni kiriting."); hasError = true; }
  else { $(".error_tul_qarz_zdacha_sum").text(""); }

  if (hasError) {
    if (payButton){ payButton.disabled = false; payButton.innerHTML = "Qarzni to'lash"; }
    return;
  }

  $.ajax({ url: action, data: payload, method: "POST" })
    .done(function(data) {
      alert(data);
      if (payButton){ payButton.disabled = false; payButton.innerHTML = "Qarzni to'lash"; }
    })
    .fail(function() {
      if (payButton){ payButton.disabled = false; payButton.innerHTML = "Qarzni to'lash"; }
    });
});

$("#buy").submit(function(event){
  event.preventDefault();
  let action = $(this).attr("action");

  let payload = {
    customer_name: $('select[name="customer_name"]').val(),
    jami_qarzi: $("#qarz_client_summ").text(),
    order_date: $('input[name="order_date"]').val(),
    dollar_kurs: $('input[name="dollar_kurs"]').val(),
    chegirma_summa: $('input[name="chegirma_summa"]').val(),
    summa_dollor: $('input[name="summa_dollor"]').val(),
    dollar_sumda: $('input[name="dollar_sumda"]').val(),
    summa_som: $('input[name="summa_som"]').val(),
    summa_karta: $('input[name="summa_karta"]').val(),
    summa_transfer: $('input[name="summa_transfer"]').val(),
    zdacha_dollar: $('input[name="zdacha_dollar"]').val(),
    zdacha_sum: $('input[name="zdacha_sum"]').val(),
    comment: $('textarea[name="comment"]').val(),
    driver_info: $('input[name="driver_info"]').val(),
    total: $("#count_porduct").text(),
    count: $("#total_product").text(),
    all_sum: $("#total_product_sum").text(),
    product_details: $('input[name="product_details"]').val(),
    tasdiq_check: $('input[name="tasdiq_check"]').val(),
    fast_order: $('#fastOrderCheckbox').is(':checked') ? 1 : 0,
  };

  let hasError = false;
  let s1 = parseFloat($('input[name="summa_dollor"]').val());
  if (isNaN(s1) || s1 < 0) { $(".error_summa_dollor").text("Jami to'langan summa ($) ni kiriting."); hasError = true; } else { $(".error_summa_dollor").text(""); }

  let s2 = parseFloat($('input[name="summa_transfer"]').val());
  if (isNaN(s2) || s2 < 0) { $(".error_summa_transfer").text("To'langan summa ($) ni kiriting."); hasError = true; } else { $(".error_summa_transfer").text(""); }

  let s3 = parseFloat($('input[name="summa_som"]').val());
  if (isNaN(s3) || s3 < 0) { $(".error_summa_som").text("To'langan summa so'mda ni kiriting."); hasError = true; } else { $(".error_summa_som").text(""); }

  let s4 = parseFloat($('input[name="summa_karta"]').val());
  if (isNaN(s4) || s4 < 0) { $(".error_summa_karta").text("To'langan summa kartada ni kiriting."); hasError = true; } else { $(".error_summa_karta").text(""); }

  let s5 = parseFloat($('input[name="zdacha_dollar"]').val());
  if (isNaN(s5) || s5 < 0) { $(".error_zdacha_dollar").text("Qaytim ($) ni kiriting."); hasError = true; } else { $(".error_zdacha_dollar").text(""); }

  let s6 = parseFloat($('input[name="zdacha_sum"]').val());
  if (isNaN(s6) || s6 < 0) { $(".error_zdacha_sum").text("Qaytim (so'mda) ni kiriting."); hasError = true; } else { $(".error_zdacha_sum").text(""); }

  if (hasError) return false;

  $.ajax({
    url: action,
    data: payload,
    method: "POST",
    beforeSend: function() {
      $("#sellSubmitButton").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Jarayonda...');
    }
  }).done(function(data) {
    alert(data);
    $("#modal-dialog2").modal("hide");
    $(this).addClass("done");
  }).fail(function(jqXHR, textStatus, errorThrown) {
    console.error("Request failed: " + textStatus + ", " + errorThrown);
    $("#modal-dialog2").modal("hide");
  }).always(function() {
    $("#sellSubmitButton").prop("disabled", false).html('Sotishni tasdiqlash');
    $("#modal-dialog2").modal("hide");
  });
});

$('#tasdiqCheckbox').on('change', function() {
  if ($(this).is(':checked')) $(this).val(1);
  else $(this).val(0);
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
        brand_id: $(this).children().eq(10).text(),             // 🔥 yangi
        product_category_id: $(this).children().eq(11).text(),  // 🔥 yangi
      });

    }
  });
  console.log("product_details:", product_details);
  
  $('input[name="product_details"]').val(JSON.stringify(product_details));
  $('#count_porduct').text(products_count);
  $("#total_product").text(all_total);
  $("#total_product_sum").text(all_total_sum);

  if(all_total == 0){
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
  let product_id = $(this).children().eq(6).text();
  // YANGI QO‘SHIMCHA: brand_id va product_category_id ni data-* dan olish
  let brand_id = $(this).data('brand-id') || '';
  let product_category_id = $(this).data('category-id') || '';

  $("#marka").text(mark);
  $("#name").text(name);
  $("#size").text(size);
  $("#maxsulot_tipi").text(maxsulot_tipi);

  if (count < 0) count = 0;

  $('input[name="product_id"]').val(product_id);
  $('input[name="soni"]').val(count);
  $('input[name="size"]').val(size);
  $('input[name="maxsulot_tipi"]').val(maxsulot_tipi);
  $('input[name="key"]').val($(this).attr('id'));
  // brand_id va category_id ni ham formaga yozamiz
  $('input[name="brand_id"]').val(brand_id);
  $('input[name="product_category_id"]').val(product_category_id);

  $("#modal-dialog2").modal();
});

$(".submit").on("click", function(event){
  event.preventDefault();
  $(".error_message").text("");

  let count_product   = parseInt($('input[name="soni"]').val() || '0', 10);
  let price           = $('input[name="maxsulot_narxi"]').val();
  let maxsulot_joyi   = $('select[name="maxsulot_joyi"]').val();
  let name            = $('#name').text();
  let marka           = $('#marka').text();
  let product_id      = $('input[name="product_id"]').val();
  let size            = $('input[name="size"]').val();
  let maxsulot_tipi   = $('input[name="maxsulot_tipi"]').val();

  let key       = $('input[name="key"]').val();
  let countAll  = parseInt($("#backet").attr("data-count") || '0', 10);
  let all_sum   = parseFloat($("#backet").attr("data-count-sum") || '0');
  let increment = parseInt($("#backet").attr("data-increment") || '0', 10) + 1;

  countAll = countAll + count_product;
  all_sum  = all_sum + (parseFloat(price || '0') * count_product);

  let count_old = parseInt($("#" + key).children().eq(4).text() || '0', 10);

  if(count_product === 0){
    $(".error_message").text("0 ta buyurtma berib bo'lmaydi");
    return false;
  }
  if (!String(price || '').length) {
    $(".error_message_narx").text("Mahsulot narxini kiriting.");
    return false;
  }
  count_old = count_old - count_product;

  var id = (key || '').split("_");
  var aVal = $("." + id[0] + "-amount").children().eq(2).text();
  aVal = (parseInt(aVal || '0', 10) - count_product);
  $("." + id[0] + "-amount").children().eq(2).text(aVal);

  let brand_id = $('input[name="brand_id"]').val() || '';
  let product_category_id = $('input[name="product_category_id"]').val() || '';


  $("#add").before(
    "<tr>" +
    "<td style='background-color:#efdfdf;'><b>" + increment + "</b></td>" +
    "<td style='background-color:#efdfdf;'>" + maxsulot_joyi + "</td>" +
    "<td style='background-color:#efdfdf;'><b>" + marka + "</b></td>" +
    "<td style='background-color:#efdfdf;'>" + name + "</td>" +
    "<td style='background-color:#efdfdf;'><b>" + size + "</b></td>" +
    "<td style='background-color:#efdfdf;'>" + maxsulot_tipi + "</td>" +
    "<td style='background-color:#efdfdf;'><b>" + price + "</b></td>" +
    "<td style='background-color:#efdfdf;'>" + count_product + "</td>" +
    "<td style='background-color:#efdfdf;'><b>" + Math.round(parseFloat(price || '0') * count_product * 100)/100 + "</b></td>" +
    "<td style='display:none;'>" + product_id + "</td>" +
    "<td style='display:none;' class='brand-id'>" + brand_id + "</td>" +
    "<td style='display:none;' class='category-id'>" + product_category_id + "</td>" +
    "<td style='background-color:#efdfdf;'><button class='btn btn-sm btn-danger delete-product'><i class='glyphicon glyphicon-remove'></i></button></td>" +
    "</tr>"
  );


  $("#backet").attr("data-increment", increment);
  $("#backet").attr("data-count", countAll);
  $("#backet").attr("data-count-sum", all_sum);
  $("#all").text(countAll);
  $("#all_sum").text(all_sum);

  $("#modal-dialog2").modal('toggle');
});

$(document).on("click", ".delete-product", function() {
  let row = $(this).closest("tr");
  let decrementCount = parseInt(row.find("td:eq(7)").text() || '0', 10);
  let increment = parseInt($("#backet").attr("data-increment") || '0', 10);

  let currentCount = parseInt($("#backet").attr("data-count") || '0', 10);
  let currentSum   = parseFloat($("#backet").attr("data-count-sum") || '0');

  currentCount -= decrementCount;
  currentSum   -= parseFloat(row.find("td:eq(6)").text() || '0') * decrementCount;

  $("#backet").attr("data-count", currentCount);
  $("#backet").attr("data-count-sum", currentSum);
  $("#all").text(currentCount);
  $("#all_sum").text(currentSum);

  row.remove();
  increment--;
  $("#backet").attr("data-increment", increment);

  let i = 1;
  $('#backet').find('tr').each(function() {
    if($(this).attr('id') !== 'add'){
      $(this).find("td:eq(0)").html(i);
      if (i === increment) return false;
      i++;
    }
  });
});

document.getElementById('toggleSwitch')?.addEventListener('change', function() {
  var toggleContent = document.getElementById('toggleContent');
  var sellButton = document.getElementById('sellButton');
  if (this.checked) {
    toggleContent?.classList.remove('hidden');
    if (sellButton) sellButton.disabled = true;
    $('input[name="tul_qarz_dollar_kurs"]').val($('#dollarToSum').val());
  } else {
    toggleContent?.classList.add('hidden');
    if (sellButton) sellButton.disabled = false;
  }
});

/* productSearch elementi endi yo‘q — himoya bilan qoldiramiz */
var ps = document.getElementById("productSearch");
if (ps){
  ps.addEventListener("input", function () {
    const searchValue = (this.value || '').toLowerCase();
    const rows = document.querySelectorAll("#showRes .handle");
    rows.forEach(function(row){
      const productName = row.dataset.name || "";
      row.style.display = productName.includes(searchValue) ? "" : "none";
    });
  });
}
var ci = document.getElementById("clearInputs");
if (ci){
  ci.addEventListener("click", function () {
    $('#brandFilter').val(null).trigger('change');
    $('#categoryFilter').val(null).trigger('change');
    const rows = document.querySelectorAll("#showRes .handle");
    rows.forEach(function(row){ row.style.display = ""; });
  });
}

/***** BRAND → CATEGORY yuklash + jadvalni filtrlash (asosiy qo'shimcha) *****/

// Kategoriyalarni AJAX bilan yuklash
function bcLoadCategories(brandId){
  var url = $('#brandFilter').attr('data-url');    // endpoint
  var catSelect = $('#categoryFilter');

  if(!brandId){
    catSelect.prop('disabled', true).val(null).trigger('change');
    catSelect.html('');
    bcApplyFilter();
    return;
  }

  $.get(url, { brand_id: brandId })
    .done(function(res){
      var items = (res && res.results) ? res.results : [];
      catSelect.prop('disabled', items.length === 0).html('');
      items.forEach(function(it){
        catSelect.append(new Option(it.text, it.id, false, false));
      });
      catSelect.val(null).trigger('change'); // nothing selected
      bcApplyFilter();
    })
    .fail(function(){
      catSelect.prop('disabled', true).val(null).trigger('change');
      catSelect.html('');
      bcApplyFilter();
    });
}

// Jadvalga filtrlash (brand + category)
function bcApplyFilter(){
  var brandId   = $('#brandFilter').val();
  var brandName = brandId ? (window.BRAND_MAP ? (BRAND_MAP[brandId] || '') : '') : '';
  var catText   = ($('#categoryFilter option:selected').text() || '').toLowerCase();

  // .handle qatorlari
  $('#showRes .handle').each(function(){
    var $r       = $(this);
    var rowBrand = ($r.children().eq(1).text() || '').trim();           // Model ustuni
    var rowCat   = ($r.attr('data-name') || '').toLowerCase();          // data-name = kategoriya nomi (lower)
    var visible  = true;

    if (brandName && rowBrand !== brandName) visible = false;
    if (catText   && rowCat   !== catText)   visible = false;

    $r.toggle(visible);
  });

  // Header qatorlar (.handle-header)
  if (brandName){
    $('#showRes .handle-header').each(function(){
      var hName = ($(this).find('td:first b').text() || '').trim();
      $(this).toggle(hName === brandName);
    });
  } else {
    $('#showRes .handle-header').show();
  }
}

// Brand o'zgarganda — kategoriyalarni yuklab, filtrlash
$('#brandFilter').on('change', function(){
  bcLoadCategories($(this).val());
});

// Kategoriya o'zgarganda — filtrlash
$('#categoryFilter').on('change', function(){
  bcApplyFilter();
});

// Dastlabki holat
if($('#brandFilter').val()){
  bcLoadCategories($('#brandFilter').val());
  bcApplyFilter();
}else{
  $('#categoryFilter').prop('disabled', true).html('').trigger('change');
}

// "Tozalash" tugmasi: brand & kategoriya ni reset qiladi, jadvalni tiklaydi
$('#bcClearFilters').on('click', function () {
  // Brandni tozalash
  $('#brandFilter').val(null).trigger('change.select2');

  // Kategoriyani tozalash va o‘chirib qo‘yish
  var $cat = $('#categoryFilter');
  $cat.prop('disabled', true).html('').val(null).trigger('change.select2');

  // Ichki yordamchi funksiyalar orqali jadvalni qayta ko‘rsatish
  if (typeof bcLoadCategories === 'function') bcLoadCategories(null);
  if (typeof bcApplyFilter   === 'function') bcApplyFilter();
  else {
    // fallback: hammasini ko‘rsatib yuboramiz
    $('#showRes .handle').show();
    $('#showRes .handle-header').show();
  }
});

$(document).on('keydown', '#modal-dialog2 input', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    $('#modal-dialog2 .submit').trigger('click');
  }
});

JS
);
