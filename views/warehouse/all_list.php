<?php
use yii\helpers\Html;
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

$brands = Brands::find()
    ->orderBy(['sorting' => SORT_ASC])
    ->andWhere(['<>', 'sup_status', 0])
    ->all();

$this->title = "Mahsulotlar ro'yxati";
CrudAsset::register($this);

$catAjaxUrl = Url::to(['product-category/by-brand']);

/**
 * =========================
 * OPTIMIZATION BOSHLANISHI
 * =========================
 */

// $warehouse ichidan brand_id larni ajratib olamiz
$brandIds = [];
foreach ($warehouse as $item) {
    if (!empty($item->brand_id)) {
        $brandIds[] = (int)$item->brand_id;
    } elseif (!empty($item->brand) && !empty($item->brand->id)) {
        $brandIds[] = (int)$item->brand->id;
    }
}
$brandIds = array_values(array_unique($brandIds));

$rowsByBrand = [];
$allMarkCount = 0;

if (!empty($brandIds)) {
    // Barcha warehouse rowlarni bitta query bilan olamiz
    $allRows = Warehouse::find()
        ->alias('w')
        ->with(['brand', 'productCategory'])
        ->leftJoin('product_category pc', 'w.product_category_id = pc.id')
        ->andWhere(['w.brand_id' => $brandIds])
        ->orderBy([
            'w.brand_id' => SORT_ASC,
            'pc.sorting' => SORT_ASC,
            'w.id' => SORT_ASC,
        ])
        ->all();

    // Row id lar
    $warehouseIds = ArrayHelper::getColumn($allRows, 'id');

    // Barcha narxlarni bitta query bilan olamiz
    $prices = [];
    if (!empty($warehouseIds)) {
        $prices = Prices::find()
            ->where(['warehouse_id' => $warehouseIds])
            ->indexBy('warehouse_id')
            ->all();
    }

    // Brand bo'yicha group qilamiz
    foreach ($allRows as $row) {
        $brandId = (int)$row->brand_id;
        if (!isset($rowsByBrand[$brandId])) {
            $rowsByBrand[$brandId] = [];
        }

        $unitPrice = isset($prices[$row->id]) ? (float)$prices[$row->id]->price : 0.0;

        $rowsByBrand[$brandId][] = [
            'model' => $row,
            'unitPrice' => $unitPrice,
            'count' => (int)$row->count,
            'rowTotal' => ((float)$unitPrice * (int)$row->count),
        ];

        $allMarkCount += (int)$row->count;
    }
}

/**
 * Brandlarni original sorting bo'yicha render qilamiz
 */
$brandRenderOrder = [];
foreach ($brands as $brand) {
    if (isset($rowsByBrand[$brand->id])) {
        $brandRenderOrder[] = $brand;
    }
}
?>

