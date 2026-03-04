<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;

use app\models\ExchangeRate;
use app\models\ProductAccountHistory;
use app\models\Brands;
use app\models\ProductCategory;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAccountHistory */
/* @var $form yii\widgets\ActiveForm */

$historyRows = ProductAccountHistory::find()
    ->where(['order_account_history_id' => $model->id])
    ->all();

/**
 * MultipleInput uchun data + Select2’lar uchun boshlang‘ich ro‘yxatlarni tayyorlaymiz
 */
$data        = [];
$categoryIds = [];
$sizeValues  = [];
$typeIds     = [];
$initialTotal = 0; // jami summa hisoblash

foreach ($historyRows as $row) {
    $data[] = [
        'type_sklad_id'       => $row->type_sklad_id,
        'brand_id'            => $row->brand_id,
        'product_category_id' => $row->product_category_id,
        'size'                => $row->size,
        'type'                => $row->type,
        'count'               => $row->count,
        'price'               => $row->price,
    ];

    if ($row->product_category_id) {
        $categoryIds[] = (int)$row->product_category_id;
    }
    if ($row->size !== null && $row->size !== '') {
        $sizeValues[] = (string)$row->size;
    }
    if ($row->type !== null && $row->type !== '') {
        $typeIds[] = (int)$row->type;
    }

    $initialTotal += (float)$row->price * (float)$row->count;
}

// agar modelda sum_all_pro bo'sh bo'lsa, hisoblangan qiymatni qo'yamiz
if (empty($model->sum_all_pro)) {
    $model->sum_all_pro = $initialTotal;
}

$categoryIds = array_unique($categoryIds);
$sizeValues  = array_unique($sizeValues);
$typeIds     = array_unique($typeIds);

/** Select2 uchun boshlang‘ich ro'yxatlar – faqat ushbu buyurtmada ishlatilgan qiymatlar */
$categoryList = [];
if ($categoryIds) {
    $cats = ProductCategory::find()
        ->select(['id', 'name'])
        ->where(['id' => $categoryIds])
        ->asArray()
        ->all();
    foreach ($cats as $cat) {
        $categoryList[$cat['id']] = $cat['name'];
    }
}

$sizeList = [];
foreach ($sizeValues as $sv) {
    $sizeList[$sv] = $sv;
}

$typeList = [];
foreach ($typeIds as $tid) {
    $typeList[$tid] = ProductCategory::getTypeView($tid);
}

$exchangeRate = ExchangeRate::findOne(1);
$isDisabled   = $model->order_account_status ? 'disabled' : '';
$brandList    = Brands::listActive();

