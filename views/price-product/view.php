<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PriceProduct */
?>
<div class="price-product-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'brand_id',
            'product_category_id',
            'real_price',
            'cr_date',
        ],
    ]) ?>

</div>
