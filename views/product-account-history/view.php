<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ProductAccountHistory */
?>
<div class="product-account-history-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_account_id',
            'order_account_history_id',
            'brand_id',
            'product_category_id',
            'size',
            'count',
            'type',
            'price',
            'real_price',
            'type_sklad_id',
            'profit',
            'cr_date',
            'created_by',
        ],
    ]) ?>

</div>
