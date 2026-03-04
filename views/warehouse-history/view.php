<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\WarehouseHistory */
?>
<div class="warehouse-history-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'brand_id',
            'product_category_id',
            'size',
            'count',
            'cr_date',
            'created_by',
            'update_by',
        ],
    ]) ?>

</div>
