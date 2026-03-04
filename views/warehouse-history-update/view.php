<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
?>
<div class="warehouse-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'brand_id',
                'value' => function ($data) {
                    return $data->brand->name;
                },
            ],
            [
                'attribute'=>'product_category_id',
                'width' => '250px',
                'value' => function ($data) {
                    return $data->productCategory->name;
                },
            ],
            'size',
            'count',
            'count_update',
            'cr_date',
            [
                'attribute'=>'created_by',
                'width' => '250px',
                'value' => function ($data) {
                    if ($data->created_by) {
                        return $data->createdBy->getFio();
                    }
                },
            ],
            [
                'attribute'=>'update_by',
                'width' => '250px',
                'value' => function ($data) {
                    if ($data->update_by) {
                        return $data->updateBy->getFio();
                    }
                },
            ],
        ],
    ]) ?>

</div>
