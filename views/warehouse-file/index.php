<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\searchs\HelpsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mahsulot rasmlari';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/warehouse/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
            <?php if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 6){?>
                <!-- <span class="btn btn-info btn-xs m-r-5"> $exchangeRate->dollar $</span> -->
                <?= Html::a('<span class="btn btn-success btn-xs m-r-5"><i class="fa fa-plus"></i> Qo\'shish</span>', ['/warehouse-file/create', 'warehouse_id' => $warehouse_id], ['role'=>'modal-remote', 'data-toggle'=>'tooltip',]); ?>
            <?php }?>
            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>
        <h4 class="panel-title">Mahsulot Rasmlari</h4>
    </div>
    <div style="color: red; font-weight: bold; font-size: 16px; margin-top: 15px;margin-left:30px;">
        <?= Html::encode("Model: " . ($warehouse->brand->name ?? '–')) ?> |
        <?= Html::encode("Nomi: " . ($warehouse->productCategory->name ?? '–')) ?> |
        <?= Html::encode("O'lcham: " . ($warehouse->size ?? '–')) ?> |
        <?= Html::encode("Tip: " . ($warehouse->getProductTypeView($warehouse->type) ?? '–')) ?>
    </div>
    <div class="panel-body">
        <div id="ajaxCrudDatatable">
            <?=GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => require(__DIR__.'/_columns.php'),
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'pager' => [
                    'firstPageLabel' => 'Birinchi',
                    'lastPageLabel'  => 'Oxirgi'
                ],
                'responsiveWrap' => false,
                'panelBeforeTemplate' => false /*Html::a('Добавить <i class="fa fa-plus"></i>', ['create'],
                    ['data-pjax'=>'0','title'=> 'Добавить','class'=>'btn btn-success']) . ' ' .
                    Html::a('Назад', ['/references/helps-categories'],
                    ['data-pjax'=>'0','title'=> 'Назад','class'=>'btn btn-inverse']) */,
                'panel' => [
                    'headingOptions' => ['style' => 'display: none;'],
                    'after'=>
                        '<div class="clearfix"></div>',
                ],
            ])?>
        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
