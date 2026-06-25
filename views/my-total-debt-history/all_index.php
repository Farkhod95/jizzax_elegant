<?php
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MyTotalDebtHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mening to\'lagan qarzlarim';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$query = clone $dataProvider->query;
$totalDebtSum = $query->sum('all_summ_dollar');
?>
<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/my-total-debt/index" class="btn btn-xs btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <a href="javascript:;" title="To'liq ekran" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Yangilash" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Mening to'lagan qarzlarim: <b style="font-size: 18px;"><?= Yii::$app->formatter->asDecimal($totalDebtSum?:0, 2) ?> $</b></h4>
    </div>
    <div class="panel-body">
        <div id="ajaxCrudDatatable">
            <?= GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => require(__DIR__.'/_columns_all.php'),
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'pager' => [
                    'firstPageLabel' => 'Birinchi',
                    'lastPageLabel'  => 'Oxirgi'
                ],
                'responsiveWrap' => false,
                'panelBeforeTemplate' => false,
                'panel' => [
                    'headingOptions' => ['style' => 'display: none;'],
                    'after'=> '<div class="clearfix"></div>',
                ],
            ]) ?>
        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",
])?>
<?php Modal::end(); ?>
