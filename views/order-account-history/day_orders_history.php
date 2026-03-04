<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Mahsulotlar ro\'yxati';
?>

<div class="row">
    <div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?= Url::to(['/order-account-history/day-orders']) ?>" class="btn btn-xs btn-info">
                        <i class="fa fa-reply"></i> Orqaga qaytish
                    </a>
                </div>

                <h4 class="panel-title">
                    <b style="color:#e7cbcb">
                        <?= Html::encode($start_date) ?> sanadan <?= Html::encode($end_date) ?> sanagacha bo'lgan vaqtdagi sotilgan tavarlar ro'yxati
                    </b>
                </h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th nowrap style="text-align:left;width:150px;color:#f59c1a">Jami to'lanadigan summa ($):</th>
                            <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($all_product_sum, 2) ?> $</b></td>
                            <td style="width:10px;"></td>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan summa transferda ($):</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_transfers, 2) ?> $</b></td>
                        </tr>

                        <tr>
                            <th nowrap style="text-align:left;width:150px;color:#f59c1a">Jami to'langan summa ($):</th>
                            <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($all_summ_dollar, 2) ?> $</b></td>
                            <td style="width:10px;"></td>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan summa so'mda:</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_som, 0) ?></b></td>
                        </tr>

                        <tr>
                            <th nowrap style="text-align:left;width:200px;color:#f59c1a">Chegirma ($):</th>
                            <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($discount_amount, 2) ?> $</b></td>
                            <td style="width:10px;"></td>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan summa kartada:</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($sum_cart, 0) ?></b></td>
                        </tr>

                        <tr>
                            <th nowrap style="text-align:left;width:150px;color:#f59c1a">To'lanmagan summa ($):</th>
                            <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($total_debt, 2) ?> $</b></td>
                            <td style="width:10px;"></td>
                            <th nowrap style="text-align:left;color:#474ba0">Qaytim:</th>
                            <td>
                                <b style="color:#474ba0">
                                    <?= Yii::$app->formatter->asDecimal($zdacha_sum, 0) ?>
                                    (<?= Yii::$app->formatter->asDecimal($zdacha_dollar, 2) ?> $)
                                </b>
                            </td>
                        </tr>
                    </table>

                    <h3><b>To'langan Qarzlar</b></h3>
                    <hr>

                    <div class="row">
                        <?php foreach ($debtRepayments as $model): ?>
                            <div class="col-md-3">
                                <div class="alert alert-warning show m-b-10">
                                    <i class="fa fa-user fa-2x" style="font-size:16px;"></i>

                                    <?php
                                        $fio = $model->client ? $model->client->fio : null;
                                        $paidUsd = (float)$model->summ_dollar
                                            + ((float)$model->sum_som / (float)$exchangeRate->dollar)
                                            + ((float)$model->summ_cart / (float)$exchangeRate->dollar)
                                            + ((float)$model->sum_transfers / (float)$exchangeRate->dollar);
                                    ?>

                                    <a class="alert-link" style="font-size:10px;margin-left:10px">
                                        <?= Html::encode($fio ? $fio : ('Client topilmadi (ID: '.$model->client_id.')')) ?>,
                                        <?= Html::encode($model->date) ?>
                                        <b style="color:green"><br>To'langan:</b>
                                        <b style="color:red"><?= Yii::$app->formatter->asDecimal($paidUsd, 2) ?> $</b>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <table class="table">
                        <tr>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan qarz summa ($):</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_transferss, 2) ?></b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align:left;color:#474ba0">Jami qaytim summa:</th>
                            <td><b style="color:#474ba0"> <?= Yii::$app->formatter->asDecimal($debt_sum_all_zdacha_sum, 0) ?>  (<?= Yii::$app->formatter->asDecimal($debt_sum_all_zdacha_dollar, 0) ?> $)</b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan qarz summa so'mda:</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_soms, 0) ?> - <?= Yii::$app->formatter->asDecimal($debt_sum_all_zdacha_sum, 0) ?> = </b><b style="color:#2b4df5"><?= Yii::$app->formatter->asDecimal($debt_sum_soms_all, 0) ?></b></td>
                        </tr>
                        <tr>
                            <th nowrap style="text-align:left;color:#474ba0">To'langan qarz summa kartada:</th>
                            <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($debt_sum_carts, 0) ?></b></td>
                        </tr>

                        <tr>
                            <td style="background-color:#edb0b0a1;"><b style="color:black">Jami ($):</b></td>
                            <td style="background-color:#edb0b0a1;">
                                <b style="color:black"><?= Yii::$app->formatter->asDecimal($rep_total_debt, 2) ?></b>
                            </td>
                        </tr>
                    </table>

                    <!-- Agar sizga sotilgan mahsulotlarni ham qayta yoqish kerak bo'lsa,
                         quyidagi blokni qaytarib yoqamiz (oldin sizda kommentda edi). -->

                </div>
            </div>
        </div>
    </div>
</div>
