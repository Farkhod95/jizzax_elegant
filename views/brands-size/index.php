<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BrandsSizeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Mahsulot O'lchami";
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

// AJAX endpoint (brand -> categories)
$catsByBrandUrl = Url::to(['/product-category/categories-by-brand']);
?>
<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <?= Html::a(
                "Qo'shish <i class='fa fa-plus'></i>",
                ['/brands-size/create'],
                [
                    'role' => 'modal-remote',
                    'class' => 'btn btn-xs btn-success',
                    'data-pjax' => '0',
                    'encode' => false
                ]
            ) ?>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Roʻyxat</h4>
    </div>
    <div class="panel-body">
        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'pjax'=>true,
                'columns' => require(__DIR__.'/_columns.php'),
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'responsiveWrap' => false,
                'pager' => [
                    'firstPageLabel' => 'Birinchi',
                    'lastPageLabel'  => 'Oxirgi'
                ],
                'panelBeforeTemplate' => false,
                'panel' => [
                    'headingOptions' => ['style' => 'display: none;'],
                    'after'=> '<div class="clearfix"></div>',
                ],
            ])?>
        </div>
    </div>
</div>

<?php Modal::begin([
    'id' => 'ajaxCrudModal',
    'footer' => '',
    'options' => ['tabindex' => false], // Select2 modal ichida muammosiz ishlashi uchun
])?>
<?php Modal::end(); ?>

<?php
// BRAND -> CATEGORY dinamik filter (Select2 + PJAX)
$js = <<<JS
(function(){
  var urlCats = '{$catsByBrandUrl}';

  function select2DestroyIfAny(\$el){
    if (\$el.hasClass('select2-hidden-accessible')) {
      try { \$el.select2('destroy'); } catch(e){}
    }
  }

  function fillSelect(\$select, items, selectedId){
    // mavjud Select2 ni to'xtatib va opsiyalarni yangilaymiz
    select2DestroyIfAny(\$select);
    \$select.empty();

    // placeholder / prompt
    var ph = (\$select.attr('data-placeholder') || 'Tanlang...');
    \$select.append(new Option('', '', false, false));

    if (Array.isArray(items)) {
      items.forEach(function(it){
        \$select.append(new Option(it.text, it.id, false, false));
      });
    }

    if (selectedId) {
      \$select.val(String(selectedId));
    } else {
      \$select.val(null);
    }

    // qayta Select2 ni ishga tushiramiz
    try { \$select.select2({allowClear:true, width:'resolve', placeholder: ph}); } catch(e){}
    \$select.trigger('change.select2'); // UI update
  }

  function loadCatsByBrand(brandId, selectedCatId){
    var \$cat = \$('#filter-category');
    if (!\$cat.length) return;

    if (!brandId) {
      fillSelect(\$cat, [], null);
      return;
    }

    \$.get(urlCats, {brand_id: brandId})
      .done(function(res){
        var items = (res && res.results) ? res.results : [];
        fillSelect(\$cat, items, selectedCatId || null);
      })
      .fail(function(){
        fillSelect(\$cat, [], null);
      });
  }

  function initDependentFilters(){
    var \$brand = \$('#filter-brand');
    var \$cat   = \$('#filter-category');
    if (!\$brand.length || !\$cat.length) return;

    // Sahifa/PJAX yuklanganda hozirgi qiymatlar
    var curBrand = \$brand.val();
    var curCat   = \$cat.val();

    // Hozirgi brand bo'lsa, shu brand uchun kategoriyalarni yuklab, tanlanganini tiklaymiz
    loadCatsByBrand(curBrand, curCat);

    // Brand o'zgarsa, category ro'yxatini yangilaymiz
    \$brand.off('change.depCat').on('change.depCat', function(){
      var b = \$(this).val();
      loadCatsByBrand(b, null);
    });
  }

  // Dastlabki ishga tushirish
  initDependentFilters();

  // Kartik Grid Pjax konteyneri: #kv-pjax-container-<gridId>
  // Sizda gridId = crud-datatable => #kv-pjax-container-crud-datatable
  \$(document).on('pjax:end pjax:success', function (e) {
    var id = e.target && e.target.id ? e.target.id : '';
    if (id === 'kv-pjax-container-crud-datatable' || id === 'crud-datatable-pjax' || id.indexOf('pjax') !== -1) {
      initDependentFilters();
    }
  });
})();
JS;

$this->registerJs($js, \yii\web\View::POS_END);
