<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
use app\models\Prices;
use kartik\select2\Select2;
use app\models\Brands;
use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Warehouse[] $warehouse */

$brands = Brands::find()->orderBy(['sorting' => SORT_ASC])->andWhere(['<>', 'sup_status', 0])->all();
$this->title = "Mahsulotlar ro'yxati";

$allMarkCount = 0;
CrudAsset::register($this);

// Dependent Select2 uchun endpoint
$catAjaxUrl = Url::to(['product-category/by-brand']);
?>
<div class="row">
  <div class="col-xl-12">
    <div class="panel panel-inverse" data-sortable-id="table-basic-4">
      <div class="panel-heading">
        <div class="panel-heading-btn">
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
          <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Ombordagi barcha mahsulotlar ro'yxati</h4>
      </div>

      <div class="panel-body">
        <div class="table-responsive">

          <!-- ====== FILTER QISMI ====== -->
          <div class="row form-inline" id="bcFilterRow">
            <div class="col-md-5">
              <label style="font-size:14px;margin-right:10px" for="brandFilter">Modelni tanlang:</label>
              <?= Select2::widget([
                'name' => 'brand_id',
                'id'   => 'brandFilter',
                'data' => ArrayHelper::map($brands, 'id', 'name'),
                'options' => [
                  'placeholder' => 'Modelni tanlang',
                  'data-url'    => $catAjaxUrl,
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

            <div class="col-md-3" style="padding-top:22px;">
              <button type="button" id="bcClearFilters" class="btn btn-default" title="Filtrlarni tozalash">
                <i class="fa fa-eraser"></i> Tozalash
              </button>
            </div>
          </div>
          <!-- ====== /FILTER QISMI ====== -->

          <br/>

          <?php Pjax::begin([
            'id' => 'crud-datatable-pjax',
            'timeout' => 0,
            'enablePushState' => false,
          ]); ?>
          <table class="table">
            <thead>
              <tr>
                <th style="background-color:#90e6e6;"><b>#</b></th>
                <th style="background-color:#90e6e6;" nowrap><b>Model</b></th>
                <th style="background-color:#90e6e6;" nowrap><b>Nomi</b></th>
                <th style="background-color:#90e6e6;" nowrap><b>O'lchami</b></th>
                <th style="background-color:#90e6e6;" nowrap><b>Tip</b></th>
                <th style="background-color:#90e6e6;" nowrap><b>Soni</b></th>
                <?php if (Yii::$app->user->identity->permission == 1): ?>
                  <th style="background-color:#90e6e6;" nowrap><b>Narxi ($)</b></th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody id="showRes">
              <?php
              $allPriceSum = 0;
              foreach ($warehouse as $model):
                $i = 1; $allCount = 0; $priceSum = 0;
              ?>
                <!-- Brand header qatori -->
                <tr class="handle-header" data-brand-id="<?= (int)$model->brand->id ?>">
                  <?php if (Yii::$app->user->identity->permission == 1): ?>
                    <td colspan="6" style="background-color:#a0d9ea;"><b style="color:red"><?= Html::encode($model->brand->name) ?></b></td>
                    <td style="background-color:#a0d9ea;"></td>
                  <?php else: ?>
                    <td colspan="5" style="background-color:#a0d9ea;"><b style="color:red"><?= Html::encode($model->brand->name) ?></b></td>
                    <td style="background-color:#a0d9ea;"></td>
                  <?php endif; ?>
                </tr>

                <?php
                $rows = Warehouse::find()
                  ->alias('w')
                  ->select(['w.*','pc.sorting'])
                  ->andWhere(['w.brand_id' => $model->brand->id])
                  ->leftJoin('product_category pc','w.product_category_id = pc.id')
                  ->orderBy(['pc.sorting'=>SORT_ASC])
                  ->all();

                foreach ($rows as $model1):
                  $price = Prices::find()->where(['warehouse_id' => $model1->id])->one();
                  $sumWarehouse = $price ? (float)$price->price : 0.0;
                ?>
                  <tr class="handle"
                      data-brand-id="<?= (int)$model1->brand_id ?>"
                      data-category-id="<?= (int)$model1->product_category_id ?>">
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= $i ?></b></td>
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= Html::encode($model1->brand->name) ?></b></td>
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= $model1->product_category_id ? Html::encode($model1->productCategory->name) : '' ?></b></td>
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= Html::encode($model1->size) ?></b></td>
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= Html::encode($model1->getTypeView($model1->type)) ?></b></td>
                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;"><b><?= (int)$model1->count ?></b></td>
                    <?php if (Yii::$app->user->identity->permission == 1): ?>
                      <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;font-size:14px">
                        <?= Html::a(
                          '<b>' . number_format($sumWarehouse, 2, '.', '') . ' </b> <span class="glyphicon glyphicon-usd"></span>',
                          ['/prices/update', 'warehouse_id' => $model1->id],
                          [
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip',
                            'title' => 'Narxni tahrirlash',
                            'class' => 'price-link',
                            'data-pjax' => 0,
                            'style' => 'color:' . ($sumWarehouse == 0 ? 'red' : 'green') . '; font-weight:bold;'
                          ]
                        ) ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php
                  $i++;
                  $allCount += (int)$model1->count;
                  $allMarkCount += (int)$model1->count;
                  $priceSum += $sumWarehouse;
                endforeach;
                ?>

                <!-- Brand bo‘yicha Jami qatori -->
                <tr class="brand-amount" data-brand-id="<?= (int)$model->brand->id ?>">
                  <?php if (Yii::$app->user->identity->permission == 1): ?>
                    <td colspan="4"></td>
                    <td><b>Jami:</b></td>
                    <td><b><?= (int)$allCount ?></b></td>
                    <td><b><?= number_format($priceSum, 2, '.', '') ?> $</b></td>
                  <?php else: ?>
                    <td colspan="4"></td>
                    <td><b>Jami:</b></td>
                    <td><b><?= (int)$allCount ?></b></td>
                  <?php endif; ?>
                </tr>
              <?php
                $allPriceSum += $priceSum;
              endforeach;
              ?>

              <!-- Umumiy Jami -->
              <tr>
                <?php if (Yii::$app->user->identity->permission == 1): ?>
                  <td colspan="5" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                  <td style="background-color:#2d353c;"><b style="color:white"><?= (int)$allMarkCount ?></b></td>
                  <td style="background-color:#2d353c;"><b style="color:white"><?= number_format($allPriceSum, 2, '.', '') ?> $</b></td>
                <?php else: ?>
                  <td colspan="5" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                  <td style="background-color:#2d353c;"><b style="color:white"><?= (int)$allMarkCount ?></b></td>
                <?php endif; ?>
              </tr>
            </tbody>
          </table>
          <?php Pjax::end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
Modal::begin(["id"=>"ajaxCrudModal","footer"=>""]);
Modal::end();

/* ====== JS: Brand→Category yuklash va jadvalni filtrlash ====== */
$this->registerJs(<<<'JS'
var $brand = $('#brandFilter');
var $cat   = $('#categoryFilter');

// Kategoriyalarni AJAX bilan yuklash
function bcLoadCategories(brandId){
  var url = $brand.data('url');
  if(!brandId){
    $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
    bcApplyFilter();
    return;
  }
  $.get(url, {brand_id: brandId}).done(function(res){
    var items = (res && res.results) ? res.results : [];
    $cat.prop('disabled', items.length === 0).empty();
    items.forEach(function(it){
      $cat.append(new Option(it.text, it.id, false, false));
    });
    $cat.val(null).trigger('change.select2');
    bcApplyFilter();
  }).fail(function(){
    $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
    bcApplyFilter();
  });
}

// Jadvalni filtrlash (id bo'yicha; kerak bo'lsa nom bo'yicha ham)
function bcApplyFilter(){
  var bId   = String($brand.val() || '');
  var cId   = String($cat.val()   || '');                     // category_id (Select2 value)
  var cText = ($cat.find('option:selected').text() || '').toLowerCase().trim(); // ko'rinadigan nom (masalan "1203")

  var visibleByBrand = {};

  $('#showRes .handle').each(function(){
    var $tr        = $(this);
    var rBid       = String($tr.attr('data-brand-id')    || ''); // .attr — caching yo'q
    var rCid       = String($tr.attr('data-category-id') || '');
    var rowCatText = ($tr.children().eq(2).text() || '').toLowerCase().trim(); // jadvaldagi kategoriya nomi

    var matchBrand = (!bId || rBid === bId);
    var matchCat   = (!cId || rCid === cId || (cText && rowCatText === cText)); // id yoki nom bo'yicha

    var show = matchBrand && matchCat;
    $tr.toggle(show);
    if(show){ visibleByBrand[rBid] = (visibleByBrand[rBid] || 0) + 1; }
  });

  // Brand headerlari
  $('#showRes .handle-header').each(function(){
    var rBid = String($(this).attr('data-brand-id') || '');
    $(this).toggle( visibleByBrand[rBid] > 0 );
  });

  // Brand "Jami" qatorlari
  $('#showRes .brand-amount').each(function(){
    var rBid = String($(this).attr('data-brand-id') || '');
    $(this).toggle( visibleByBrand[rBid] > 0 );
  });
}

// Eventlar
$brand.on('change', function(){ bcLoadCategories($(this).val()); });
$cat.on('change', bcApplyFilter);

// Tozalash tugmasi
$('#bcClearFilters').on('click', function(){
  $brand.val(null).trigger('change.select2');
  $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
  bcApplyFilter();
});

// Boshlang'ich holat
bcApplyFilter();

// PJAXdan keyin ham ishlasin
$(document).on('pjax:end', function(e){
  if(e.target.id === 'crud-datatable-pjax'){
    bcApplyFilter();
  }
});
JS
);
