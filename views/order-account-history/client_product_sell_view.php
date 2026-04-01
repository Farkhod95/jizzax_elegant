<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $groupedOrders */
/** @var app\models\Brands $brand */
/** @var app\models\ProductCategory $productCategory */
/** @var int $brand_id */
/** @var int $product_category_id */
/** @var string $size */
/** @var int $type */
/** @var string $type_name */
/** @var string $cr_date */
/** @var string $start_date */
/** @var string $end_date */
/** @var int $totalHighlightedCount */

$this->title = Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') . ' sanadagi mahsulot sotuv tarixi';
?>

<div class="row">
    <div class="col-xl-12">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="<?= Url::toRoute(['order-account-history/client-product-sell']) ?>"
                       onclick="event.preventDefault(); document.getElementById('back-form-product-sell').submit();"
                       class="btn btn-xs btn-info">
                        <i class="fa fa-reply"></i> Orqaga qaytish
                    </a>

                    <?= Html::beginForm(['/order-account-history/client-product-sell'], 'post', [
                        'id' => 'back-form-product-sell',
                        'style' => 'display:none;',
                    ]) ?>
                        <input type="hidden" name="brand_id" value="<?= (int)$brand_id ?>">
                        <input type="hidden" name="product_category_id" value="<?= (int)$product_category_id ?>">
                        <input type="hidden" name="size" value="<?= Html::encode($size) ?>">
                        <input type="hidden" name="type" value="<?= (int)$type ?>">
                        <input type="hidden" name="start_date" value="<?= Html::encode($start_date) ?>">
                        <input type="hidden" name="end_date" value="<?= Html::encode($end_date) ?>">
                    <?= Html::endForm() ?>
                </div>

                <h4 class="panel-title">
                    <?= Yii::$app->formatter->asDate($cr_date, 'php:d.m.Y') ?>
                    sanadagi sotuvlar
                </h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <div class="alert alert-info" style="font-size:16px;">
                        <b>Qidirilgan filter:</b>
                        <span style="background:#ffe082; color:#000; padding:4px 10px; border-radius:4px;">
                            <?= Html::encode($brand->name) ?>
                            - <?= Html::encode($productCategory->name) ?>
                            <?php if ($size !== ''): ?>
                                - Size: <?= Html::encode($size) ?>
                            <?php endif; ?>
                            <?php if ($type_name): ?>
                                - Type: <?= Html::encode($type_name) ?>
                            <?php endif; ?>
                        </span>
                        &nbsp;&nbsp;
                        <b>Jami sotilgan soni:</b>
                        <span style="color:#d9534f; font-size:18px;"><?= (int)$totalHighlightedCount ?></span>
                    </div>

                    <?php if (empty($groupedOrders)): ?>
                        <div class="alert alert-warning">Bu sanada ushbu filter bo‘yicha buyurtma topilmadi.</div>
                    <?php else: ?>
                        <?php $globalIndex = 1; ?>

                        <?php foreach ($groupedOrders as $group): ?>
                            <?php
                            $order = $group['order'];
                            $clientName = $order->client ? $order->client->fio : 'Noma’lum mijoz';
                            ?>
                            <div class="panel panel-default" style="border:1px solid #d9edf7; margin-bottom:20px;">
                                <div class="panel-heading" style="background:#8ec3d6; color:#204d74;">
                                    <b><?= $globalIndex ?>.</b>
                                    <i class="fa fa-user"></i>
                                    <b><?= Html::encode($clientName) ?></b>
                                    <?php if (!empty($order->cr_date_time)): ?>
                                        , <?= date('H:i', strtotime($order->cr_date_time)) ?>
                                    <?php endif; ?>
                                    <span style="float:right;">
                                        <b>Qidirilgan mahsulot soni:</b>
                                        <span style="color:#d9534f; font-size:16px;"><?= (int)$group['highlight_count'] ?></span>
                                    </span>
                                </div>

                                <div class="panel-body">
                                    <?php if (Yii::$app->user->identity->permission == 1 || Yii::$app->user->identity->permission == 2): ?>
                                        <table class="table table-bordered" style="margin-bottom:15px;">
                                            <tr>
                                                <th style="color:#f59c1a;">Dollar kursi</th>
                                                <td><b style="color:#f59c1a"><?= Html::encode($order->exchange_rate) ?></b></td>
                                                <th style="color:#474ba0;">To‘langan summa $</th>
                                                <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($order->sum_dollar, 2) ?></b></td>
                                            </tr>
                                            <tr>
                                                <th style="color:#f59c1a;">Jami to‘lanadigan summa ($)</th>
                                                <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($order->all_product_sum, 2) ?></b></td>
                                                <th style="color:#474ba0;">To‘langan summa so‘mda</th>
                                                <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($order->sum_som, 2) ?></b></td>
                                            </tr>
                                            <tr>
                                                <th style="color:#f59c1a;">To‘langan summa ($)</th>
                                                <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($order->all_summ_dollar, 2) ?></b></td>
                                                <th style="color:#474ba0;">To‘langan summa kartada</th>
                                                <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($order->sum_cart, 2) ?></b></td>
                                            </tr>
                                            <tr>
                                                <th style="color:#f59c1a;">Chegirma ($)</th>
                                                <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($order->discount_amount, 2) ?></b></td>
                                                <th style="color:#474ba0;">To‘langan summa transferda</th>
                                                <td><b style="color:#474ba0"><?= Yii::$app->formatter->asDecimal($order->sum_transfers, 2) ?></b></td>
                                            </tr>
                                            <tr>
                                                <th style="color:#f59c1a;">Qolgan qarz ($)</th>
                                                <td><b style="color:#f59c1a"><?= Yii::$app->formatter->asDecimal($order->total_debt, 2) ?></b></td>
                                                <th></th>
                                                <td></td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:60px;">#</th>
                                                <th>Model</th>
                                                <th>Nomi</th>
                                                <th>O'lchami</th>
                                                <th>Type</th>
                                                <th>Soni</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; ?>
                                            <?php foreach ($group['items'] as $row): ?>
                                                <?php
                                                $item = $row['model'];
                                                $isHighlight = $row['highlight'];
                                                ?>
                                                <tr style="<?= $isHighlight ? 'background:#ffe082; font-weight:bold;' : '' ?>">
                                                    <td><?= $i ?></td>
                                                    <td><?= Html::encode($item->brand ? $item->brand->name : '') ?></td>
                                                    <td>
                                                        <?= Html::encode($item->productCategory ? $item->productCategory->name : '') ?>
                                                        <?php if ($isHighlight): ?>
                                                            <span class="label label-danger" style="margin-left:8px;">Qidirilgan mahsulot</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= Html::encode($item->size) ?></td>
                                                    <td><?= Html::encode($item->getTypeView($item->type)) ?></td>
                                                    <td><?= (int)$item->count ?></td>
                                                </tr>
                                                <?php $i++; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php $globalIndex++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>