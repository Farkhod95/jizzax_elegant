<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
use yii\helpers\Url;
use app\models\MyTotalDebt;

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
                    <a href="/sklad/import" class="btn btn-xs  btn-info"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
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
                <div class="row row-space-10">
                <?php 
                        $index = 1; // Hisoblagichni boshlash
                        foreach ($orders as $model) { 
                            if (($index - 1) % 3 == 0) { // Har uchta elementdan keyin yangi satr ochish
                                echo '<div class="row">';
                            }
                        ?>
                            <div class="col-md-4"> <!-- 4 dan foydalanyapmiz, chunki 12/3 = 4, ya'ni 3 ustun bo'ladi -->
                                <div class="alert alert-info show m-b-10">
                                <b style="color:red"><?= $index ?> </b>. <i class="fa fa-user fa-2x" style="font-size: 16px;"></i>
                                    <a href="<?=Url::toRoute(['sklad/import-view', 'id'=> $model->id, 'cr_date' => $model->cr_date])?>" class="alert-link" style="font-size: 14px; margin-left: 10px">
                                         <?= $model->consignor0->name ?>, <?= date("H:i", strtotime($model->cr_date_time)) ?>
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
                            <tr>
                                <th nowrap style="text-align: left; color:#474ba0">To'lanishi kerak summa dollarda ($):</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($given_sum_dollars??0,2) ?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left;color:#474ba0">To'langan summa ($):</th>
                                <td  ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_dollars??0,2) ?></b></td>
                            </tr>
                            <tr>
                                <th nowrap style="text-align: left; color:#474ba0">Chegirma ($):</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($discount_amounts, 2)?></b></td>
                            </tr>
                            <tr>                            
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
                        $myTotalDebt = MyTotalDebt::find()->where(['id' => $model->my_total_debt_id])->one();
                    ?>
                        <div class="col-md-4"> <!-- 4 dan foydalanamiz, chunki 12/3 = 4, ya'ni 3 ustun bo'ladi -->
                            <div class="alert alert-warning show m-b-10">
                            <b style="color:red"><?= $index ?>.</b> <i class="fa fa-user fa-2x" style="font-size: 16px;"></i>
                                <a href="<?= Url::to(['my-total-debt/index']) ?>" target="_blank" class="alert-link" style="font-size: 14px; margin-left: 10px">
                                     <?= $myTotalDebt->consignor->name ?>, <b style="color:green">To'langan qarzlarim:</b>
                                    <b style="color:red">
                                        <?= Yii::$app->formatter->asDecimal($deb_all_summ_dollars??0, 2) ?> $
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
                            <tr>
                                <th nowrap style="text-align: left; color:#474ba0">To'langan qarzim summa dollarda ($):</th>
                                <td ><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($deb_all_summ_dollars??0,2) ?></b></td>
                            </tr>
                    </table>
                </div>
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>