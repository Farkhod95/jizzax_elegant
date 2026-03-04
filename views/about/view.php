<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\About */
?>
<div class="about-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nomer_nakladnoy',
            'postavshik',
            'phone',
            'dostavshik',
            't_p',
        ],
    ]) ?>

</div>
