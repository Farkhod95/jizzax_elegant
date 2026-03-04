<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
use yii\helpers\Url;
use app\models\Client;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$clients = Client::find()->orderBy(['id' => SORT_ASC])->all();
$this->title = Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') . 'sanadagi eksport tovarlar tarixi';
$i = 1;
$sumCount = 0;
$allMarkCount = 0
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/order-account-history/export" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') ?> sanadagi buyurtmachilar</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= Select2::widget([
                            'name' => 'customer_name',
                            'id' => 'searchSelect',
                            'data' => \yii\helpers\ArrayHelper::map($clients, 'fio', 'fio'),
                            'options' => [
                                'placeholder' => 'Mijoz tanlang',
                                'allowClear' => true, // O'chirish imkoniyatini beradi
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>
                </div>
                <br/>
                <div class="row row-space-10" id="clientList">
                    <?php 
                        $index = 1; // Hisoblagichni boshlash
                        foreach ($orders as $model) { 
                            if (($index - 1) % 3 == 0) { // Har uchta elementdan keyin yangi satr ochish
                                echo '<div class="row">';
                            }
                        ?>
                            <div class="col-md-4 client-item" data-name="<?= strtolower($model->client->fio) ?>"> <!-- 4 dan foydalanyapmiz, chunki 12/3 = 4, ya'ni 3 ustun bo'ladi -->
                                <div class="alert alert-info show m-b-10">
                                    <b style="color:red"><?= $index ?> </b>. <i class="fa fa-user fa-2x" style="font-size: 16px;"></i>
                                    <a href="<?=Url::toRoute(['order-account-history/export-view', 'order_id'=> $model->id, 'cr_date' => $cr_date, 'customer_fio' => $model->client->fio])?>" class="alert-link" style="font-size: 14px; margin-left: 10px">
                                            <?= $model->client->fio ?>, <?= date("H:i", strtotime($model->cr_date_time)) ?>
                                    </a>
                                </div>
                            </div>
                        <?php 
                            if ($index % 3 == 0) { // Har uchinchi elementdan keyin satrni yopish
                                echo '</div>';
                            }
                            $index++; // Hisoblagichni oshirish
                        } 
                        // Agar oxirgi satr yopilmagan bo'lsa, uni yopish kerak
                        if (($index - 1) % 3 != 0) {
                            echo '</div>';
                        }
                    ?>

                    <table class="table">   
                            <!--<tr>-->
                            <!--    <th nowrap style="text-align: left; color:#474ba0">Jami to'langan summa dollarda ($):</th>-->
                            <!--    <td ><b style="color:#474ba0">< ?= Yii::$app->formatter->asDecimal($sum_dollars,2) ?></b></td>-->
                            <!--</tr>-->
                             <tr>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa dollarda ($):</th>
                                <td   ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_transferss,2)?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa so'mda:</th>
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_soms,2) ?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; color:#474ba0">To'langan summa kartada:</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_carts, 2)?></b></td>
                            </tr>
                           
                             <tr>
                                <td  style="background-color:#edb0b0a1;"><b style="color:black">Jami to'langan summa dollarda ($):</b></td>
                                <td style="background-color:#edb0b0a1;"><b style="color:black"><?= Yii::$app->formatter->asDecimal($sum_dollars,2) ?></b></td>
                            </tr>                            
                        </table>
                </div>
            </div>
            <!-- end panel-body -->
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading" style="background:#d37373">
                <div class="panel-heading-btn">
                    <!-- <a href="/order-account-history/export" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a> -->
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') ?> sanadagi qarz to'lagan mijozlar</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <div class="row row-space-10">
                <?php 
                    $index = 1; // Hisoblagichni boshlash
                    foreach ($debtRepayments as $model) { 
                        if (($index - 1) % 3 == 0) { // Har uchta elementdan keyin yangi satr ochish
                            echo '<div class="row">';
                        }
                    ?>
                        <div class="col-md-4"> <!-- 4 dan foydalanamiz, chunki 12/3 = 4, ya'ni 3 ustun bo'ladi -->
                            <div class="alert alert-warning show m-b-10">
                            <b style="color:red"><?= $index ?>.</b> <i class="fa fa-user fa-2x" style="font-size: 16px;"></i>
                                <a href="<?= Url::to(['/debt-repayment/print-debt', 'id' => $model->id]) ?>" target="_blank" class="alert-link" style="font-size: 14px; margin-left: 10px">
                                     <?= $model->client->fio ?>, <b style="color:green">To'langan qarz:</b>
                                    <b style="color:red">
                                        <?= Yii::$app->formatter->asDecimal($model->all_summ_dollar, 2) ?> $
                                    </b>
                                </a>
                            </div>
                        </div>
                    <?php 
                        if ($index % 3 == 0) { // Har uchinchi elementdan keyin satrni yopish
                            echo '</div>';
                        }
                        $index++; // Hisoblagichni oshirish
                    } 
                    // Agar oxirgi satr yopilmagan bo'lsa, uni yopish
                    if (($index - 1) % 3 != 0) {
                        echo '</div>';
                    }
                    ?>

                    <table class="table">   
                            <!--<tr>-->
                            <!--    <th nowrap style="text-align: left; color:#474ba0">Jami To'langan qarz summa dollarda ($):</th>-->
                            <!--    <td ><b style="color:#474ba0">< ?= Yii::$app->formatter->asDecimal($debt_sum_dollars,2) ?></b></td>-->
                            <!--</tr>-->
                            <tr>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan qarz dollarda ($):</th>
                                <td   ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_transferss,2)?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan qarz so'mda:</th>
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_soms,2) ?> </b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; color:#474ba0">To'langan qarz kartada:</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_carts, 2)?></b></td>
                            </tr>
                            
                            <tr>
                                <td  style="background-color:#edb0b0a1;"><b style="color:black">Jami to'langan qarz dollarda ($):</b></td>
                                <td style="background-color:#edb0b0a1;"><b style="color:black"><?= Yii::$app->formatter->asDecimal($debt_sum_dollars,2) ?></b></td>
                            </tr>
                    </table>
                </div>
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
// Select2 tanlovini boshqarish
$('#searchSelect').on('change', function() {
    const selectedClient = $(this).val()?.toLowerCase() || ''; // Tanlangan qiymatni olish (kichik harflar)
    const clients = document.querySelectorAll(".client-item");

    clients.forEach(client => {
        const name = client.getAttribute("data-name");
        if (selectedClient === '' || name.includes(selectedClient)) {
            client.style.display = "block";
        } else {
            client.style.display = "none";
        }
    });
});
JS
);
?>