<div class="row">
    <div class="col-xl-12">
        <div class="panel panel-inverse warehouse-sticky-panel" data-sortable-id="table-basic-4">

            <div class="panel-heading warehouse-panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">
                        <i class="fa fa-expand"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse">
                        <i class="fa fa-minus"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <h4 class="panel-title">Ombordagi barcha mahsulotlar ro'yxati</h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive warehouse-table-wrap">

                    <!-- FILTER -->
                    <div class="warehouse-sticky-filters" id="bcFilterRow">
                        <div class="warehouse-filter-inline">
                            <div class="warehouse-filter-item warehouse-filter-brand">
                                <label for="brandFilter">Modelni tanlang:</label>
                                <?= Select2::widget([
                                    'name' => 'brand_id',
                                    'id'   => 'brandFilter',
                                    'data' => ArrayHelper::map($brands, 'id', 'name'),
                                    'options' => [
                                        'placeholder' => 'Modelni tanlang',
                                        'data-url'    => $catAjaxUrl,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '100%',
                                    ],
                                ]); ?>
                            </div>

                            <div class="warehouse-filter-item warehouse-filter-category">
                                <label for="categoryFilter">Kategoriya:</label>
                                <?= Select2::widget([
                                    'name' => 'product_category_id',
                                    'id'   => 'categoryFilter',
                                    'data' => [],
                                    'options' => [
                                        'placeholder' => 'Kategoriyani tanlang',
                                        'disabled'    => true,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'width' => '100%',
                                    ],
                                ]); ?>
                            </div>

                            <div class="warehouse-filter-item warehouse-filter-btn">
                                <button type="button" id="bcClearFilters" class="btn btn-default" title="Filtrlarni tozalash">
                                    <i class="fa fa-eraser"></i> Tozalash
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="warehouse-table-space"></div>

                    <?php Pjax::begin([
                        'id' => 'crud-datatable-pjax',
                        'timeout' => 0,
                        'enablePushState' => false,
                    ]); ?>

                    <table class="table table-bordered table-striped warehouse-main-table">
                        <thead>
                        <tr>
                            <th style="background-color:#90e6e6;"><b>#</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Model</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Nomi</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>O'lchami</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Tip</b></th>
                            <th style="background-color:#90e6e6;" nowrap><b>Soni</b></th>
                            <?php if (Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 2): ?>
                                <th style="background-color:#90e6e6;" nowrap><b>Narxi ($)</b></th>
                                <th style="background-color:#90e6e6;" nowrap><b>Jami narxi ($)</b></th>
                            <?php endif; ?>
                        </tr>
                        </thead>

                        <tbody id="showRes">
                        <?php
                        $allPriceSum = 0;
                        $allTotalSum = 0;

                        foreach ($brandRenderOrder as $brand):
                            $brandId = (int)$brand->id;
                            $brandRows = $rowsByBrand[$brandId] ?? [];

                            if (empty($brandRows)) {
                                continue;
                            }

                            $i = 1;
                            $allCount = 0;
                            $priceSum = 0;
                            $totalSum = 0;
                            ?>

                            <tr class="handle-header" data-brand-id="<?= $brandId ?>">
                                <?php if (Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 2): ?>
                                    <td colspan="7" style="background-color:#a0d9ea;">
                                        <b style="color:red"><?= Html::encode($brand->name) ?></b>
                                    </td>
                                    <td style="background-color:#a0d9ea;"></td>
                                <?php else: ?>
                                    <td colspan="5" style="background-color:#a0d9ea;">
                                        <b style="color:red"><?= Html::encode($brand->name) ?></b>
                                    </td>
                                    <td style="background-color:#a0d9ea;"></td>
                                <?php endif; ?>
                            </tr>

                            <?php foreach ($brandRows as $rowData):
                                /** @var Warehouse $model1 */
                                $model1 = $rowData['model'];
                                $unitPrice = (float)$rowData['unitPrice'];
                                $count = (int)$rowData['count'];
                                $rowTotal = (float)$rowData['rowTotal'];
                            ?>
                                <tr class="handle"
                                    data-brand-id="<?= (int)$model1->brand_id ?>"
                                    data-category-id="<?= (int)$model1->product_category_id ?>">
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= $i ?></b>
                                    </td>
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= Html::encode($model1->brand ? $model1->brand->name : '') ?></b>
                                    </td>
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= $model1->product_category_id && $model1->productCategory ? Html::encode($model1->productCategory->name) : '' ?></b>
                                    </td>
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= Html::encode($model1->size) ?></b>
                                    </td>
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= Html::encode($model1->getTypeView($model1->type)) ?></b>
                                    </td>
                                    <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;">
                                        <b><?= $count ?></b>
                                    </td>

                                    <?php if (Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 2): ?>
                                        <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;font-size:14px">
                                            <?= Html::a(
                                                '<b>' . number_format($unitPrice, 2, '.', '') . '</b> <span class="glyphicon glyphicon-usd"></span>',
                                                ['/prices/update', 'warehouse_id' => $model1->id],
                                                [
                                                    'role' => 'modal-remote',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Narxni tahrirlash',
                                                    'class' => 'price-link',
                                                    'data-pjax' => 0,
                                                    'style' => 'color:' . ($unitPrice == 0 ? 'red' : 'green') . '; font-weight:bold;'
                                                ]
                                            ) ?>
                                        </td>
                                        <td style="border-top:1px solid #bebabaff;border-bottom:1px solid #bebabaff;background-color:#efdfdf;font-size:14px;">
                                            <b style="color:#2d6a4f;"><?= number_format($rowTotal, 2, '.', '') ?> $</b>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php
                                $i++;
                                $allCount += $count;
                                $priceSum += $unitPrice;
                                $totalSum += $rowTotal;
                            endforeach;
                            ?>

                            <tr class="brand-amount" data-brand-id="<?= $brandId ?>">
                                <?php if (Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 2): ?>
                                    <td colspan="4"></td>
                                    <td><b>Jami:</b></td>
                                    <td><b><?= (int)$allCount ?></b></td>
                                    <!-- <td><b><?= number_format($priceSum, 2, '.', '') ?> $</b></td> -->
                                     <td><b></b></td>
                                    <td><b><?= number_format($totalSum, 2, '.', '') ?> $</b></td>
                                <?php else: ?>
                                    <td colspan="4"></td>
                                    <td><b>Jami:</b></td>
                                    <td><b><?= (int)$allCount ?></b></td>
                                <?php endif; ?>
                            </tr>

                            <?php
                            $allPriceSum += $priceSum;
                            $allTotalSum += $totalSum;
                        endforeach;
                        ?>

                        <tr>
                            <?php if (Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 2): ?>
                                <td colspan="5" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= (int)$allMarkCount ?></b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"></b></td>
                                <!-- <td style="background-color:#2d353c;"><b style="color:white"><?= number_format($allPriceSum, 2, '.', '') ?> $</b></td> -->
                                <td style="background-color:#2d353c;"><b style="color:white"><?= number_format($allTotalSum, 2, '.', '') ?> $</b></td>
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
Modal::begin(["id" => "ajaxCrudModal", "footer" => ""]);
Modal::end();