// AJAX endpointlar
$urlCats      = Url::to(['order-account-history/categories-by-brand']);
$urlSizesOnly = Url::to(['order-account-history/sizes-types-by-category']); // brand+category -> sizes (+ maybe types)
$urlTypesBySz = Url::to(['order-account-history/types-by-size']);           // brand+category+size -> types
?>
<div class="panel panel-inverse user-index">
  <div class="panel-heading">
    <div class="panel-heading-btn">
      <a href="/order-account-history/<?= Html::encode($type)?>" class="btn btn-xs btn-warning">
        <i class="fa fa-reply"></i> Orqaga qaytish
      </a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand" title="Во весь экран">
        <i class="fa fa-expand"></i>
      </a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload" title="Обновить">
        <i class="fa fa-repeat"></i>
      </a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse">
        <i class="fa fa-minus"></i>
      </a>
      <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove">
        <i class="fa fa-times"></i>
      </a>
    </div>
    <h4 class="panel-title">
      <?= Html::encode($model->client->fio) ?> ning <?= Html::encode($model->date) ?> sanadagi mahsulotlarini o'zgartirish
    </h4>
  </div>

  <div class="panel-body">
    <?php
    $form = ActiveForm::begin([
        'id'      => 'order-form',
        'options' => ['novalidate' => true],
    ]);
    ?>

      <div class="row">
        <div class="col-md-3">
          <?= $form->field($model, 'client_id')->widget(\kartik\select2\Select2::class, [
              'data' => $model->getClients(),
              'disabled' => true,
              'options' => [
                  'placeholder' => Yii::t('app','Tanlang...'),
                  'onchange' => '$.post("/order-account/qarzs?id="+$(this).val(), function(d){$("#total_debts").val(d);});'
              ],
              'pluginOptions' => ['allowClear' => true],
          ])->label('Mijoz') ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'total_debt_old')->textInput([
              'value'   => $client_total_debt,
              'id'      => 'total_debts',
              'readonly'=> true,
          ]) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'date')->textInput(['required'=>true,'readonly'=>true]) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'exchange_rate')->textInput(['type'=>'number','required'=>true]) ?>
        </div>
      </div>

      <div class="col-md-12">
        <?= $form->field($model, 'allValue')->widget(MultipleInput::class, [
          'id' => 'my_id',
          'data' => $data,
          'allowEmptyList' => true,
          'enableGuessTitle' => true,
          'columns' => [
            [
              'name'  => 'type_sklad_id',
              'title' => 'Joy',
              'type'  => \kartik\select2\Select2::class,
              'options' => [
                'data' => $model->getTypeSklads(),
                'options' => [
                  'placeholder' => 'Tanlang...',
                  'class' => 'input-priority mi-sklad mi-req',
                ],
                'pluginOptions' => ['allowClear'=>true],
              ],
              'headerOptions' => ['style'=>'width:180px;']
            ],
            [
              'name'  => 'brand_id',
              'title' => 'Model',
              'type'  => \kartik\select2\Select2::class,
              'options' => [
                'data' => $brandList,
                'options' => [
                  'placeholder'=>'Tanlang...',
                  'class'=>'input-priority mi-brand mi-req',
                ],
                'pluginOptions' => ['allowClear'=>true],
              ],
              'headerOptions' => ['style'=>'width:300px;']
            ],
            [
              'name'  => 'product_category_id',
              'title' => 'Nomi',
              'type'  => \kartik\select2\Select2::class,
              'options' => [
                'data' => $categoryList,
                'options' => [
                  'placeholder'=>'Tanlang...',
                  'class'=>'input-priority mi-category mi-req',
                ],
                'pluginOptions' => ['allowClear'=>true],
              ],
              'headerOptions' => ['style'=>'width:300px;']
            ],
            [
              'name'  => 'size',
              'title' => 'O\'lchami',
              'type'  => \kartik\select2\Select2::class,
              'options' => [
                'data' => $sizeList,
                'options' => [
                  'placeholder'=>'Tanlang...',
                  'class'=>'input-priority mi-size mi-req',
                ],
                'pluginOptions' => ['allowClear'=>true],
              ],
              'headerOptions' => ['style'=>'width:160px;']
            ],
            [
              'name'  => 'type',
              'title' => 'Tip',
              'type'  => \kartik\select2\Select2::class,
              'options' => [
                'data' => $typeList,
                'options' => [
                  'placeholder'=>'Tanlang...',
                  'class'=>'input-priority mi-type mi-req',
                ],
                'pluginOptions' => ['allowClear'=>true],
              ],
              'headerOptions' => ['style'=>'width:300px;']
            ],
            [
              'name'  => 'count',
              'title' => 'Soni',
              'headerOptions' => ['style'=>'width:140px;'], 
              'enableError' => true,
              'options' => [
                'type'     => 'number',
                'min'      => 1,
                'step'     => '1',
                'required' => true,
                'class'    => 'input-priority target mi-count',
              ]
            ],
            [
              'name'  => 'price',
              'title' => 'Narxi',
              'headerOptions' => ['style'=>'width:140px;'], 
              'enableError' => true,
              'options' => [
                'type'     => 'number',
                'min'      => 0,
                'step'     => '0.01',
                'required' => true,
                'class'    => 'input-priority mi-price',
              ]
            ],
          ]
        ])->label(''); ?>
      </div>

      <div class="row">
          <div class="col-md-9"></div>
          <div class="col-md-2">
            <?= $form->field($model, 'sum_all_pro')->textInput([
                'readonly' => true,
                'style'    => 'margin-left:35px;',
                'id'       => 'sum_all_pro',
                'value'    => $model->sum_all_pro,
            ]) ?>
          </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php
          $sumDollarVal = ($model->sum_dollar && $model->sum_dollar != 0) ? $model->sum_dollar : '';
          echo $form->field($model, 'sum_dollar')->textInput([
              'id' => 'all-sum-dollar',
              'value' => $sumDollarVal,
              'required' => true,
          ])->label("Jami summa dollarda ($)");
          ?>
        </div>

        <div class="col-md-2">
          <?= $form->field($model, 'discount_amount')->textInput([
              'type' => 'number',
          ])->label('Jami chegirma ($)') ?>
        </div>

        <div class="col-md-2">
          <?php
          $sumTransfersVal = ($model->sum_transfers && $model->sum_transfers != 0) ? $model->sum_transfers : '';
          echo $form->field($model, 'sum_transfers')->textInput([
              'type' => 'number',
              'id' => 'sum-dollar',
              'value' => $sumTransfersVal,
              'required' => true,
          ])->label("Summa dollarda ($)");
          ?>
        </div>

        <div class="col-md-2">
          <?php
          $sumSomVal = ($model->sum_som && $model->sum_som != 0) ? $model->sum_som : '';
          echo $form->field($model, 'sum_som')->textInput([
              'type' => 'number',
              'id' => 'sum-som',
              'value' => $sumSomVal,
              'required' => true,
          ])->label("Summa so'mda");
          ?>
        </div>

        <div class="col-md-2">
          <?php
          $sumCartVal = ($model->sum_cart && $model->sum_cart != 0) ? $model->sum_cart : '';
          echo $form->field($model, 'sum_cart')->textInput([
              'type' => 'number',
              'id' => 'sum-cart',
              'value' => $sumCartVal,
              'required' => true,
          ])->label("Summa kartada");
          ?>
        </div>
      </div>

      <div class="row">
          <div class="col-md-2">
          <?php
          $zdachaDollarVal = ($model->zdacha_dollar && $model->zdacha_dollar != 0) ? $model->zdacha_dollar : '';
          echo $form->field($model, 'zdacha_dollar')->textInput([
              // 'type' => 'number',
              'id' => 'qaytim-dollar',
              'value' => $zdachaDollarVal,
              'required' => true,
          ])->label("Qaytim ($)");
          ?>
        </div>
          <div class="col-md-2">
          <?php
          $zdachaSumVal = ($model->zdacha_sum && $model->zdacha_sum != 0) ? $model->zdacha_sum : '';
          echo $form->field($model, 'zdacha_sum')->textInput([
              'type' => 'number',
              'id' => 'qaytim-som',
              'value' => $zdachaSumVal,
              'required' => true,
          ])->label("Qaytim so'mda");
          ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'comment')->textInput([
              'id'=>'id-comment',
              'required'=>true
          ])->label('Izoh') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'driver_info')->textInput([
              'id'=>'id-driver-info'
          ])->label("Haydovchi ma'lumotlari") ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <?= $form->field($model,'order_account_status')
              ->checkbox(['disabled'=>($model->order_account_status == 1)])
              ->label("<b style='font-size:16px;color:red;margin-top:-230px'>Tasdiqlash: </b>") ?>
        </div>
        <div class="col-md-5"></div>
        <div class="col-md-2">
          <?= $form->field($model,'fast_order')
              ->checkbox()
              ->label("<b style='font-size:16px;color:#f59c1a;margin-top:-230px'>Tezda tayyorlash: </b>") ?>
        </div>
      </div>

      <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
          <?= Html::submitButton(
              $model->isNewRecord ? 'Qo\'shish' : 'O\'zgartirish',
              ['class'=>$model->isNewRecord?'btn btn-success':'btn btn-primary','style'=>'width:100%']
          ) ?>
        </div>
      <?php } ?>

    <?php ActiveForm::end(); ?>
  </div>
