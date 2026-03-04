<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProducts */
?>
<div class="order-products-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'brand_id',
            'product_category_id',
            'size',
            'count',
            'cr_date',
            'order_id',
        ],
    ]) ?>

</div>
