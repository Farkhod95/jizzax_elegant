<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\OrderAccountHistory;
use app\models\ProductAccount;
use app\models\OrderAccount;
use yii\helpers\Url;
use app\models\Client;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Qarzdorlar ro\'yxati';
$allSumm = 0;
$clients = Client::find()->orderBy(['id' => SORT_ASC])->all();
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <!-- <a href="/order-account/index" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a> -->
                    <?php /* if(Yii::$app->user->identity->permission == 1 || Yii::$app->user->identity->permission == 2){?>
                        <a class="btn btn-xs  btn-danger" href="<?= Url::toRoute(['/orders/check', 'id' => $order_id])?>"><i class="fa fa-trash-o"> </i> Buyurtmani bekor qilish</a>
                    <?php }*/?>
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a> -->
                    <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a> -->
                </div>
                <h4 class="panel-title">Qarzdorlar ro'yxati</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
                <div class="table-responsive">
                    <label style="font-size: 14px;" for="debt">Mijozni tanlang:</label>
    
                    <?= Select2::widget([
                        'name' => 'debt',
                        'id' => 'debt',
                        
                        'data' => \yii\helpers\ArrayHelper::map($clients, 'fio', 'fio'),
                        'options' => ['placeholder' => 'Mijozni tanlang',],
                        'pluginOptions' => [
                            'width' => '400px',
                            'allowClear' => true, // This option allows the user to clear the selection
                        ],
                    ]); ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th nowrap>FIO</th>
                                <th nowrap>Qarzi ($)</th>
                                <th nowrap>Sana</th>
                            </tr>
                        </thead>
                        <tbody id="showRes1" >
                            <?php  $i = 1; foreach ($orderAccount as $model) {
                                $orderAccountHistory = OrderAccountHistory::find()->where(['client_id' => $model->client_id])->orderBy(['id' => SORT_DESC])->one();
                                ?>
                                <tr class="handle">
                                    <td style="width:50px"> <?= $i ?></td>
                                    <td ><b ><a href="<?=Url::toRoute(['order-account/products', 'id'=> $model->id])?>"><?= $model->client->fio ?></a></b></td>
                                    <td ><b style="color:red"><?= Yii::$app->formatter->asDecimal($model->total_debt, 2) ?></b></td>
                                    <td ><b style="color:#f59c1a"><?= $orderAccountHistory? Yii::$app->formatter->asDate($orderAccountHistory->date, 'php:d.m.Y') : '' ?></b></td>
                                </tr>
                            <?php  $i=$i+1;$allSumm = $allSumm + $model->total_debt; }?>
                            <tr>
                                <td colspan="2" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= Yii::$app->formatter->asDecimal($allSumm, 2) ?></b></td>
                                <td  style="background-color:#2d353c;"></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    
                </div>
                <!-- end table-responsive -->
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$('#debt').on('change', function(e){
    e.preventDefault();
    const value = $(this).val().toUpperCase();;
    var selectElement = document.getElementById("debt");
    var selectedValue = selectElement.value.toUpperCase();
    if (selectedValue) {
        $("#showRes1 .handle").filter(function() {
            const td = $(this).children('td').eq(1).text().toUpperCase();
            $(this).toggle(td === selectedValue)
        });
    }else{
        $("#showRes1 tr").filter(function() {
        $(this).show()
        });
    }
});

JS
) ?>