<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use app\models\ExchangeRate;

use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use app\models\Client;
use app\models\DebtRepayment;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderAccountHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mijozlar buyurmalari tarixi';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$exchangeRate = ExchangeRate::findOne(1);

$clientTypeList = [
    1 => 'Oddiy mijoz',
    2 => 'Bozordagi mijoz',
    3 => 'Filial',
];

$showFilter = (int) Yii::$app->request->get('show_filter', 0);
Yii::$app->session->set('oah_showFilter', (int)$showFilter);

$clientInitText = '';
if (!empty($searchModel->client_id)) {
    $clientInitText = Client::find()
        ->select('fio')
        ->where(['id' => (int)$searchModel->client_id])
        ->scalar() ?: '';
}

$this->registerCss("
.top-filter .select2-container--krajee .select2-selection--single{ height: 30px !important; }
.top-filter .select2-container--krajee .select2-selection--single .select2-selection__rendered{ line-height: 28px !important; }
.top-filter .select2-container--krajee .select2-selection--single .select2-selection__arrow{ height: 28px !important; }
.top-filter .kv-date-picker .form-control{ height: 30px !important; padding: 4px 10px !important; }
.top-filter .form-group{ margin-bottom: 6px !important; }
.top-filter .panel{ border-radius: 10px; }

.filter-toggle{
  display:flex; align-items:center; gap:14px;
  padding: 6px 10px; border: 1px solid #eee; border-radius: 10px; margin-bottom: 10px;
}
.filter-toggle label{ margin:0; font-weight:600; }
.filter-toggle .radio-inline{ margin-right: 12px; }

table.kv-grid-table tbody tr.day-seq-1-row > td{
  background-color:#d5d2d2 !important;
  font-weight:700;
}
");

// ✅ totals (1 query)
$totals = (clone $dataProvider->query)
    ->select([
        'sum_all_product' => 'SUM(all_product_sum)',
        'sum_all_paid'    => 'SUM(all_summ_dollar)',
        'sum_all_profit'  => 'SUM(all_profit_dollar)',
        'sum_all_dollar'  => 'SUM(sum_dollar)',
        'sum_all_som'     => 'SUM(sum_som)',
        'sum_all_cart'    => 'SUM(sum_cart)',
    ])
    ->asArray()
    ->one();

$sumAllProduct  = (float)($totals['sum_all_product'] ?? 0);
$sumAllPaid     = (float)($totals['sum_all_paid'] ?? 0);
$sumAllProfit   = (float)($totals['sum_all_profit'] ?? 0);

$isSearched = !empty(Yii::$app->request->get($searchModel->formName()));

// ✅ DebtRepayment sum (client bo'lsa client bo'yicha, bo'lmasa type bo'yicha)
$debtQuery = DebtRepayment::find()->alias('dr')
    ->andWhere(['or', ['dr.is_delete' => null], ['<>', 'dr.is_delete', 1]])
    ->andWhere(['or', ['!=', 'dr.is_worker', 1], ['is', 'dr.is_worker', null]])
    ->leftJoin(['c' => Client::tableName()], 'c.id = dr.client_id'); // ✅ type uchun

$req = Yii::$app->request->get($searchModel->formName(), []);

$clientId   = (int)($searchModel->client_id   ?: ($req['client_id'] ?? 0));
$clientType = (int)($searchModel->client_type ?: ($req['client_type'] ?? 0));

// 1) client tanlangan bo'lsa — client bo'yicha
if ($clientId > 0) {
    $debtQuery->andWhere(['dr.client_id' => $clientId]);
}
// 2) client tanlanmagan, type tanlangan bo'lsa — shu type dagi clientlar bo'yicha
elseif ($clientType > 0) {
    $debtQuery->andWhere(['c.type' => $clientType]);
}

// Date range filter
if (!empty($searchModel->date_from)) {
    $from = date('Y-m-d', strtotime($searchModel->date_from));
    $debtQuery->andWhere(['>=', 'dr.date', $from]);
}
if (!empty($searchModel->date_to)) {
    $to = date('Y-m-d', strtotime($searchModel->date_to));
    $debtQuery->andWhere(['<=', 'dr.date', $to]);
}

$sumDebtRepaymentAllSummDollar = (float)($debtQuery->sum('dr.all_summ_dollar') ?: 0);
?>

<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <?php if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 6){?>
                <?= Html::a('<span class="btn btn-info btn-xs m-r-5"><i class="fa fa-usd"></i> Dollar kursni o\'zgartirish</span>', ['/exchange-rate/update', 'id' => 1], ['role'=>'modal-remote', 'data-toggle'=>'tooltip']); ?>
                <?= Html::a('<span class="btn btn-warning btn-xs m-r-5"><i class="fa fa-exclamation-triangle" style="color: white;"></i> Narxdagi farq</span>', ['order-account-history/index', 'large_price' => 1], ['data-pjax' => 0, 'data-toggle'=>'tooltip']); ?>
                <?= Html::a('<span class="btn btn-success btn-xs m-r-5"><i class="fa fa-list" style="color: white;"></i> Hammasi</span>', ['order-account-history/index'], ['data-pjax' => 0, 'data-toggle'=>'tooltip']); ?>
            <?php }?>

            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Roʻyxat</h4>
    </div>

    <div class="panel-body">

        <?php if(\Yii::$app->user->identity->permission == 1 ){?>

            <div class="filter-toggle">
                <label>Filter ko‘rinsinmi?</label>

                <?php $params = Yii::$app->request->queryParams; ?>

                <?= Html::beginForm(['index'], 'get', ['style' => 'margin:0; display:flex; align-items:center; gap:10px;']); ?>

                    <?php
                    foreach ($params as $k => $v) {
                        if ($k === 'show_filter') continue;

                        if (is_array($v)) {
                            foreach ($v as $kk => $vv) {
                                echo Html::hiddenInput($k . '[' . $kk . ']', $vv);
                            }
                        } else {
                            echo Html::hiddenInput($k, $v);
                        }
                    }
                    ?>

                    <label class="radio-inline">
                        <input type="radio" name="show_filter" value="1" <?= $showFilter === 1 ? 'checked' : '' ?> onchange="this.form.submit()">
                        Ha
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="show_filter" value="0" <?= $showFilter === 0 ? 'checked' : '' ?> onchange="this.form.submit()">
                        Yo‘q
                    </label>

                <?= Html::endForm(); ?>
            </div>

            <?php if ($showFilter === 1): ?>
                <div class="top-filter">
                    <div class="panel panel-default" style="margin-bottom:10px;">
                        <div class="panel-body" style="padding:12px 12px 6px 12px;">
                            <?php $form = ActiveForm::begin([
                                'method' => 'get',
                                'action' => ['index'],
                                'options' => ['autocomplete' => 'off'],
                            ]); ?>

                            <?= Html::hiddenInput('show_filter', 1) ?>

                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <?= $form->field($searchModel, 'client_type')->widget(Select2::class, [
                                        'data' => $clientTypeList,
                                        'options' => [
                                            'placeholder' => 'Mijoz turi...',
                                            'id' => 'client_type_select',
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                        ],
                                        'size' => Select2::SMALL,
                                    ])->label(false); ?>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <?= $form->field($searchModel, 'client_id')->widget(Select2::class, [
                                        'initValueText' => $clientInitText, // ✅ reload bo'lganda fio ko'rinadi
                                        'options' => [
                                            'placeholder' => 'Mijozni tanlang...',
                                            'id' => 'client_id_select',
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 0,
                                            'ajax' => [
                                                'url' => Url::to(['client-list']),
                                                'dataType' => 'json',
                                                'delay' => 150,
                                                'data' => new \yii\web\JsExpression('function(params){
                                                    return {
                                                        q: params.term || "",
                                                        type: $("#client_type_select").val() || "",
                                                        page: params.page || 1
                                                    };
                                                }'),
                                                'processResults' => new \yii\web\JsExpression('function(data, params){
                                                    params.page = params.page || 1;
                                                    return {
                                                        results: data.results,
                                                        pagination: {
                                                            more: data.pagination ? data.pagination.more : false
                                                        }
                                                    };
                                                }'),
                                                'cache' => true,
                                            ],
                                        ],
                                        'size' => Select2::SMALL,
                                    ])->label(false); ?>
                                </div>


                                <div class="col-md-4 col-sm-6">
                                    <div style="display:flex; gap:8px; width:100%;">
                                        <div style="flex:1;">
                                            <?= $form->field($searchModel, 'date_from')->widget(DatePicker::class, [
                                                'type' => DatePicker::TYPE_INPUT,
                                                'options' => ['placeholder' => 'Sanadan'],
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'todayHighlight' => true, 
                                                ],
                                            ])->label(false); ?>
                                        </div>
                                        <div style="flex:1;">
                                            <?= $form->field($searchModel, 'date_to')->widget(DatePicker::class, [
                                                'type' => DatePicker::TYPE_INPUT,
                                                'options' => ['placeholder' => 'Sanagacha'],
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'todayHighlight' => true, 
                                                ],
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div style="display:flex; gap:8px; justify-content:flex-end; align-items:flex-start;">
                                        <?= Html::submitButton('<i class="fa fa-search"></i> Qidirish', [
                                            'class' => 'btn btn-primary btn-sm',
                                            'style' => 'min-width:90px;'
                                        ]) ?>

                                        <?= Html::a('<i class="fa fa-times"></i> Tozalash', ['index', 'show_filter' => 1], [
                                            'class' => 'btn btn-default btn-sm',
                                            'data-pjax' => 0,
                                            'style' => 'min-width:95px;'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isSearched): ?>
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-md-12">
                        <div class="alert alert-info" style="margin-bottom:0; border-radius:10px;background-color:#f5f5f5; border:1px solid #ddd;">
                            <div style="display:flex; gap:18px; flex-wrap:wrap; align-items:center;">
                                <div><b style="font-size:14px; color:red;">Jami to‘lanadigan summa ($): <?= number_format($sumAllProduct, 2, '.', ' ') ?></b></div> |
                                <div><b style="font-size:14px; color:orange;">Jami to‘langan summa ($): <?= number_format($sumAllPaid, 2, '.', ' ') ?></b></div> |
                                <div><b style="font-size:14px; color:green;">Jami foyda ($): <?= number_format($sumAllProfit, 2, '.', ' ') ?></b></div> |
                                <div><b style="font-size:14px; color:#6f42c1;">Qarz qaytarish ($): <?= number_format($sumDebtRepaymentAllSummDollar, 2, '.', ' ') ?></b></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php } ?>

        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterUrl' => Url::to(['order-account-history/index']),
                'pjax' => true,
                // 'filterRowOptions' => [
                //     'style' => ($showFilter === 1) ? 'display:none;' : '',
                // ],
                'columns' => require(__DIR__.'/_columns.php'),
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'pager' => [
                    'firstPageLabel' => 'Birinchi',
                    'lastPageLabel'  => 'Oxirgi'
                ],
                'rowOptions' => function ($model) {
                    if ((int)$model->day_seq === 1 && $model->fast_order != 1) {
                        return [
                            'class' => 'day-seq-1-row',
                            'title' => 'Kun bo‘yicha 1-chi buyurtma',
                        ];
                    }
                    return [];
                },
                'responsiveWrap' => false,
                'panelBeforeTemplate' => false,
                'panel' => [
                    'headingOptions' => ['style' => 'display: none;'],
                ],
            ])?>
        </div>

    </div>
</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",
])?>
<?php Modal::end(); ?>

<?php
// ✅ Refreshsiz: type o'zgarsa client select tozalanadi va dropdown qayta yuklanadi
$this->registerJs(<<<JS
(function(){
  var \$type = $("#client_type_select");
  var \$client = $("#client_id_select");

  function clearClient(){
    // client select default bo'sh tursin
    \$client.val(null).trigger("change");
  }

  // type o'zgarsa -> clientni tozalab qo'yamiz, keyingi ochilganda yangi type bilan keladi
  \$type.on("change", function(){
    clearClient();
  });

})();
JS
);
?>
