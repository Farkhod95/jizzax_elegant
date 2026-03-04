<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Consignor */
?>
<div class="consignor-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'phone',
            'address:ntext',
        ],
    ]) ?>

</div>
