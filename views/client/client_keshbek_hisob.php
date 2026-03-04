<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\WarehouseHistory;
use kartik\select2\Select2;
use app\models\Client;

$warehouseHistory = WarehouseHistory::find()->all();
// $clients = Client::find()->where(['id' => $client_id])->one();
$this->title = 'Mahsulotlar ro\'yxati';

?>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/client/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a> -->
                </div>
                <h2 class="panel-title" style="font-size:16px"><?= $clients->fio ?> ning keshbeklar hisobi</h2>
            </div>
            <div class="panel-body">
                <div class="x_panel">
                    <div class="x_content">
                        <?php $form = ActiveForm::begin([
                                    'action' => Url::to(['client/client-keshbek-hisob', 'id' => $client_id]),
                                    'method' => 'post',
                                ]); ?>
                            <div class="form-group">
                                <div class="row"> 
                                    <div class="col-sm-5">
                                        <?= Select2::widget([
                                            'name' => 'customer_fio',
                                            'id' => 'myselect',
                                            'data' => [$clients->fio => $clients->fio],
                                            'value' => $clients->fio,
                                            'options' => ['placeholder' => 'Mijoz tanlang','style' => 'text-align:right;','disabled' => true,],
                                            'pluginOptions' => [
                                                'allowClear' => true, // This option allows the user to clear the selection
                                            ],
                                        ]); ?>
                                    </div>
                                    <div class="col-md-2 ">
                                        <input type="date" name="start_date" value="<?= $startDate ?? date('Y-m-d') ?>" style="font-size: 14px; padding:3px;">

                                        <label style= "font-size: 14px;padding:4px;">dan</label>
                                    </div>
                                    <div class="col-md-2 ">
                                        <input type="date" name="end_date" value="<?= $endDate ?? date('Y-m-d') ?>" style="font-size: 14px; padding:3px;">

                                        <label style= "font-size: 14px;padding:4px;">gacha</label>
                                    </div>
                                    <div class="col-md-1 ">
                                        <?php echo Html::submitButton(Yii::t('app', 'Qidirish'), ['class' => 'btn btn-primary btn-round']) ?>
                                    </div>
                                    <?php if ($totalProductSum !== null && $totalSummDollar !== null): ?>
                                        <div class="col-md-12 mt-3">
                                            <h4>Hisoblash natijasi</h4>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>To'lanadigan summa ($)</th>
                                                        <th>To'langan summa ($)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?= number_format($totalProductSum, 2, '.', ' ') ?></td>
                                                        <td><?= number_format($totalSummDollar, 2, '.', ' ') ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- Bu yerda kod bo'lishi kerak -->
                                            <div class="form-group mt-3">
                                                <div style="display: flex; align-items: center;">
                                                    <label for="percentInput" style="font-weight: 600; margin: 0 10px 0 0;">Keshbek kiriting (%):</label>
                                                    <input type="number" step="0.01" id="percentInput" placeholder="Masalan: 0.5"
                                                        style="width: 100px; margin-right: 15px;" class="form-control">
                                                    <span style="margin-right: 6px; font-weight: 600;">Hisoblangan keshbek ($):</span>
                                                    <div id="calculationResult" style="font-size: 16px; font-weight: 600; color: red; min-width: 80px;"></div>
                                                </div>
                                            </div>




                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php ActiveForm::end();?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php
/** @var yii\web\View $this */

// PHP qiymatni JSga uzatish
$total = $totalProductSum ?? 0;
$totalJS = json_encode((float)$total);

// Inline JS qo‘shish
$this->registerJs(<<<JS
    const percentInput = document.getElementById('percentInput');
    const resultBox = document.getElementById('calculationResult');
    const totalSum = $totalJS;

    percentInput?.addEventListener('input', function () {
        const value = parseFloat(this.value);
        if (!isNaN(value)) {
            const result = (totalSum * value) / 100;
            resultBox.innerText = result.toFixed(2);
        } else {
            resultBox.innerText = '';
        }
    });
JS
);
?>





