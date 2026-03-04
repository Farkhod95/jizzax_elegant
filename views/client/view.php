<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
?>
<div class="client-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fio',
            'phone',
            'region_id',
            'district_id',
            'address',
        ],
    ]) ?>

</div>
