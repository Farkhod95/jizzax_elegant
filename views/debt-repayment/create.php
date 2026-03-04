<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DebtRepayment */

?>
<div class="debt-repayment-create">
    <?= $this->render('_form', [
        'model' => $model,
        'total_debt' => $total_debt,
        'orderAccount' => $orderAccount,
    ]) ?>
</div>