$this->registerCss(<<<CSS
:root{
    --app-header-height: 50px;
    --warehouse-panel-heading-height: 40px;
    --warehouse-filter-row-height: 74px;
}

.warehouse-sticky-panel{
    position: relative;
}

.warehouse-table-wrap{
    overflow: visible !important;
    position: relative;
}

.warehouse-panel-heading{
    position: sticky;
    top: var(--app-header-height);
    z-index: 1035;
    background: #2d8c8c !important;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.warehouse-sticky-filters{
    position: sticky;
    top: calc(var(--app-header-height) + var(--warehouse-panel-heading-height));
    z-index: 1030;
    background: #f7f7f7;
    padding: 12px 0;
    border-bottom: 1px solid #dcdcdc;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}

.warehouse-filter-inline{
    display: flex;
    align-items: flex-end;
    gap: 18px;
    flex-wrap: nowrap;
}

.warehouse-filter-item{
    min-width: 0;
}

.warehouse-filter-brand{
    flex: 1 1 45%;
}

.warehouse-filter-category{
    flex: 1 1 45%;
}

.warehouse-filter-btn{
    flex: 0 0 auto;
    padding-bottom: 1px;
}

.warehouse-filter-item label{
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #2d8c8c;
}

.warehouse-filter-btn .btn{
    height: 34px;
    min-width: 100px;
}

.warehouse-table-space{
    height: 10px;
}

.warehouse-main-table{
    margin-bottom: 0;
}

.warehouse-main-table thead th{
    position: sticky;
    top: calc(var(--app-header-height) + var(--warehouse-panel-heading-height) + var(--warehouse-filter-row-height));
    z-index: 1025;
    background-color: #90e6e6 !important;
    vertical-align: middle !important;
    box-shadow: inset 0 -1px 0 #7ccccc;
}

.warehouse-main-table td,
.warehouse-main-table th{
    white-space: nowrap;
}

.select2-container{
    width: 100% !important;
}

.handle-header td{
    position: relative;
    z-index: 1;
}

.brand-amount td{
    background: #fff;
}

@media (max-width: 991px){
    .warehouse-filter-inline{
        flex-wrap: wrap;
        align-items: stretch;
    }

    .warehouse-filter-brand,
    .warehouse-filter-category{
        flex: 1 1 100%;
    }

    .warehouse-filter-btn{
        flex: 0 0 100%;
    }

    .warehouse-filter-btn .btn{
        width: 100%;
    }
}
CSS
);

$this->registerJs(<<<'JS'
var $brand = $('#brandFilter');
var $cat   = $('#categoryFilter');

function getHeaderHeight() {
    var selectors = [
        '.header',
        '#header',
        '.main-header',
        '.app-header',
        '.navbar-fixed-top',
        '.top-navbar',
        '.page-header-fixed',
        '.navbar',
        '.header.navbar-default'
    ];

    for (var i = 0; i < selectors.length; i++) {
        var el = document.querySelector(selectors[i]);
        if (el) {
            var style = window.getComputedStyle(el);
            var isFixed = style.position === 'fixed' || style.position === 'sticky';
            if (isFixed || el.classList.contains('header')) {
                return Math.ceil(el.getBoundingClientRect().height);
            }
        }
    }

    return 50;
}

function updateWarehouseStickyOffsets() {
    var appHeaderHeight = getHeaderHeight();
    var panelHeading = document.querySelector('.warehouse-panel-heading');
    var filterRow = document.querySelector('.warehouse-sticky-filters');

    var panelHeadingHeight = panelHeading ? Math.ceil(panelHeading.getBoundingClientRect().height) : 40;
    var filterRowHeight = filterRow ? Math.ceil(filterRow.getBoundingClientRect().height) : 74;

    document.documentElement.style.setProperty('--app-header-height', appHeaderHeight + 'px');
    document.documentElement.style.setProperty('--warehouse-panel-heading-height', panelHeadingHeight + 'px');
    document.documentElement.style.setProperty('--warehouse-filter-row-height', filterRowHeight + 'px');
}

function bcLoadCategories(brandId){
    var url = $brand.data('url');

    if(!brandId){
        $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
        bcApplyFilter();
        updateWarehouseStickyOffsets();
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
        setTimeout(updateWarehouseStickyOffsets, 30);
    }).fail(function(){
        $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
        bcApplyFilter();
        updateWarehouseStickyOffsets();
    });
}

function bcApplyFilter(){
    var bId   = String($brand.val() || '');
    var cId   = String($cat.val() || '');
    var cText = ($cat.find('option:selected').text() || '').toLowerCase().trim();

    var visibleByBrand = {};

    $('#showRes .handle').each(function(){
        var $tr = $(this);
        var rBid = String($tr.attr('data-brand-id') || '');
        var rCid = String($tr.attr('data-category-id') || '');
        var rowCatText = ($tr.children().eq(2).text() || '').toLowerCase().trim();

        var matchBrand = (!bId || rBid === bId);
        var matchCat   = (!cId || rCid === cId || (cText && rowCatText === cText));

        var show = matchBrand && matchCat;
        $tr.toggle(show);

        if(show){
            visibleByBrand[rBid] = (visibleByBrand[rBid] || 0) + 1;
        }
    });

    $('#showRes .handle-header').each(function(){
        var rBid = String($(this).attr('data-brand-id') || '');
        $(this).toggle(visibleByBrand[rBid] > 0);
    });

    $('#showRes .brand-amount').each(function(){
        var rBid = String($(this).attr('data-brand-id') || '');
        $(this).toggle(visibleByBrand[rBid] > 0);
    });

    updateWarehouseStickyOffsets();
}

$brand.on('change', function(){
    bcLoadCategories($(this).val());
});

$cat.on('change', function(){
    bcApplyFilter();
    setTimeout(updateWarehouseStickyOffsets, 30);
});

$('#bcClearFilters').on('click', function(){
    $brand.val(null).trigger('change.select2');
    $cat.prop('disabled', true).empty().val(null).trigger('change.select2');
    bcApplyFilter();
    setTimeout(updateWarehouseStickyOffsets, 30);
});

$(window).on('load resize', function(){
    updateWarehouseStickyOffsets();
});

$(document).ready(function(){
    bcApplyFilter();
    setTimeout(updateWarehouseStickyOffsets, 50);
});

$(document).on('pjax:end', function(e){
    if(e.target.id === 'crud-datatable-pjax'){
        bcApplyFilter();
        setTimeout(updateWarehouseStickyOffsets, 50);
    }
});
JS
);
?>