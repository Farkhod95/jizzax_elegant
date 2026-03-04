<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Prices */
?>
<div class="prices-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'price',
            'warehouse_id',
        ],
    ]) ?>

</div>
