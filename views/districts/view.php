<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Districts */
?>
<div class="districts-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'region_id',
            'key',
        ],
    ]) ?>

</div>
