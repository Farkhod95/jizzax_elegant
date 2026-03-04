<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\searchs\HelpsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Keshbek';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$sumKeshbek = (float)(clone $dataProvider->query)->sum('keshbek_sum');
?>

<div class="panel panel-inverse user-index">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="/client/index" class="btn btn-xs btn-warning">
                <i class="fa fa-reply"></i> Orqaga qaytish
            </a>

            <?php if(\Yii::$app->user->identity->permission == 1){?>
                <?= Html::a(
                    "Qo'shish <i class='fa fa-plus'></i>",
                    ['/keshbek-history/create', 'client_id' => $client->id],
                    [
                        'role' => 'modal-remote',
                        'class' => 'btn btn-xs btn-success',
                        'data-pjax' => '0',
                        'encode' => false
                    ]
                ) ?>
            <?php }?>

            <a href="javascript:;" title="Во весь экран" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" title="Обновить" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
        </div>

        <h4 class="panel-title">
            <b style="color:#f59c1a;font-size:18px;"><?= $client->fio?></b> ning Keshbeklar ro'yxati
        </h4>
    </div>

    <div class="panel-body">
        <?php Pjax::begin(['id' => 'keshbek-pjax', 'timeout' => 10000, 'enablePushState' => false]); ?>

        <div id="ajaxCrudDatatable">
            <div class="alert alert-success" style="margin-top: 10px;background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d;">
                <b style="font-size:16px;">Umumiy keshbek:</b>
                <span style="font-size:16px;">
                    <b style="font-weight: bold;color: #da1c1c">
                        <?= Yii::$app->formatter->asDecimal($sumKeshbek, 2) ?> $
                    </b>
                </span>
            </div>

            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,
                'columns' => require(__DIR__.'/_columns.php'),
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
                    'after' => '<div class="clearfix"></div>',
                ],
            ]) ?>
        </div>

        <?php Pjax::end(); ?>
    </div>
</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",
]) ?>
<?php Modal::end(); ?>
