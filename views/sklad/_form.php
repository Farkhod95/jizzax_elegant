<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;
use app\models\ExchangeRate;
use app\models\MyTotalDebt;
use app\models\Brands;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Tovar qo\'shish';
$this->params['breadcrumbs'][] = $this->title;
$exchangeRate = ExchangeRate::findOne(1);

$brandList    = Brands::listActive();

// AJAX endpointlar
$urlCats      = Url::to(['order-account-history/categories-by-brand']);
$urlSizesOnly = Url::to(['order-account-history/sizes-types-by-category']); // brand+category -> sizes
$urlTypesBySz = Url::to(['order-account-history/types-by-size']);    
?>

<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/warehouse/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Tovar qo'shish</h4>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'order-form', 'options' => ['novalidate' => true]]); ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'dates')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => Yii::t('app','Sanani tanlang...'), 'required'=>True, 'value' => date('d.m.Y')],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose'=>true, 
                            'format' => 'dd.mm.yyyy',
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-2" style="display:none;">
                     <?php if (\Yii::$app->user->identity->permission == 1) {?>
                        <?= $form->field($model, 'exchange_rates')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar])->label("Dollar kursi") ?>
                    <?php }else{?>
                        <?= $form->field($model, 'exchange_rates')->textInput(['type' => 'number', 'required'=>True, 'value' => $exchangeRate->dollar, 'style' => 'display:none;'])->label("") ?>
                    <?php }?>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'allValue')->widget(MultipleInput::className(), [
                        'id' => 'my_id',
                        // 'allowEmptyList' => true,
                        'enableGuessTitle' => true,
                        'columns' => [
                            [
                                'name'  => 'brand_id',
                                'title' => 'Model',
                                'type'  => \kartik\select2\Select2::className(),
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
                                'type'  => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data' => [],
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
                                'type'  => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data' => [],
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
                                'type'  => \kartik\select2\Select2::className(),
                                'options' => [
                                    'data' => [],
                                    'options' => [
                                    'placeholder'=>'Tanlang...',
                                    'class'=>'input-priority mi-type mi-req',
                                    ],
                                    'pluginOptions' => ['allowClear'=>true],
                                ],
                                'headerOptions' => ['style'=>'width:300px;']
                            ],  
                            [
                                'name'  => 'price',
                                'title' => 'Narxi',
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
                    ])->label('');?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'comments')->textInput(['id'=>'id-comment','required'=>true])->label('Izoh') ?>
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

<?php
// xatolik ko‘rinishlari
$this->registerCss("
.select2-selection.is-invalid { border-color:#dc3545 !important; }
.is-invalid { border-color:#dc3545 !important; }
.mi-row-error{ color:#dc3545; font-size:12px; margin-top:4px; }
");

$this->registerJs('window.__OA_URLS__ = '.Json::htmlEncode([
  'cats'  => $urlCats,
  'sizes' => $urlSizesOnly,
  'types' => $urlTypesBySz,
]).';', View::POS_END);

$this->registerJs(<<<'JS'
// --- 0, 0.00 bo'lsa kirishda bo'shatamiz (har safar sahifa ochilganda) ---
(function clearZerosOnLoad(){
  ['#all-sum-dollar', '#sum-dollar', '#sum-som', '#sum-cart'].forEach(function(id){
    const $i = $(id);
    if(!$i.length) return;
    const v = ($i.val()||'').trim();
    if (v === '0' || v === '0.0' || v === '0.00' || v === '0,0' || v === '0,00') {
      $i.val('');
    }
  });
})();

// --- required bo'lgan pastki inputlar (chegirma va haydovchini hisobga olmaymiz) ---
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
$(document).on('input', '#all-sum-dollar, #sum-dollar, #sum-som, #sum-cart, #id-comment', function(){
  clearInputInvalid($(this));
});

// ---- Select2 chain & count/price validatsiya (oldingi kod) ----
function fillSelect($select, data, selectedVal){
  $select.empty();
  $select.append(new Option('Tanlang...', '', false, false));
  let foundSel=false;
  (data||[]).forEach(function(item){
    let id=(typeof item==='object'&&item!==null)?item.id:item;
    let text=(typeof item==='object'&&item!==null)?item.text:item;
    let sel=selectedVal!=null&&String(selectedVal)===String(id);
    if(sel) foundSel=true;
    $select.append(new Option(text,id,false,sel));
  });
  if(!foundSel) $select.val('');
  $select.trigger('change');
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
var urlCats=window.__OA_URLS__.cats, urlSizes=window.__OA_URLS__.sizes, urlTypes=window.__OA_URLS__.types;
function fetchCategories($row, b, sc){ if(!b){fillSelect($row.find('select.mi-category'),[],null);fillSelect($row.find('select.mi-size'),[],null);fillSelect($row.find('select.mi-type'),[],null);return;}
 $.getJSON(urlCats,{brand_id:b},function(res){ fillSelect($row.find('select.mi-category'),res.categories||[],sc); fillSelect($row.find('select.mi-size'),[],null); fillSelect($row.find('select.mi-type'),[],null);});
}
function fetchSizesOnly($row,b,c,ss){ if(!b||!c){fillSelect($row.find('select.mi-size'),[],null);fillSelect($row.find('select.mi-type'),[],null);return;}
 $.getJSON(urlSizes,{brand_id:b,category_id:c},function(res){ fillSelect($row.find('select.mi-size'),res.sizes||[],ss); fillSelect($row.find('select.mi-type'),[],null);});
}
function fetchTypesBySize($row,b,c,s,st){ if(!b||!c||!s){fillSelect($row.find('select.mi-type'),[],null);return;}
 $.getJSON(urlTypes,{brand_id:b,category_id:c,size:s},function(res){ fillSelect($row.find('select.mi-type'),res.types||[],st); if((res.types||[]).length===1){ $row.find('select.mi-type').val(String(res.types[0].id)).trigger('change'); }});
}
$(document).on('change','select.mi-brand',function(){fetchCategories($(this).closest('tr'),$(this).val(),null);clearSelect2Invalid($(this));});
$(document).on('change','select.mi-category',function(){const $r=$(this).closest('tr');fetchSizesOnly($r,$r.find('select.mi-brand').val(),$(this).val(),null);clearSelect2Invalid($(this));});
$(document).on('change','select.mi-size',function(){const $r=$(this).closest('tr');fetchTypesBySize($r,$r.find('select.mi-brand').val(),$r.find('select.mi-category').val(),$(this).val(),null);clearSelect2Invalid($(this));});
$(document).on('change','select.mi-type',function(){clearSelect2Invalid($(this));});
$(document).on('input','.mi-price',function(){clearInputInvalid($(this));});

// --- Submit: barcha custom tekshiruvlar ---
$('#order-form').on('submit', function(e){
  let hasError=false;
  $('.mi-row-error').remove();
  $('.select2-selection').removeClass('is-invalid');

  // Pastki majburiylar: jami $, summa $, so'mda, kartada, izoh
  ['#all-sum-dollar','#sum-dollar','#sum-som','#sum-cart','#id-comment'].forEach(function(id){
    const $i=$(id);
    if($i.length && ($.trim($i.val())==='')){
      hasError=true; markInputInvalid($i,'Bu maydon to‘ldirilishi shart');
    }
  });

  // MultipleInput qatorlari
  $('#my_id').find('tr.multiple-input-list__item').each(function(){
    const $r=$(this);
    const $sk=$r.find('select.mi-sklad'); const $br=$r.find('select.mi-brand');
    const $ct=$r.find('select.mi-category'); const $sz=$r.find('select.mi-size'); const $tp=$r.find('select.mi-type');
    const $prc=$r.find('input[name*="[price]"]');

    if($sk.length&&!$sk.val()){markSelect2Invalid($sk,'Majburiy maydon');hasError=true;}
    if($br.length&&!$br.val()){markSelect2Invalid($br,'Majburiy maydon');hasError=true;}
    if($ct.length&&!$ct.val()){markSelect2Invalid($ct,'Majburiy maydon');hasError=true;}
    if($sz.length&&!$sz.val()){markSelect2Invalid($sz,'Majburiy maydon');hasError=true;}
    if($tp.length&&!$tp.val()){markSelect2Invalid($tp,'Majburiy maydon');hasError=true;}


    const ps=($prc.val()||'').trim(); const prc=parseFloat(ps.replace(',','.'));
    if(ps===''||isNaN(prc)||prc<0){hasError=true; markInputInvalid($prc,'Qiymat kiriting');}
  });

  if(hasError){
    e.preventDefault();
    const $first=$('.mi-row-error').first();
    if($first.length){$('html,body').animate({scrollTop:$first.offset().top-150},250);}
    return;
  }
});

// --- Edit holatda selectlarni tiklash ---
function initDependentSelects(){
  $('#my_id').find('tr').each(function(){
    const $row=$(this),$b=$row.find('select.mi-brand'),$c=$row.find('select.mi-category'),$s=$row.find('select.mi-size'),$t=$row.find('select.mi-type');
    if(!$b.length) return;
    const bid=$b.val()||null, cv=$c.val()||null, sv=$s.val()||null, tv=$t.val()||null;
    if(bid){
      $.getJSON(urlCats,{brand_id:bid},function(r){
        fillSelect($c,r.categories||[],cv);
        if(cv){
          $.getJSON(urlSizes,{brand_id:bid,category_id:cv},function(r2){
            fillSelect($s,r2.sizes||[],sv);
            if(sv){ $.getJSON(urlTypes,{brand_id:bid,category_id:cv,size:sv},function(r3){ fillSelect($t,r3.types||[],tv); }); }
            else { fillSelect($t,[],null); }
          });
        } else { fillSelect($s,[],null); fillSelect($t,[],null); }
      });
    } else { fillSelect($c,[],null); fillSelect($s,[],null); fillSelect($t,[],null); }
  });
}
$(document).on('afterAddRow','#my_id',function(e,row){
  const $r=$(row); fillSelect($r.find('select.mi-category'),[],null); fillSelect($r.find('select.mi-size'),[],null); fillSelect($r.find('select.mi-type'),[],null);
});
$(document).ready(initDependentSelects);

// // --- Jami summa hisoblash ---
// window.product_details = window.product_details || {};
// $(document).on("change",".input-priority",function(){
//   const name=$(this).attr('name')||''; const m=name.match(/\d+/g); if(!m) return; const id=m.join("");
//   const price=parseFloat($("input[name='OrderAccountHistory[allValue]["+id+"][price]']").val())||0;
//   const count=parseInt($("input[name='OrderAccountHistory[allValue]["+id+"][count]']").val())||0;
//   if(price && count) window.product_details[id]=price*count; else delete window.product_details[id];
//   const total=Object.values(window.product_details).reduce((a,b)=>a+b,0);
//   $("input[name='OrderAccountHistory[sum_all_pro]']").val(total.toFixed(2));
// });
// $(document).on("click",".js-input-remove",function(){
//   const row=$(this).closest('tr'); const inp=row.find('input[name*=\"[price]\"]'); if(!inp.length) return;
//   const m=inp.attr('name').match(/\d+/g); if(!m) return; const id=m.join(""); delete window.product_details[id];
//   const total=Object.values(window.product_details).reduce((a,b)=>a+b,0);
//   $("input[name='OrderAccountHistory[sum_all_pro]']").val(total.toFixed(2));
// });
JS
, View::POS_END);
