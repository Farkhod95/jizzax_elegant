<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BrandCategoriesImg */
?>
<div class="brand-categories-img-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'type',
            'brand_id',
            'product_category_id',
            'file_path',
            'created_at',
        ],
    ]) ?>

</div>
