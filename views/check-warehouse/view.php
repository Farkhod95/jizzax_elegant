<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CheckWarehouse */
?>
<div class="check-warehouse-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'check_id',
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