</div>

<?php
// xatolik ko‘rinishlari
$this->registerCss("
.select2-selection.is-invalid { border-color:#dc3545 !important; }
.is-invalid { border-color:#dc3545 !important; }
.mi-row-error{ color:#dc3545; font-size:12px; margin-top:4px; }
");

// JS ga URL larni beramiz
$this->registerJs(
  'window.__OA_URLS__ = '.Json::htmlEncode([
    'cats'  => $urlCats,
    'sizes' => $urlSizesOnly,
    'types' => $urlTypesBySz,
  ]).';',
  View::POS_END
);

// Asosiy JS
$this->registerJs(<<<'JS'
// --- 0, 0.00 bo'lsa kirishda bo'shatamiz ---
(function clearZerosOnLoad(){
  ['#all-sum-dollar', '#sum-dollar', '#sum-som', '#sum-cart', '#qaytim-som', '#qaytim-dollar'].forEach(function(id){
    const $i = $(id);
    if(!$i.length) return;
    const v = ($i.val()||'').trim();
    if (v === '0' || v === '0.0' || v === '0.00' || v === '0,0' || v === '0,00') {
      $i.val('');
    }
  });
})();

// --- required bo'lgan pastki inputlar ---
function markInputInvalid($inp, msg){
  $inp.addClass('is-invalid');
  if ($inp.next('.mi-row-error').length===0){
    $inp.after('<div class="mi-row-error">'+msg+'</div>');
  }
}
function clearInputInvalid($inp){
  $inp.removeClass('is-invalid');
  $inp.next('.mi-row-error').remove();
}
$(document).on('input', '#all-sum-dollar, #sum-dollar, #sum-som, #sum-cart, #id-comment, #qaytim-som, #qaytim-dollar', function(){
  clearInputInvalid($(this));
});

// ---- Select2 helper ----
function fillSelect($select, data, selectedVal){
  var valueToSet = (typeof selectedVal !== 'undefined') ? selectedVal : null;

  $select.empty();
  $select.append(new Option('Tanlang...', '', false, false));

  (data || []).forEach(function(item){
    var id, text;
    if (typeof item === 'object' && item !== null){
      id   = item.id;
      text = item.text;
    } else {
      id   = item;
      text = item;
    }
    $select.append(new Option(text, id, false, false));
  });

  if (valueToSet !== null && valueToSet !== '') {
    $select.val(String(valueToSet));
  } else {
    $select.val('');
  }

  $select.trigger('change.select2');
}

function markSelect2Invalid($sel,msg){
  const $c=$sel.next('.select2').find('.select2-selection');
  $c.addClass('is-invalid');
  if ($c.parent().next('.mi-row-error').length===0){
    $c.parent().after('<div class="mi-row-error">'+msg+'</div>');
  }
}
function clearSelect2Invalid($sel){
  const $c=$sel.next('.select2').find('.select2-selection');
  $c.removeClass('is-invalid');
  $c.parent().next('.mi-row-error').remove();
}

var urlCats  = window.__OA_URLS__.cats;
var urlSizes = window.__OA_URLS__.sizes;
var urlTypes = window.__OA_URLS__.types;

// --------------- BRAND -> CATEGORY -----------------
function fetchCategories($row, brandId, selectedCatId){
  var $catSel = $row.find('select.mi-category');

  if(!brandId){
    fillSelect($catSel, [], null);
    fillSelect($row.find('select.mi-size'), [], null);
    fillSelect($row.find('select.mi-type'), [], null);
    return;
  }

  var catToSet = (selectedCatId !== undefined && selectedCatId !== null && selectedCatId !== '')
      ? selectedCatId
      : null;

  $.getJSON(urlCats,{brand_id:brandId},function(res){
    fillSelect($catSel, res.categories || [], catToSet);
  });
}

// --------------- CATEGORY -> SIZE (faqat size, typega tegmaydi) -----------------
function fetchSizesOnly($row,brandId,categoryId,selectedSize){
  var $sizeSel = $row.find('select.mi-size');

  if(!brandId || !categoryId){
    fillSelect($sizeSel, [], null);
    return;
  }

  var sizeToSet = (selectedSize !== undefined && selectedSize !== null && selectedSize !== '')
      ? selectedSize
      : null;

  $.getJSON(urlSizes,{brand_id:brandId,category_id:categoryId},function(res){
    fillSelect($sizeSel, res.sizes || [], sizeToSet);
  });
}

// --------------- SIZE -> TYPE -----------------
function fetchTypesBySize($row,brandId,categoryId,size,selectedTypeId){
  var $typeSel = $row.find('select.mi-type');

  if(!brandId || !categoryId || !size){
    fillSelect($typeSel, [], null);
    return;
  }

  var typeToSet = (selectedTypeId !== undefined && selectedTypeId !== null && selectedTypeId !== '')
      ? selectedTypeId
      : null;

  $.getJSON(urlTypes,{brand_id:brandId,category_id:categoryId,size:size},function(res){
    var types = res.types || [];

    if (!typeToSet && types.length === 1) {
      typeToSet = String(types[0].id);  // bitta tip bo'lsa avtomatik tanlaymiz
    }

    fillSelect($typeSel, types, typeToSet);
  });
}

// --- Brand o'zgarganda: category/size/type tozalanadi va yangidan yuklanadi ---
$(document).on('change','select.mi-brand',function(){
  const $r   = $(this).closest('tr');
  const $cat = $r.find('select.mi-category');
  const $sz  = $r.find('select.mi-size');
  const $tp  = $r.find('select.mi-type');

  $cat.val('').trigger('change');
  $sz.val('').trigger('change');
  $tp.val('').trigger('change');

  fetchCategories($r,$(this).val(),null);
  clearSelect2Invalid($(this));
});

// 🔴 Kategoriya o'zgarganda: size va type tozalanadi + yangi size list yuklanadi
$(document).on('change','select.mi-category',function(){
  const $r  = $(this).closest('tr');
  const $sz = $r.find('select.mi-size');
  const $tp = $r.find('select.mi-type');

  $sz.val('').trigger('change');
  $tp.val('').trigger('change');

  fetchSizesOnly($r,$r.find('select.mi-brand').val(),$(this).val(),null);
  clearSelect2Invalid($(this));
});

// 🔴 Size o'zgarganda: type tozalanadi + yangi type list yuklanadi
$(document).on('change','select.mi-size',function(){
  const $r  = $(this).closest('tr');
  const sizeVal = $(this).val();
  const brandId = $r.find('select.mi-brand').val();
  const catId   = $r.find('select.mi-category').val();

  const $tp = $r.find('select.mi-type');
  $tp.val('').trigger('change');

  fetchTypesBySize($r, brandId, catId, sizeVal, null);
  clearSelect2Invalid($(this));
});

$(document).on('change','select.mi-type',function(){
  clearSelect2Invalid($(this));
});

$(document).on('input','.mi-count,.mi-price',function(){
  clearInputInvalid($(this));
});

// --- CATEGORY UCHUN QALAMCHA EDIT TUGMASI ---
// faqat select2 ni ochadi
function initCategoryEditButtons(context){
  var $ctx = context ? $(context) : $('#my_id');
  $ctx.find('select.mi-category').each(function(){
    var $sel = $(this);
    var $container = $sel.next('.select2');
    if(!$container.length) return;

    var $clear = $container.find('.select2-selection__clear');
    if(!$clear.length) return;

    if ($clear.data('as-edit')) return;
    $clear.data('as-edit', true);

    $clear.off('click');

    $clear.html('<i class="fa fa-pencil"></i>');
    $clear.css({cursor:'pointer'});

    $clear.on('click', function(e){
      e.preventDefault();
      e.stopPropagation();

      setTimeout(function(){
        $sel.select2('open');
      }, 0);
    });
  });
}

// --- Submit: barcha custom tekshiruvlar ---
$('#order-form').on('submit', function(e){
  let hasError=false;
  $('.mi-row-error').remove();
  $('.select2-selection').removeClass('is-invalid');

  ['#all-sum-dollar','#sum-dollar','#sum-som','#sum-cart','#id-comment','#qaytim-som','#qaytim-dollar'].forEach(function(id){
    const $i=$(id);
    if($i.length && ($.trim($i.val())==='')){
      hasError=true;
      markInputInvalid($i,'Bu maydon to‘ldirilishi shart');
    }
  });

  $('#my_id').find('tr.multiple-input-list__item').each(function(){
    const $r=$(this);
    const $sk=$r.find('select.mi-sklad');
    const $br=$r.find('select.mi-brand');
    const $ct=$r.find('select.mi-category');
    const $sz=$r.find('select.mi-size');
    const $tp=$r.find('select.mi-type');
    const $cnt=$r.find('input[name*="[count]"]');
    const $prc=$r.find('input[name*="[price]"]');

    if($sk.length && !$sk.val()){markSelect2Invalid($sk,'Majburiy maydon');hasError=true;}
    if($br.length && !$br.val()){markSelect2Invalid($br,'Majburiy maydon');hasError=true;}
    if($ct.length && !$ct.val()){markSelect2Invalid($ct,'Majburiy maydon');hasError=true;}
    if($sz.length && !$sz.val()){markSelect2Invalid($sz,'Majburiy maydon');hasError=true;}
    if($tp.length && !$tp.val()){markSelect2Invalid($tp,'Majburiy maydon');hasError=true;}

    const cnt=parseFloat(($cnt.val()||'').replace(',','.'));
    if(!cnt || cnt<1){hasError=true; markInputInvalid($cnt,'1 dan katta bo‘lsin');}

    const ps=($prc.val()||'').trim();
    const prc=parseFloat(ps.replace(',','.'));
    if(ps===''||isNaN(prc)||prc<0){hasError=true; markInputInvalid($prc,'Qiymat kiriting');}
  });

  if(hasError){
    e.preventDefault();
    const $first=$('.mi-row-error').first();
    if($first.length){
      $('html,body').animate({scrollTop:$first.offset().top-150},250);
    }
    return;
  }
});

// --- Yangi qator qo'shilganda bo'sh qilib qo'yamiz ---
$(document).on('afterAddRow','#my_id',function(e,row){
  const $r=$(row);
  fillSelect($r.find('select.mi-category'),[],null);
  fillSelect($r.find('select.mi-size'),[],null);
  fillSelect($r.find('select.mi-type'),[],null);

  setTimeout(function(){
    initCategoryEditButtons($r);
  }, 0);
});

// --- Jami summa hisoblash ---
function recalcTotalSum() {
  let total = 0;

  $('#my_id').find('tr.multiple-input-list__item').each(function () {
    const $row   = $(this);
    const priceV = ($row.find('input.mi-price').val() || '').toString().replace(',', '.');
    const countV = ($row.find('input.mi-count').val() || '').toString().replace(',', '.');

    const price  = parseFloat(priceV) || 0;
    const count  = parseFloat(countV) || 0;

    if (price && count) {
      total += price * count;
    }
  });

  $('#sum_all_pro').val(total.toFixed(2));
}

// === Sahifa ochilganda mavjud qatorlar uchun brand/category/size/type ni to'liq yuklash ===
$(document).ready(function () {
  $('#my_id').find('tr.multiple-input-list__item').each(function () {
    const $r = $(this);
    const b  = $r.find('select.mi-brand').val();
    const c  = $r.find('select.mi-category').val();
    const s  = $r.find('select.mi-size').val();
    const t  = $r.find('select.mi-type').val();

    if (b) {
      fetchCategories($r, b, c);
    }
    if (b && c) {
      fetchSizesOnly($r, b, c, s);   // faqat size
    }
    if (b && c && s) {
      fetchTypesBySize($r, b, c, s, t); // type
    }
  });

  initCategoryEditButtons(); // qalamcha tugmalari
  recalcTotalSum();          // jami summa
});

// Narx yoki son o'zgarsa – hammasini qayta hisoblaymiz
$(document).on('input change', '.mi-price, .mi-count', function () {
  recalcTotalSum();
});

// Qator o'chirilganda ham qayta hisoblaymiz
$(document).on('click', '.js-input-remove', function () {
  setTimeout(recalcTotalSum, 0);
});
JS
, View::POS_END);
?>
