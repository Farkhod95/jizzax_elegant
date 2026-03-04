<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BrandsSize */
?>
<div class="brands-size-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'size',
            'brand_id',
            'product_category_id',
        ],
    ]) ?>

</div>
