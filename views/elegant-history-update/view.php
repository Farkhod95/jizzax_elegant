<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ElegantHistoryUpdate */
?>
<div class="elegant-history-update-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'comment:ntext',
            'cr_date',
            'created_by',
        ],
    ]) ?>

</div>
